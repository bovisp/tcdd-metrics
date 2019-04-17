<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exports\CourseViewsByCourseSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Exports\CourseViewsByCourseCategorySheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportCourseViewsByCourse implements WithMultipleSheets
{
    use Exportable;

    protected $startTimestamp;
    protected $endTimestamp;
    protected $interval;

    public function __construct($startTimestamp, $endTimestamp, $interval)
    {
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
        $this->interval = $interval;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets = [new CourseViewsByCourseSheet($this->startTimestamp, $this->endTimestamp, $this->interval),
            new CourseViewsByCourseCategorySheet($this->startTimestamp, $this->endTimestamp, $this->interval),
            new CourseViewsByParentCategorySheet($this->startTimestamp, $this->endTimestamp, $this->interval)];

        return $sheets;
    }
}
