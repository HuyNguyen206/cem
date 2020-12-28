<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;
use Mail;

class SendCsatEmailWeekFullZone extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(env('APP_ENV') != 'local'){
            $mail = [
                'ftel.cem@fpt.com.vn',
                'hact@fpt.com.vn',
                'anhhv@fpt.com.vn',
                'thangch@fpt.com.vn',
                'huongvm@fpt.com.vn',
                'kienht@fpt.com.vn',
                'linh@fpt.com.vn',
                'tuva@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'haitt3@fpt.com.vn',
                'phuongtn@fpt.com.vn',
                'tuanpa@fpt.com.vn',
                'ducna7@fpt.com.vn',
                'hapt8@fpt.com.vn',
                'khanhnn@fpt.com.vn',
                'tuanpt@fpt.com.vn',
                'hoangdm@fpt.com.vn',
                'phunp@fpt.com.vn',
                'ngatt7@fpt.com.vn',
                'anhdv4@fpt.com.vn',
                'sonth@fpt.com.vn',
                'huyvd@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'hoainam@fpt.com.vn',
                'tritt@fpt.com.vn',
                'dunght@fpt.com.vn',
            ];
        }else{
            $mail = [
                'huydp2@fpt.com.vn',
            ];
        }

        $cc = [

        ];

        $redisKey = 'dataExcelCSATWeekFullZone';
        $data = json_decode(Redis::get($redisKey),1);           
        if ($this->attempts() < 2) {
            if (!empty($data)) {
                $dayStart = strtotime($data['from_date']);
                $dayEnd = strtotime($data['to_date']);
                $file = 'exports/' . 'ToanQuoc-CSAT12Internet&Truyenhinh-' . date('Ymd',$dayStart) . '-' . date('Ymd',$dayEnd) . '.xls';
                $path = storage_path($file);
                Mail::send('emails.templateEmailWeekZone', $data, function ($message) use ($path, $mail, $dayStart, $dayEnd, $cc) {
                    $message->from('rad.support@fpt.com.vn', 'Support');
                    $message->to($mail);
                    $message->attach($path);
                    $message->subject('[CEM – Customer Voice] Tổng hợp CSAT 1, 2 Chất lượng dịch vụ Internet và Truyền hình toàn quốc từ ngày ' . date('d/m/Y',$dayStart) . ' đến ngày ' . date('d/m/Y',$dayEnd));
                });
            }
        }
    }
}
