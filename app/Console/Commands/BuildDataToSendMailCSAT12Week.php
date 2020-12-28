<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Cron\Cron;

class BuildDataToSendMailCSAT12Week extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:csat12weekbuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 7 excel file with data csat 1,2 in week and store the data to redis';

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
        $cron = new Cron();
        $cron->buildDataToSendCsatMailWeek();
    }
}
