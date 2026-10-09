<?php

namespace App\Listeners;

use App\Events\MaterialStockDepleted;
use App\Models\User;
use App\Notifications\LowStockAlertNotification;
use Illuminate\Support\Facades\Notification;

class CheckSafetyStock
{
    public function handle(MaterialStockDepleted $event): void
    {
        // Kirim notifikasi ke semua user / Admin yang terdaftar di sistem
        $users = User::all();

        if ($users->isNotEmpty()) {
            Notification::send($users, new LowStockAlertNotification($event->material));
        }
    }
}