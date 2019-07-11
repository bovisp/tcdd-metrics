<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueCometAccess implements Rule
{
    protected $module;

    protected $date;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($module, $date)
    {
        $this->module = $module;

        $this->date = $date;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $record = DB::connection('mysql')->table('comet_access')
            ->where('email', $value)
            ->where('module', $this->module)
            ->where('date', $this->date)
            ->count();

        return $record === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
