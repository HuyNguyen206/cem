<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;
use Mail;

class SendCsatEmailWeek extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $mail;
    protected $zone;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mail, $zone)
    {
        $this->mail = $mail;
        $this->zone = $zone;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = $this->mail;
        $zone = $this->zone;

        $cc = [
            '1' => [
                'thangch@fpt.com.vn',
                'annp@fpt.com.vn',
                'tuandq@fpt.com.vn',
                'toannc@fpt.com.vn',
                'huongvg@fpt.com.vn',
                'thuannt@fpt.com.vn',
                'toannm@fpt.com.vn',
                'ngaph3@fpt.com.vn',

                'haitt3@fpt.com.vn',
                'sonth@fpt.com.vn',
                'huyvd@fpt.com.vn',
                'tuva@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'ngatt7@fpt.com.vn',
                'phuongtn@fpt.com.vn',
                'tuanpa@fpt.com.vn',
                'thuyttt@fpt.com.vn',
                'yenbui@fpt.com.vn',

                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',

                'hunghd@fpt.com.vn',
                'hieutb@fpt.com.vn',

                'linh@fpt.com.vn',

                'khoapv@fpt.com.vn',
                'phunghung@fpt.com.vn',
                'thangdd@fpt.com.vn',
                'hieulm2@fpt.com.vn',
                'trungl2@fpt.com.vn',
                'chuonglq@fpt.com.vn',
                'hoainam@fpt.com.vn',

                'quangnn10@fpt.com.vn',
                'binhlt6@fpt.com.vn',
                'anhnt8@fpt.com.vn',
                'hieupq@fpt.com.vn',
                'quynhltt@fpt.com.vn',

                'thovt4@fpt.com.vn',
                'hoadtt11@fpt.com.vn',
                'trangnn4@fpt.com.vn',
                'huydp2@fpt.com.vn',
            ],
            '2' => [
                'thangch@fpt.com.vn',
                'annp@fpt.com.vn',
                'tuandq@fpt.com.vn',
                'toannc@fpt.com.vn',
                'huongvg@fpt.com.vn',
                'thuannt@fpt.com.vn',
                'toannm@fpt.com.vn',
                'ngaph3@fpt.com.vn',

                'haitt3@fpt.com.vn',
                'sonth@fpt.com.vn',
                'huyvd@fpt.com.vn',
                'tuva@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'ngatt7@fpt.com.vn',
                'phuongtn@fpt.com.vn',
                'tuanpa@fpt.com.vn',
                'thuyttt@fpt.com.vn',
                'yenbui@fpt.com.vn',

                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',

                'hunghd@fpt.com.vn',
                'hieutb@fpt.com.vn',

                'linh@fpt.com.vn',

                'khoapv@fpt.com.vn',
                'phunghung@fpt.com.vn',
                'thangdd@fpt.com.vn',
                'hieulm2@fpt.com.vn',
                'trungl2@fpt.com.vn',
                'chuonglq@fpt.com.vn',
                'hoainam@fpt.com.vn',

                'thovt4@fpt.com.vn',
                'hoadtt11@fpt.com.vn',
                'trangnn4@fpt.com.vn',
                'huydp2@fpt.com.vn',
            ],
            '3' => [
                'thangch@fpt.com.vn',
                'annp@fpt.com.vn',
                'tuandq@fpt.com.vn',
                'toannc@fpt.com.vn',
                'huongvg@fpt.com.vn',
                'thuannt@fpt.com.vn',
                'toannm@fpt.com.vn',
                'ngaph3@fpt.com.vn',

                'haitt3@fpt.com.vn',
                'sonth@fpt.com.vn',
                'huyvd@fpt.com.vn',
                'tuva@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'ngatt7@fpt.com.vn',
                'phuongtn@fpt.com.vn',
                'tuanpa@fpt.com.vn',
                'thuyttt@fpt.com.vn',
                'yenbui@fpt.com.vn',

                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',

                'hunghd@fpt.com.vn',
                'hieutb@fpt.com.vn',

                'linh@fpt.com.vn',

                'khoapv@fpt.com.vn',
                'phunghung@fpt.com.vn',
                'thangdd@fpt.com.vn',
                'hieulm2@fpt.com.vn',
                'trungl2@fpt.com.vn',
                'chuonglq@fpt.com.vn',
                'hoainam@fpt.com.vn',

                'thovt4@fpt.com.vn',
                'hoadtt11@fpt.com.vn',
                'trangnn4@fpt.com.vn',
                'huydp2@fpt.com.vn',
            ],
            '4' => [
                'thangch@fpt.com.vn',
                'annp@fpt.com.vn',
                'tuandq@fpt.com.vn',
                'toannc@fpt.com.vn',
                'huongvg@fpt.com.vn',
                'thuannt@fpt.com.vn',
                'toannm@fpt.com.vn',
                'ngaph3@fpt.com.vn',

                'haitt3@fpt.com.vn',
                'sonth@fpt.com.vn',
                'huyvd@fpt.com.vn',
                'tuva@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'ngatt7@fpt.com.vn',
                'phuongtn@fpt.com.vn',
                'tuanpa@fpt.com.vn',
                'thuyttt@fpt.com.vn',
                'yenbui@fpt.com.vn',

                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',

                'nvanh@fpt.com.vn',
                'hunghd@fpt.com.vn',
                'hieutb@fpt.com.vn',

                'cuongnh.hcm@fpt.com.vn',
                'baopnh@fpt.com.vn',
                'chidth@fpt.com.vn',

                'linh@fpt.com.vn',

                'khoapv@fpt.com.vn',
                'phunghung@fpt.com.vn',
                'thangdd@fpt.com.vn',
                'hieulm2@fpt.com.vn',
                'trungl2@fpt.com.vn',
                'chuonglq@fpt.com.vn',
                'hoainam@fpt.com.vn',

                'thovt4@fpt.com.vn',
                'hoadtt11@fpt.com.vn',
                'trangnn4@fpt.com.vn',
                'huydp2@fpt.com.vn',
            ],
            '5' => [
                'thangch@fpt.com.vn',
                'annp@fpt.com.vn',
                'tuandq@fpt.com.vn',
                'toannc@fpt.com.vn',
                'huongvg@fpt.com.vn',
                'thuannt@fpt.com.vn',
                'toannm@fpt.com.vn',
                'ngaph3@fpt.com.vn',

                'haitt3@fpt.com.vn',
                'sonth@fpt.com.vn',
                'huyvd@fpt.com.vn',
                'tuva@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'ngatt7@fpt.com.vn',
                'phuongtn@fpt.com.vn',
                'tuanpa@fpt.com.vn',
                'thuyttt@fpt.com.vn',
                'yenbui@fpt.com.vn',

                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',

                'cuongnh.hcm@fpt.com.vn',
                'baopnh@fpt.com.vn',
                'chidth@fpt.com.vn',

                'linh@fpt.com.vn',

                'khoapv@fpt.com.vn',
                'phunghung@fpt.com.vn',
                'thangdd@fpt.com.vn',
                'hieulm2@fpt.com.vn',
                'trungl2@fpt.com.vn',
                'chuonglq@fpt.com.vn',
                'hoainam@fpt.com.vn',

                'thovt4@fpt.com.vn',
                'hoadtt11@fpt.com.vn',
                'trangnn4@fpt.com.vn',
                'huydp2@fpt.com.vn',
            ],
            '6' => [
                'thangch@fpt.com.vn',
                'annp@fpt.com.vn',
                'tuandq@fpt.com.vn',
                'toannc@fpt.com.vn',
                'huongvg@fpt.com.vn',
                'thuannt@fpt.com.vn',
                'toannm@fpt.com.vn',
                'ngaph3@fpt.com.vn',

                'haitt3@fpt.com.vn',
                'sonth@fpt.com.vn',
                'huyvd@fpt.com.vn',
                'tuva@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'ngatt7@fpt.com.vn',
                'phuongtn@fpt.com.vn',
                'tuanpa@fpt.com.vn',
                'thuyttt@fpt.com.vn',
                'yenbui@fpt.com.vn',

                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',

                'cuongnh.hcm@fpt.com.vn',
                'baopnh@fpt.com.vn',
                'chidth@fpt.com.vn',

                'linh@fpt.com.vn',

                'khoapv@fpt.com.vn',
                'phunghung@fpt.com.vn',
                'thangdd@fpt.com.vn',
                'hieulm2@fpt.com.vn',
                'trungl2@fpt.com.vn',
                'chuonglq@fpt.com.vn',
                'hoainam@fpt.com.vn',

                'thovt4@fpt.com.vn',
                'hoadtt11@fpt.com.vn',
                'trangnn4@fpt.com.vn',
                'huydp2@fpt.com.vn',
            ],
            '7' => [
                'thangch@fpt.com.vn',
                'annp@fpt.com.vn',
                'tuandq@fpt.com.vn',
                'toannc@fpt.com.vn',
                'huongvg@fpt.com.vn',
                'thuannt@fpt.com.vn',
                'toannm@fpt.com.vn',
                'ngaph3@fpt.com.vn',

                'haitt3@fpt.com.vn',
                'sonth@fpt.com.vn',
                'huyvd@fpt.com.vn',
                'tuva@fpt.com.vn',
                'phongpt@fpt.com.vn',
                'ngatt7@fpt.com.vn',
                'phuongtn@fpt.com.vn',
                'tuanpa@fpt.com.vn',
                'thuyttt@fpt.com.vn',
                'yenbui@fpt.com.vn',

                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',

                'cuongnh.hcm@fpt.com.vn',
                'baopnh@fpt.com.vn',
                'chidth@fpt.com.vn',

                'linh@fpt.com.vn',

                'khoapv@fpt.com.vn',
                'phunghung@fpt.com.vn',
                'thangdd@fpt.com.vn',
                'hieulm2@fpt.com.vn',
                'trungl2@fpt.com.vn',
                'chuonglq@fpt.com.vn',
                'hoainam@fpt.com.vn',

                'thovt4@fpt.com.vn',
                'hoadtt11@fpt.com.vn',
                'trangnn4@fpt.com.vn',
                'huydp2@fpt.com.vn',
            ]
        ];

        $redisKey = 'dataExcelCSATWeek'.$zone;
        $data = json_decode(Redis::get($redisKey),1);
        
        if ($this->attempts() < 2) {
            if (!empty($data)) {
                $dayStart = strtotime($data['from_date']);
                $dayEnd = strtotime($data['to_date']);
                $file = 'exports/' . 'Vung' . $zone . '-CSAT12Internet&Truyenhinh-' . date('Ymd',$dayStart) . '-' . date('Ymd',$dayEnd) . '.xls';
                $path = storage_path($file);
                Mail::send('emails.templateEmailWeek', $data, function ($message) use ($path, $mail, $zone, $dayStart, $dayEnd, $cc) {
                    $message->from('rad.support@fpt.com.vn', 'Support');
                    $message->to($mail);
                    if (env('APP_ENV') != 'local') {
                        $message->cc($cc[$zone]);
                    }
                    $message->attach($path);
                    $message->subject('[CEM – Customer Voice] Tổng hợp CSAT 1, 2 Chất lượng dịch vụ Internet và Truyền hình vùng ' . $zone . ' từ ngày ' . date('d/m/Y',$dayStart) . ' đến ngày ' . date('d/m/Y',$dayEnd));
                });
            }
        }
    }
}
