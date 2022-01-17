<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mergeExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'example:merge {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge CSV {filename} data to Customers table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Начало импорта');

        app('FileMerger')->filename = $this->argument('filename');

        if (app('FileMerger')->merge()) {
            $this->info(app('FileMerger')->lastResult);
        } else {
            $this->error(app('FileMerger')->lastResult);
        }

        $this->info('Импорт завершен');

    }
}
