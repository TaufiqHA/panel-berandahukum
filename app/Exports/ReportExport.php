<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings
{
    protected $export;

    public function __construct(array $export)
    {
        $this->export = $export;
    }

    public function headings(): array
    {
        return $this->export[0];
    }

    public function array(): array
    {
        unset($this->export[0]);

        return array_values($this->export);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:XFD1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
