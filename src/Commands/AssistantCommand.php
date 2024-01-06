<?php

namespace Eele94\Assistant\Commands;

use Illuminate\Console\Command;

class AssistantCommand extends Command
{
    public $signature = 'ai-assistant';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
