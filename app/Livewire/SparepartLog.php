<?php

namespace App\Livewire;

use App\Models\PengambilanSparepart;
use App\Models\Pengembalian;
use App\Models\Spareparts;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SparepartLog extends Component
{
    public $sparepartId;
    public $logs = [];

    public function mount($sparepartId)
{
    if (!Auth::user()->isAdminOrSuper()) {
        abort(403);
    }

    $this->sparepartId = $sparepartId;
    $this->loadLogs();
}

    public function loadLogs()
    {
        $pengambilan = PengambilanSparepart::with(['user', 'bagian'])
            ->where('spareparts_id', $this->sparepartId)
            ->get();

        $data = [];

        foreach ($pengambilan as $ambil) {
            $dikembalikan = $ambil->jumlah_dikembalikan ?? 0;
            $sisa = $ambil->jumlah - $dikembalikan;

            // 🔥 LOG PENGAMBILAN
            $data[] = [
                'tanggal' => $ambil->waktu_pengambilan,
                'user' => $ambil->user->name ?? '-',
                'bagian' => $ambil->bagian->nama ?? '-',
                'jenis' => 'Pengambilan',
                'jumlah' => $ambil->jumlah,
                'dikembalikan' => $dikembalikan,
                'sisa' => $sisa,
            ];

            // 🔥 LOG PENGEMBALIAN
            foreach ($ambil->pengembalian as $kembali) {
                $data[] = [
                    'tanggal' => $kembali->tanggal_kembali,
                    'user' => $kembali->user->name ?? '-',
                    'bagian' => $kembali->bagian->nama ?? '-',
                    'jenis' => 'Pengembalian',
                    'jumlah' => $kembali->jumlah_dikembalikan,
                    'dikembalikan' => '-',
                    'sisa' => '-',
                ];
            }
        }

        // SORT BY TANGGAL
        usort($data, fn($a, $b) => strtotime($b['tanggal']) - strtotime($a['tanggal']));

        $this->logs = $data;
    }

    public function render()
    {
        return view('livewire.sparepart-log');
    }
}
