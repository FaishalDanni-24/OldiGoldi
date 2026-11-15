<?php

namespace App\Notifications;

use App\Models\Shipments\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentDeliveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Shipment $shipment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pesanan Telah Dikirim/Diterima')
            ->line('Pengiriman untuk pesanan #' . $this->shipment->order?->order_number . ' telah selesai.')
            ->line('Nomor resi: ' . $this->shipment->tracking_number);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'shipment_id' => $this->shipment->shipment_id,
            'order_id' => $this->shipment->order_id,
            'tracking_number' => $this->shipment->tracking_number,
        ];
    }
}
