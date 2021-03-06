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
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => '']);

        $attributes = [
            'course_id' => $course->id,
            'multilingual_course_group_id' => $mlangcoursegroupid
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
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => '']);

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
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => '']);

        $attributes = [
            'course_id' => 99999999,
            'multilingual_course_group_id' => $mlangcoursegroupid
        ];

        $this->post('/multilingual-courses', $attributes)->assertSessionHasErrors(['course_id']);
    }

    /** @test */
    public function it_requires_the_course_group_to_exist_in_the_database()
    {
        //create a multilingual course
        $course = new \stdClass;
        $course->id = 7;
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => '']);

        $attributes = [
            'course_id' => 7,
            'multilingual_course_group_id' => 99999999
        ];

        $this->post('/multilingual-courses', $attributes)->assertSessionHasErrors(['multilingual_course_group_id']);
    }

    /** @test */
    public function a_user_can_remove_a_multilingual_course()
    {
        $this->withoutExceptionHandling();

        //create a multilingual course
        $course = new \stdClass;
        $course->id = 7;
        $mlangcoursegroupid = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => '']);

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

    /** @test */
    public function a_user_can_update_a_multilingual_course()
    {
        $this->withoutExceptionHandling();
        
        //create a multilingual course
        $course = new \stdClass;
        $course->id = 7;
        $mlangcoursegroupid1 = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => '']);

        $attributes = [
            'course_id' => $course->id,
            'multilingual_course_group_id' => $mlangcoursegroupid1
        ];

        $multilingualCourseId = DB::connection('mysql')->table('multilingual_course')->insertGetId($attributes);
        $this->assertDatabaseHas('multilingual_course', $attributes);

        $mlangcoursegroupid2 = DB::connection('mysql')->table('multilingual_course_group')->insertGetId(['name' => '']);

        $this->put("/multilingual-courses/{$multilingualCourseId}", [
            'course_id' => $course->id,
            'multilingual_course_group_id' => $mlangcoursegroupid2
        ]);

        $this->assertDatabaseHas('multilingual_course', [
            'course_id' => $course->id,
            'multilingual_course_group_id' => $mlangcoursegroupid2
        ]);
    }
}
