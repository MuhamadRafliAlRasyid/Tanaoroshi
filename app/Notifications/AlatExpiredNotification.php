<?php

namespace App\Notifications;

use App\Models\Alat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AlatExpiredNotification extends Notification
{
    use Queueable;

    protected $alat;
    protected $status;

    /**
     * status = expired | warning
     */
    public function __construct(Alat $alat, string $status)
    {
        $this->alat = $alat;
        $this->status = $status;
    }

    // 🔥 CHANNEL
    public function via($notifiable)
    {
        return ['database'];
    }

    // 🔥 SIMPAN KE TABLE notifications
    public function toDatabase($notifiable)
    {
        return [
            'alat_id' => $this->alat->id,
            'hashid' => $this->alat->hashid,

            'nama_alat' => $this->alat->nama_alat,
            'kode' => $this->alat->no_identitas ?? $this->alat->no_seri,

            'masa_berlaku' => $this->alat->masa_berlaku,
            'status' => $this->status, // expired / warning

            // 🔥 LINK KE HALAMAN DETAIL
            'action_url' => route('alat.show', $this->alat->hashid),
        ];
    }
}
