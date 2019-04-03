<?php

use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('mysql')->table('languages')->insert([
            ['name' => 'English'],
            ['name' => 'French']
        ]);
    }
}
