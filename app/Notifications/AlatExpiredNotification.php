<?php

namespace App\Notifications;

use App\Models\Alat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AlatExpiredNotification extends Notification
{
    use Queueable;

    public Alat $alat;
    public string $status;

    /**
     * Hanya butuh 2 parameter: alat dan status
     */
    public function __construct(Alat $alat, string $status)
    {
        $this->alat = $alat;
        $this->status = $status; // 'expired' atau 'warning'
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast']; // atau ['database'] saja
    }

    public function toDatabase($notifiable): array
    {
        $nama = $this->alat->nama_alat;
        $waktu = $this->alat->masa_berlaku?->format('d M Y') ?? '-';

        $message = match($this->status) {
            'expired' => "Masa berlaku alat **{$nama}** sudah berakhir ({$waktu}).",
            'warning' => "Masa berlaku alat **{$nama}** akan berakhir ({$waktu}).",
            default   => "Notifikasi alat."
        };

        return [
            'type'       => 'alat_kalibrasi',
            'alat_id'    => $this->alat->id,
            'nama_alat'  => $this->alat->nama_alat,
            'status'     => $this->status,
            'message'    => $message,
            'action_url' => route('alat.show', $this->alat->hashid),
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'alat_id'   => $this->alat->id,
            'nama_alat' => $this->alat->nama_alat,
            'status'    => $this->status,
            'message'   => $this->toDatabase($notifiable)['message'],
        ]);
    }
}
