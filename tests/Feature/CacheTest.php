<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CacheTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function can_retrieve_a_value_from_cache()
    {
        Cache::forever('minDate', '24-Apr-2019');
        Cache::shouldReceive('minDate');    
    }
}
