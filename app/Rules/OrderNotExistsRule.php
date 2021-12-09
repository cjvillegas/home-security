<?php

namespace App\Rules;

use App\Models\Order;
use Illuminate\Contracts\Validation\Rule;

class OrderNotExistsRule implements Rule
{
    /**
     * @var string
     */
    private $column;

    /**
     * Create a new rule instance.
     *
     * @param string $column
     *
     * @return void
     */
    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !Order::where($this->column, $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The order already exist in our database.';
    }
}
