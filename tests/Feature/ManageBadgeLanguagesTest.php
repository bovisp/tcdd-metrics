<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageBadgeLanguagesTest extends TestCase
{
    /** test */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
