<?php

namespace App\Jobs;

use App\GetCourses;
use App\CourseFormatter;
use Barryvdh\DomPDF\PDF;
use App\Mail\CatalogMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateCatalogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $courseFormatter = new CourseFormatter();
        $courseGetter = new GetCourses();

        $moodleCoursesFr = $courseGetter->getMoodleCourses('fr');
        $moodleCoursesFr = $courseFormatter->formatMoodleCourses('fr', $moodleCoursesFr);

        $moodleCoursesEn = $courseGetter->getMoodleCourses('en');
        $moodleCoursesEn = $courseFormatter->formatMoodleCourses('en', $moodleCoursesEn);

        $cometCoursesFr = $courseGetter->getCometCourses('fr');
        $cometCoursesFr = $courseFormatter->formatCometCourses('fr', $cometCoursesFr);

        $cometCoursesEn = $courseGetter->getCometCourses('en');
        $cometCoursesEn = $courseFormatter->formatCometCourses('en', $cometCoursesEn);

        $dataEn = [
            'moodleCourses' => $moodleCoursesEn,
            'cometCourses' => $cometCoursesEn
        ];
        $dataFr = [
            'moodleCourses' => $moodleCoursesFr,
            'cometCourses' => $cometCoursesFr
        ];

        $pdfEn = \PDF::loadView('englishCoursesByCategoryPDF', $dataEn);
        $pdfFr = \PDF::loadView('frenchCoursesByCategoryPDF', $dataFr);


        Mail::to('me@me.com')->send(new CatalogMail($pdfEn, $pdfFr));
    }
}
