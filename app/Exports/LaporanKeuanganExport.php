<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border; // DITAMBAHKAN: Import class untuk Border
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LaporanKeuanganExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    // ... (property dan method __construct, collection, headings, map, columnFormats tidak berubah) ...
    protected $startDate;
    protected $endDate;
    protected $totalAmount;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->totalAmount = Transaksi::where('status_transaksi', 'success')
                                    ->whereBetween('updated_at', [$this->startDate, $this->endDate])
                                    ->sum(DB::raw('jumlah_bayar + biaya_admin'));
    }

    public function collection()
    {
        return Transaksi::with('tagihan.user')
                      ->where('status_transaksi', 'success')
                      ->whereBetween('updated_at', [$this->startDate, $this->endDate])
                      ->latest('updated_at')
                      ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Bayar',
            'Mahasiswa',
            'NIM',
            'Keterangan Pembayaran',
            'Tipe Pembayaran',
            'Pembayaran Tagihan (Rp)',
            'Biaya Admin (Rp)',
            'Total Bayar (Rp)',
        ];
    }

    public function map($transaksi): array
    {
        return [
            Carbon::parse($transaksi->updated_at)->format('d-m-Y H:i'),
            $transaksi->tagihan->user->name ?? 'N/A',
            $transaksi->tagihan->user->nim ?? 'N/A',
            $transaksi->deskripsi,
            Str::startsWith($transaksi->deskripsi, 'Cicilan') ? 'Cicilan' : 'Pelunasan',
            $transaksi->jumlah_bayar,
            $transaksi->biaya_admin,
            $transaksi->jumlah_bayar + $transaksi->biaya_admin,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '"Rp"#,##0',
            'G' => '"Rp"#,##0',
            'H' => '"Rp"#,##0',
        ];
    }

    /**
     * DIUBAH: Menambahkan logika untuk memberi border pada seluruh tabel.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Menambahkan Judul Laporan
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'Laporan Keuangan CBN EDUSAKU');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Periode: ' . $this->startDate->format('d F Y') . ' - ' . $this->endDate->format('d F Y'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Mengatur style header kolom
                $headerRange = "A3:H3";
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFD9E2F3');
                
                // ==================================================================
                // DITAMBAHKAN: Memberi border pada seluruh tabel data
                // ==================================================================
                // 1. Tentukan range tabel (dari header A3 sampai sel terakhir yang berisi data)
                $tableRange = 'A3:H' . $sheet->getHighestRow();
                
                // 2. Terapkan style border tipis ke semua sisi sel dalam range tersebut
                $sheet->getStyle($tableRange)->getBorders()
                      ->getAllBorders()
                      ->setBorderStyle(Border::BORDER_THIN);
                // ==================================================================
                
                // Menulis total di bagian bawah
                $lastRow = $sheet->getHighestRow();
                $totalRow = $lastRow + 2;

                $sheet->setCellValue("G{$totalRow}", 'TOTAL KESELURUHAN');
                $sheet->setCellValue("H{$totalRow}", $this->totalAmount);

                $sheet->getStyle("G{$totalRow}:H{$totalRow}")->getFont()->setBold(true);
                $sheet->getStyle("H{$totalRow}")->getNumberFormat()->setFormatCode('"Rp"#,##0');
            },
        ];
    }
}