<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\Order;

class UpdateRankingsCommand extends Command
{
    public function __construct(
        private UserService $userService
    ) {
        parent::__construct();
    }

    protected $signature = 'update:rankings';

    public function handle()
    {
        $users = collect($this->userService->get('users'));

        $ambassadors = $users->filter(fn($user) => $user['is_admin'] === 0);

        $bar = $this->output->createProgressBar($ambassadors->count());

        $bar->start();

        $ambassadors->each(function ($user) use ($bar) {
            $orders = Order::where('user_id', $user->id)->get();
            $revenue = $orders->sum(fn(Order $order) => $order->total);

            Redis::zadd('rankings', (int)$revenue, $user->first_name . ' ' . $user->last_name);

            $bar->advance();
        });

        $bar->finish();
    }
}
