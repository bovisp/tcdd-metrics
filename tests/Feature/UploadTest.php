<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UploadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_save_comet_accesses()
    {
        $this->withoutExceptionHandling();

        $row = [
            'email' => 'asgsagsdgs',
            'last' => 'asgsgsadgdsg',
            'first' => 'dfhfdhfghfg',
            'Module' => 'fghjhjdghkj',
            'language' => 'sfasfsadf',
            'sessions' => 21,
            'elapsed_time' => 35.5,
            'session_pages' => 3,
            'date' => '2019-05-31'
        ];
        $sheet = [$row];
        $data = [$sheet];

        $this->post('/comet-accesses', $data);

        $this->assertDatabaseHas('comet_access', $row);
    }

    /** @test */
    public function a_user_can_save_comet_completions()
    {
        $this->withoutExceptionHandling();

        $row = [
            'email' => 'asgsagsdgs',
            'Last_name' => 'asgsgsadgdsg',
            'First_name' => 'dfhfdhfghfg',
            'Module' => 'fghjhjdghkj',
            'Language' => 'sfasfsadf',
            'score' => 21,
            'date_completed' => '2019-05-31'
        ];
        $sheet = [$row];
        $data = [$sheet];

        $this->post('/comet-completions', $data);

        $attributes = [
            'email' => $row['email'],
            'last' => $row['Last_name'],
            'first' => $row['First_name'],
            'module' => $row['Module'],
            'language' => $row['Language'],
            'score' => $row['score'],
            'date_completed' => $row['date_completed']
        ];

        $this->assertDatabaseHas('comet_completion', $attributes);
    }
}
