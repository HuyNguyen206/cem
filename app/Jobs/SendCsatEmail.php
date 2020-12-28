<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;
use Mail;

class SendCsatEmail extends Job implements ShouldQueue
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
                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',
                'haidt5@fpt.com.vn',
                'thuyttt@fpt.com.vn',

                'hunghd@fpt.com.vn',
                'hieutb@fpt.com.vn',

                'kienht@fpt.com.vn ',

                'quangnn10@fpt.com.vn',
                'binhlt6@fpt.com.vn',
                'anhnt8@fpt.com.vn',
                'hieupq@fpt.com.vn',
                'quynhltt@fpt.com.vn',

                'dungntp15@fpt.com.vn',

                'tuanha@fpt.com.vn',
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
                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',
                'haidt5@fpt.com.vn',
                'thuyttt@fpt.com.vn',

                'hunghd@fpt.com.vn',
                'hieutb@fpt.com.vn',

                'kienht@fpt.com.vn ',

                'dungntp15@fpt.com.vn',

                'tuanha@fpt.com.vn',
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
                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',
                'haidt5@fpt.com.vn',
                'thuyttt@fpt.com.vn',

                'hunghd@fpt.com.vn',
                'hieutb@fpt.com.vn',

                'kienht@fpt.com.vn ',

                'dungntp15@fpt.com.vn',

                'tuanha@fpt.com.vn',
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
                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',
                'haidt5@fpt.com.vn',
                'thuyttt@fpt.com.vn',

                'hunghd@fpt.com.vn',
                'hieutb@fpt.com.vn',

                'kienht@fpt.com.vn ',

                'cuongnh.hcm@fpt.com.vn',
                'baopnh@fpt.com.vn',
                'chidth@fpt.com.vn',

                'cuongnv37@fpt.com.vn',

                'dungntp15@fpt.com.vn',

                'tuanha@fpt.com.vn',
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
                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',
                'haidt5@fpt.com.vn',
                'thuyttt@fpt.com.vn',

                'cuongnh.hcm@fpt.com.vn',
                'baopnh@fpt.com.vn',
                'chidth@fpt.com.vn',

                'linh@fpt.com.vn',

                'dungntp15@fpt.com.vn',

                'tuanha@fpt.com.vn',
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
                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',
                'haidt5@fpt.com.vn',
                'thuyttt@fpt.com.vn',

                'cuongnh.hcm@fpt.com.vn',
                'baopnh@fpt.com.vn',
                'chidth@fpt.com.vn',

                'linh@fpt.com.vn',

                'dungntp15@fpt.com.vn',

                'tuanha@fpt.com.vn',
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
                'thiennx@fpt.com.vn',
                'trungnp2@fpt.com.vn',
                'haint4@fpt.com.vn',
                'haidt5@fpt.com.vn',
                'thuyttt@fpt.com.vn',

                'cuongnh.hcm@fpt.com.vn',
                'baopnh@fpt.com.vn',
                'chidth@fpt.com.vn',

                'linh@fpt.com.vn',

                'dungntp15@fpt.com.vn',

                'tuanha@fpt.com.vn',
                'thovt4@fpt.com.vn',
                'hoadtt11@fpt.com.vn',
                'trangnn4@fpt.com.vn',
                'huydp2@fpt.com.vn',
            ]
        ];

        $redisKey = 'dataExcelCSAT'.$zone;
        $data = json_decode(Redis::get($redisKey),1);

        if ($this->attempts() < 2) {
            if(!empty($data)){
                $dayStart = strtotime($data['from_date']);
                $file = 'exports/' . 'Vung' . $zone . '-CSAT12Internet&Truyenhinh-' . date('Ymd',$dayStart) . '.xls';
                $path = storage_path($file);
                Mail::send('emails.templateEmail', $data, function ($message) use($path, $mail, $zone, $dayStart, $cc){
                    $message->from('rad.support@fpt.com.vn', 'Support');
                    $message->to($mail);
                    if(env('APP_ENV') != 'local'){
                        $message->cc($cc[$zone]);
                    }
                    $message->attach($path);
                    $message->subject('[CEM – Customer Voice] Tổng hợp CSAT 1, 2 Chất lượng dịch vụ Internet và Truyền hình vùng '.$zone.' ngày '.date('d/m/Y',$dayStart));
                });

            }
        }
    }
}
