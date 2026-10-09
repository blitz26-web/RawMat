<?php

namespace App\Notifications;

use App\Models\Material;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlertNotification extends Notification
{
    use Queueable;

    public Material $material;

    public function __construct(Material $material)
    {
        $this->material = $material;
    }

    public function via(object $notifiable): array
    {
        // Menyimpan notifikasi ke Database dan Email
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject("⚠️ Peringatan Stok Kritis: {$this->material->name}")
            ->greeting("Halo Admin Production,")
            ->line("Bahan baku **{$this->material->name}** ({$this->material->code}) telah mencapai batas kritis di bawah Safety Stock.")
            ->line("Sisa Stok Saat Ini: **{$this->material->current_stock} {$this->material->unit}**")
            ->line("Batas Safety Stock: **{$this->material->safety_stock} {$this->material->unit}**")
            ->action('Cek Stok Gudang', url('/materials'))
            ->line('Segera buat Purchase Order (PO) ke supplier untuk menghindari keterlambatan alur produksi!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'material_id'   => $this->material->id,
            'code'          => $this->material->code,
            'name'          => $this->material->name,
            'current_stock' => $this->material->current_stock,
            'unit'          => $this->material->unit,
            'message'       => "Stok {$this->material->name} ({$this->material->current_stock} {$this->material->unit}) berada di bawah Safety Stock!",
        ];
    }
}