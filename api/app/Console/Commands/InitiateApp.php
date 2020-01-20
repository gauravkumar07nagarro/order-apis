<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InitiateApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initiate:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command will be used to setup your application';

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

        Artisan::call("key:generate");
        Artisan::call("config:clear");
        Artisan::call("cache:clear");
        Artisan::call("route:clear");
        Artisan::call("view:clear");
        Artisan::call("l5-swagger:generate");
    }
}
