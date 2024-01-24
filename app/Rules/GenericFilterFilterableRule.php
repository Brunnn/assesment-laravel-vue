<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Checks if a column name is listed as 'filterable' in the model
 * 
 * this is used in the cursor pagination standards
 */
class GenericFilterFilterableRule implements ValidationRule
{

    /**
     * @var Illuminate\Database\Eloquent\Model $model
     */
    protected $model = null;

    /**
     * @var array $filterableColumns
     */
    protected $filterableColumns = [];

    /**
     * @var string $column
     */
    protected $column = '';

    /**
     * Create a new rule instance.
     * @param Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function __construct($model = null)
    {
        $this->model = new $model();
        $this->filterableColumns = $this->model->getFilterableColumns() ?? [];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array($value, $this->filterableColumns)){
            $this->column = $value;
            $fail("Column $this->column is not filterable, allowed values:". implode(', ', $this->filterableColumns));
        }
    }
}
