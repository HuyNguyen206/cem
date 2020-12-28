<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Process\Process;

class Kernel extends ConsoleKernel {

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
//        Commands\BuildDataToSendMailCSAT12::class,
//        Commands\BuildDataToSendMailCSAT12Week::class,
//        Commands\SendMailCSAT12::class,
//        Commands\SendMailCSAT12Week::class,
//        Commands\FixMissSurveyReport::class,
//        Commands\summaryData::class,
//        Commands\SendMailRemindCUSCommand::class,
//         Commands\summaryDataCron::class,
//        Commands\summaryDataTotal::class,
//        Commands\summaryUpdate::class,
//        Commands\updateInvalidCaseSurvey::class,
//        Commands\transactionGetCount::class,
//        Commands\formatDataFormulaSalaryTinPNC::class,
//		Commands\Test::class,
//        Commands\getAnonymousSurvey::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('queue:work --queue=emails')->cron('* * * * * *');
//        $schedule->command('queue:work --queue=mobile')->cron('* * * * * *');
//        $schedule->command('queue:work --queue=newPhone')->cron('* * * * * *');
//        $schedule->command('survey:getAnonymousSurvey')->cron('* * * * * *');
//        $schedule->command('queue:work --queue=emailsRemindCus')->cron('* * * * * *');
//
//        $schedule->command('survey:updateInvalidCase')->dailyAt('23:00');
//        $schedule->command('transaction:getCount')->dailyAt('00:30');
//        $schedule->command('summaryData:total')->dailyAt('00:10');
//        $schedule->command('formatData:salaryTinPNC')->dailyAt('00:10');
//        $schedule->command('mail:remindcus12send')->dailyAt('00:05');
//        $schedule->command('summaryData:Update')->cron('* * * * * *');
//        $schedule->command('summaryData:total')->cron('* * * * * *');
//        $schedule->command('formatData:salaryTinPNC')->everyFiveMinutes();


//        $schedule->command('mail:csat12build')->dailyAt('07:00')
//                ->when(function () {
//                    return date('l') != "Monday";
//                })
//                ->after(function () {
//                    $date = date('Y-m-d');
//                    $log = new Logger('csat12build');
//                    $log->pushHandler(new StreamHandler(storage_path("/logs/csat12build.log"), Logger::DEBUG));
//                    $log->addInfo('Run at ' . $date);
//                });
//        $schedule->command('mail:csat12send')->dailyAt('07:30')
//                ->when(function () {
//                    return date('l') != "Monday";
//                })
//                ->after(function () {
//                    $date = date('Y-m-d');
//                    $log = new Logger('csat12send');
//                    $log->pushHandler(new StreamHandler(storage_path("/logs/csat12send.log"), Logger::DEBUG));
//                    $log->addInfo('Run at ' . $date);
//                });
//
//        $schedule->command('mail:csat12weekbuild')->weekly()->mondays()->at('07:00')
//                ->after(function () {
//                    $date = date('Y-m-d');
//                    $log = new Logger('csat12weekbuild');
//                    $log->pushHandler(new StreamHandler(storage_path("/logs/csat12weekbuild.log"), Logger::DEBUG));
//                    $log->addInfo('Run at ' . $date);
//                });
//        $schedule->command('mail:csat12weeksend')->weekly()->mondays()->at('07:30')
//                ->after(function () {
//                    $date = date('Y-m-d');
//                    $log = new Logger('csat12weeksend');
//                    $log->pushHandler(new StreamHandler(storage_path("/logs/csat12weeksend.log"), Logger::DEBUG));
//                    $log->addInfo('Run at ' . $date);
//                });
    }

}
