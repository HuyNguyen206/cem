<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendMailTotalRemindCUSJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $input;

    public function __construct($input) {
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
    */
    public function handle()
    {
        if ($this->attempts() >= 2) {
            return;
        }
        $mail = $this->input['mail'];
        $cc = $this->input['cc'];
        $subject = $this->input['subject'];
        $param = $this->input['param'];

        Mail::send('emails.sendMailTotalRemindCUS', ['param' => $param], function ($message) use ( $mail, $cc, $subject) {
            $message->from('rad.support@fpt.com.vn', 'Support');
            $message->to($mail);
            $message->cc($cc);
            $message->subject($subject);
        });
    }
}
