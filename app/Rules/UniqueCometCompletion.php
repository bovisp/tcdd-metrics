<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueCometCompletion implements Rule
{
    protected $module;

    protected $date_completed;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($module, $date_completed)
    {
        $this->module = $module;

        $this->date_completed = $date_completed;
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
        $record = DB::connection('mysql')->table('comet_completion')
            ->where('email', $value)
            ->where('module', $this->module)
            ->where('date_completed', $this->date_completed)
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
