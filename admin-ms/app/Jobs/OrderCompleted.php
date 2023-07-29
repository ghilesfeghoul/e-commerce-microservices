<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public mixed $data;

    /**
     * Create a new job instance.
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
        Order::create([
            'id' => $this->data['id'],
            'transaction_id' => $this->data['transaction_id'],
            'user_id' => $this->data['user_id'],
            'code' => $this->data['code'],
            'ambassador_email' => $this->data['ambassador_email'],
            'first_name' => $this->data['first_name'],
            'last_name' => $this->data['last_name'],
            'email' => $this->data['email'],
            'address' => $this->data['address'],
            'city' => $this->data['city'],
            'country' => $this->data['country'],
            'zip' => $this->data['zip'],
            'complete' => $this->data['complete'],
            'created_at' => $this->data['created_at'],
            'updated_at' => $this->data['updated_at']
        ]);

        OrderItem::insert($this->data['order_items']);
    }
}
