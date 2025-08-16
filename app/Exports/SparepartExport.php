<?php

namespace App\Exports;

use App\Models\Spareparts;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SparepartExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        Log::info('Collecting spareparts data');
        $data = Spareparts::all();
        Log::info('Collected ' . $data->count() . ' records');
        return $data;
    }

    // public function headings(): array
    // {

    //     return ['Nama Part', 'Model', 'Merk', 'Jumlah Baru']; // Sesuaikan dengan kolom
    // }

    public function headings(): array
    {
        Log::info('Defining export headings');
        return [
            'No',
            'NAMA PART',
            'MODEL',
            'MERK',
            'JUMLAH BARU',
            'JUMLAH BEKAS',
            'SUPPLIER',
            'PATOKAN HARGA',
            'TOTAL',
            'RUK No',
            'PURCHASE DATE',
            'DELIVERY DATE',
            'PO NUMBER',
            'TITIK PESANAN',
            'JUMLAH PESANAN',
            'CEK',
            'PIC',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nama_part,
            $row->model,
            $row->merk,
            $row->jumlah_baru,
            $row->jumlah_bekas ?? 0,
            $row->supplier ?? 'PART FROM PE',
            $row->patokan_harga ? 'Rp ' . number_format($row->patokan_harga, 2, ',', '.') : 'Rp 0.00',
            $row->total ? 'Rp ' . number_format($row->total, 2, ',', '.') : 'Rp 0.00',
            $row->ruk_no ?? '01-1',
            $row->purchase_date ? \Carbon\Carbon::parse($row->purchase_date)->format('d-M-y') : '',
            $row->delivery_date ? \Carbon\Carbon::parse($row->delivery_date)->format('d-M-y') : '',
            $row->po_number ?? '',
            $row->titik_pesanan ?? 0,
            $row->jumlah_pesanan ?? 0,
            $row->cek ? '〇' : '×', // Sesuaikan dengan nilai boolean (1 = 〇, 0 = ×)
            $row->pic ?? 'Jafar',
        ];
    }
    // public function styles(Worksheet $sheet)
    // {
    //     return [
    //         1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center']], // Header tebal dan rata tengah
    //         'P' => ['alignment' => ['horizontal' => 'center']], // Kolom CEK (indeks 16, huruf P) rata tengah
    //     ];
    // }
}
