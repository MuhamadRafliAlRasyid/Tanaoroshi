<?php

namespace App\Exports;

use App\Models\Spareparts;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;

class SparepartExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    public function collection()
    {
        Log::info('Collecting spareparts data');
        $data = Spareparts::all();
        Log::info('Collected ' . $data->count() . ' records');
        return $data;
    }

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
            $row->nama_part ?? 'N/A',
            $row->model ?? 'N/A',
            $row->merk ?? 'N/A',
            $row->jumlah_baru ?? 0,
            $row->jumlah_bekas ?? 0,
            $row->supplier ?? 'PART FROM PE',
            $row->patokan_harga ? 'Rp ' . number_format($row->patokan_harga, 2, ',', '.') : 'Rp 0.00',
            $row->total ? 'Rp ' . number_format($row->total, 2, ',', '.') : 'PART FROM PE',
            $row->ruk_no ?? '01-1',
            $row->purchase_date ? \Carbon\Carbon::parse($row->purchase_date)->format('d-M-y') : 'PART FROM PE',
            $row->delivery_date ? \Carbon\Carbon::parse($row->delivery_date)->format('d-M-y') : 'PART FROM PE',
            $row->po_number ?? 'PART FROM PE',
            $row->titik_pesanan ?? 0,
            $row->jumlah_pesanan ?? 0,
            $row->cek ? '〇' : '×',
            $row->pic ?? 'Jafar',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3498DB']],
                'alignment' => ['horizontal' => 'center'],
            ],
            'P' => ['alignment' => ['horizontal' => 'center']], // Kolom CEK rata tengah
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Auto-fit semua kolom berdasarkan konten terpanjang
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Tambahkan filter pada header (dari baris 1 sampai kolom terakhir)
                $sheet->setAutoFilter('A1:' . $highestColumn . $highestRow);
            },
        ];
    }
}
