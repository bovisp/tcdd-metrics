<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Traits\GetTrainingPortalData;

class CourseViewsByCourseSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    use GetTrainingPortalData;
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $startTimestamp;
    protected $endTimestamp;

    public function __construct($startTimestamp, $endTimestamp)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
    }

    public function collection()
    {   
        return $this->getTrainingPortalViews($this->startTimestamp, $this->endTimestamp);
    }

    public function headings(): array
    {
        return [
            'Course Id',
            'English Category Name',
            'French Category Name',
            'English Course Name',
            'French Course Name',
            'Course Language',
            'Views'
        ];
    }

    public function title(): string
    {
        return 'Views By Course';
    }
}
