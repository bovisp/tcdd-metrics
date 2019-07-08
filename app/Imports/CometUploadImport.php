<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Imports\CometAccessesSheet;
use App\Imports\CometCompletionsSheet;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class CometUploadImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
    }

    // public function getCsvSettings(): array
    // {
    //     return [
    //         'input_encoding' => 'ISO-8859-1'
    //     ];
    // }

    // public function headingRow(): int
    // {
    //     return 3;
    // }

    public function sheets(): array
    {
        return [
            0 => new CometCompletionsSheet(),
            1 => new CometAccessesSheet()
        ];
    }
}
