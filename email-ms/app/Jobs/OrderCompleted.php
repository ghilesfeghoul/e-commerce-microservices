<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public mixed $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->data;

        var_dump('[IN_PROGRESS] Sending email to admin...');
        \Mail::send('admin', ['order' => $order], function (Message $message) {
            $message->subject('An Order has been completed');
            $message->to('admin@admin.com');
        });
        var_dump('[DONE] Email sent to admin...');

        var_dump('[IN_PROGRESS] Sending email to ambassador...');
        \Mail::send('ambassador', ['order' => $order], function (Message $message) use ($order) {
            $message->subject('An Order has been completed');
            $message->to($order['ambassador_email']);
        });
        var_dump('[DONE] Email sent to ambassador...');
    }
}
