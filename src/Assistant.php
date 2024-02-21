<?php

namespace Eele94\Assistant;

use Exception;
use OpenAI\Laravel\Facades\OpenAI;

class Assistant
{
    protected array $options = [
        'model' => 'gpt-3.5-turbo-0125',
    ];

    public function setOption(string $key, mixed $value): static
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function getOption(string $key): mixed
    {
        return $this->options[$key];
    }

    public function __construct(protected array $messages = [], array $options = [])
    {
        $this->options = array_merge($this->options, $options);
        $this->messages = $messages;
    }

    public function systemMessage(string $message): static
    {
        $this->addMessage($message, 'system');

        return $this;
    }

    protected function addMessage(string|array $message, string $role = 'user'): self
    {
        if (is_array($message)) {
            $this->messages[] = $message;
            return $this;
        }

        $this->messages[] = [
            'role' => $role,
            'content' => $message,
        ];

        return $this;
    }

    public function deleteHistory(): static
    {
        $this->messages = [];

        return $this;
    }

    public function messages()
    {
        return $this->messages;
    }

    public function send(?string $message = null, bool $speech = false): ?string
    {
        if ($message) {
            $this->addMessage($message);
        }

        $options = array_merge($this->options, [
            'messages' => $this->messages,
        ]);

        $response = OpenAI::chat()->create($options)->choices[0]->message->content;

        if ($response) {
            $this->addMessage($response, 'assistant');
        }

        return $speech ? $this->speech($response) : $response;
    }

    public function function(string $message, array|FunctionCall $function)
    {
        $function = is_array($function) ? $function : $function->serialize();
        $this->addMessage($message);
        $response = OpenAI::chat()->create([
            'model' => $this->getOption('model'), // 'gpt-3.5-turbo-0613',
            'messages' => $this->messages,
            'tools' => [
                [
                    'type' => 'function',
                    'function' => $function,
                ],
            ],
            // todo: https://platform.openai.com/docs/api-reference/chat/create#chat-create-tools
            // 'tool_choice' => 'auto',
            // 'tool_choice' => [
            //     'type' => 'function',
            //     'function' => $function->name,
            // ],
        ]);

        $arguments = $response->choices[0]->message->toolCalls[0]->function->arguments;

        if (app()->environment('local')) {
            logger('Ai Assistant Function call response', [
                'response' => $response,
            ]);
        }

        $arguments = json_decode($arguments, true);

        return $arguments;
    }

    public function speech(string $message): string
    {
        return OpenAI::audio()->speech([
            'model' => 'tts-1',
            'input' => $message,
            'voice' => 'alloy',
        ]);
    }

    public function reply(string $message): ?string
    {
        return $this->send($message);
    }

    public function vision(string $imageUrl, ?string $message = null): static
    {
        $this->setOption('model', 'gpt-4-vision-preview');
        $this->setOption('max_tokens', 2000);

        $content = [];
        if ($message) {
            $content[] = [
                'type' => 'text',
                'text' => $message,
            ];
        }
        $content[] = [
            'type' => 'image_url',
            'image_url' => [
                'url' => $imageUrl,
            ],
        ];

        $this->addMessage([
            'role' => 'user',
            'content' => $content,
        ]);

        return $this;
    }

    public function imageVariantion(string $message, string $imageUrl): string
    {
        $this->addMessage($message);

        // Retrieve the image content
        $imageContent = file_get_contents($imageUrl);
        throw_unless($imageContent, new Exception('Error: Unable to retrieve image content.'));

        // Create an image resource from the retrieved content
        $imageResource = imagecreatefromstring($imageContent);
        throw_unless($imageResource, new Exception('Error: Unable to create image resource.'));

        // Create a temporary file path with a .png extension
        $tempFilePath = tempnam(sys_get_temp_dir(), 'image').'.png';

        // Convert and save the image as PNG
        imagepng($imageResource, $tempFilePath);
        imagedestroy($imageResource);

        // Open the PNG file
        $fileResource = fopen($tempFilePath, 'r');

        $response = OpenAI::images()->variation([
            'image' => $fileResource,
            'prompt' => $message,
            'n' => 1,
            'size' => '1024x1024',
            'response_format' => 'url',
        ]);

        unlink($tempFilePath);
        $image = $response->data[0]->url;

        $this->addMessage($image, 'assistant');

        return $image;
    }

    public function visualize(string $description, array $options = []): array
    {
        $this->addMessage($description);

        $description = collect($this->messages)->where('role', 'user')->pluck('content')->implode(' ');

        $options = array_merge([
            'prompt' => $description,
            'model' => 'dall-e-3',
            // https://platform.openai.com/docs/guides/images/usage?context=node
            'n' => 1, // Number of images to generate is now only allowed to 1
        ], $options);

        $data = OpenAI::images()->create($options)->data;
        $urls = data_get($data, '*.url', []);

        // $this->addMessage(collect($urls)->join(','), 'assistant');

        return $urls;
    }
}
