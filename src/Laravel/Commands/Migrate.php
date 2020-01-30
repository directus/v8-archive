<?php

declare(strict_types=1);

namespace Directus\Laravel\Commands;

use Illuminate\Console\Command;

/**
 * Migration command.
 */
class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'directus:migrate {project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs directus migrations on a project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    }
}
