<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportStayConnectedWebinarViews implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    use GetWebinarData;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
}
