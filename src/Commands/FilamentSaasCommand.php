<?php

namespace Mhmadahmd\FilamentSaas\Commands;

use Illuminate\Console\Command;

class FilamentSaasCommand extends Command
{
    public $signature = 'filament-saas';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
