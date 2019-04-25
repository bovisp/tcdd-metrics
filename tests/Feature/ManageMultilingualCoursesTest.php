<?php

namespace Tests\Feature;

use App\Language;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageMultilingualCoursesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_create_a_multilingual_course() {
        $this->withoutExceptionHandling();

        //create a multilingual course
        $course = new \stdClass;
        $course->id = 7;
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId([]);

        $attributes = [
            'course_id' => $course->id,
            'multilingual_course_group_id' => $mlangcoursegroupid
        ];

        $this->post('/multilingual-courses', $attributes);
        $this->assertDatabaseHas('multilingual_course', $attributes);
    }

    /** @test */
    public function a_user_can_create_a_multilingual_course_without_a_course_group() {
        $this->withoutExceptionHandling();

        //create a multilingual course
        $course = new \stdClass;
        $course->id = 7;

        $attributes = [
            'course_id' => $course->id
        ];

        $this->post('/multilingual-courses', $attributes);
        $this->assertDatabaseHas('multilingual_course', $attributes);
    }

    /** @test */
    public function a_user_can_view_their_multilingual_courses() {
        $this->withoutExceptionHandling();
        
        //create a multilingual course
        $course = new \stdClass;
        $course->id = 7;
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId([]);

        $attributes = [
            'course_id' => $course->id,
            'multilingual_course_group_id' => $mlangcoursegroupid
        ];

        $this->post('/multilingual-courses', $attributes);
        $this->get('/multilingual-courses')->assertJsonFragment([
            'course_id' => $course->id
        ]);
    }

    /** @test */
    public function it_requires_the_course_to_exist_in_the_database()
    {
        //create a multilingual course
        $course = new \stdClass;
        $course->id = 7;
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId([]);

        $attributes = [
            'course_id' => 99999999,
            'multilingual_course_group_id' => $mlangcoursegroupid
        ];

        $this->post('/multilingual-courses', $attributes)->assertSessionHasErrors(['course_id']);
    }

    /** @test */
    public function a_user_can_remove_a_multilingual_course()
    {
        $this->withoutExceptionHandling();
        //create a multilingual course
        $course = new \stdClass;
        $course->id = 7;
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId([]);

        $attributes = [
            'course_id' => $course->id,
            'multilingual_course_group_id' => $mlangcoursegroupid
        ];

        $multilingualCourseId = DB::connection('mysql')->table('multilingual_course')->insertGetId($attributes);
        $this->assertDatabaseHas('multilingual_course', $attributes);

        $this->delete("/multilingual-courses/{$multilingualCourseId}");
        $this->assertDatabaseMissing('multilingual_course', $attributes);
        $this->assertDatabaseMissing('multilingual_course_group', ['id' => $mlangcoursegroupid]);
    }
}
