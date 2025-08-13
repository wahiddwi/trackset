<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueNameCaseInsensitive implements Rule
{
  protected $table;
  protected $column;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table, $column = 'mtn_type')
    {
        $this->table = $table;
        $this->column = $column;
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
        return !DB::table($this->table)
                    ->whereRaw("LOWER({$this->column}) = ?", [strtolower($value)])
                    ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Nama Sudah Digunakan';
    }
}
