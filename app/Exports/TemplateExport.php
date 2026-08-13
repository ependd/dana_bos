<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateExport implements FromArray, WithHeadings
{
    private array $headings;
    private array $exampleRow;

    public function __construct(array $headings, array $exampleRow = [])
    {
        $this->headings = $headings;
        $this->exampleRow = $exampleRow;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->exampleRow ? [$this->exampleRow] : [];
    }
}
