<?php

namespace App\Rules;

use App\Enums\GenericFilterConditionTypeEnum;
use App\Enums\GenericFilterDateOperationsEnum;
use App\Enums\GenericFilterNumberOperationsEnum;
use App\Enums\GenericFilterTextOperationsEnum;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * This rule checks wether the given condition object is valid
 */
class GenericFilterConditionRule implements ValidationRule
{

    /**
     * @var string $errorMessage
     */
    protected $errorMessage = "";

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->validateFilterType($value))
            $fail($this->errorMessage);

        if (!$this->validateOperation($value))
            $fail($this->errorMessage);

        if (!$this->validateValue($value))
            $fail($this->errorMessage);
    }

    /**
     * Validates the condition type
     *
     * @return bool
     */
    private function validateFilterType($value)
    {
        $allowedValues = GenericFilterConditionTypeEnum::getValues(false);

        if (!isset($value["filterType"])) {
            $this->errorMessage = "Filter type is required: " . implode(", ", $allowedValues);
            return false;
        }
        if (!in_array($value["filterType"], $allowedValues)) {

            $this->errorMessage = "Invalid filters, allowed:" . implode(", ", $allowedValues);
            return false;
        }
        return true;
    }

    /**
     * Validates the condition's operation, based on the filter type
     * @return bool
     */
    private function validateOperation($value)
    {
        $filterType = GenericFilterConditionTypeEnum::tryFrom($value["filterType"]);

        switch ($filterType) {
            case GenericFilterConditionTypeEnum::TEXT:
                $allowedOperations = GenericFilterTextOperationsEnum::getValues(false);
                if (!isset($value["operation"])) {
                    $this->errorMessage = "Invalid text filter operation, allowed:" . implode(", ", $allowedOperations);
                    return false;
                }
                if (!in_array($value["operation"], $allowedOperations)) {
                    $this->errorMessage = "Invalid text filter operation, allowed:" . implode(", ", $allowedOperations);
                    return false;
                }
                break;
            case GenericFilterConditionTypeEnum::NUMBER:
                $allowedOperations = GenericFilterNumberOperationsEnum::getValues(false);
                if (!isset($value["operation"])) {
                    $this->errorMessage = "Invalid number filter operation, allowed:" . implode(", ", $allowedOperations);
                    return false;
                }
                if (!in_array($value["operation"], $allowedOperations)) {
                    $this->errorMessage = "Invalid number filter operation, allowed:" . implode(", ", $allowedOperations);
                    return false;
                }
                break;
            case GenericFilterConditionTypeEnum::DATE:
                $allowedOperations = GenericFilterDateOperationsEnum::getValues(false);
                if (!isset($value["operation"])) {
                    $this->errorMessage = "Invalid date filter operation, allowed:" . implode(", ", $allowedOperations);
                    return false;
                }
                if (!in_array($value["operation"], $allowedOperations)) {
                    $this->errorMessage = "Invalid date filter operation, allowed:" . implode(", ", $allowedOperations);
                    return false;
                }

                break;
            default:
                $this->errorMessage = "Allowed filter types are text, number and date";
                return false;
        }
        return true;
    }

    /**
     * Validates the condition's value, based on the filter type and operation type
     * @return bool
     */
    private function validateValue($value)
    {
        $filterType = GenericFilterConditionTypeEnum::tryFrom($value["filterType"]);
        switch ($filterType) {
            case GenericFilterConditionTypeEnum::TEXT:
                if (isset($value["value"]) && !is_string($value["value"])) {
                    $this->errorMessage = "Text filter value must be a string";
                    return false;
                }
                break;
            case GenericFilterConditionTypeEnum::NUMBER:
                $operation = GenericFilterNumberOperationsEnum::tryFrom($value["operation"]);
                if (isset($value["value"]) && !is_numeric($value["value"])) {
                    $this->errorMessage = "Number filter value must be a number";
                    return false;
                }
                break;
            case GenericFilterConditionTypeEnum::DATE:
                try {
                    $date = new \DateTime($value["value"]);
                    if (!$date)
                        throw new Exception("Invalid date");
                } catch (\Exception $e) {
                    $this->errorMessage = "Date filter value must be a valid date";
                    return false;
                }
                break;
            default:
                $this->errorMessage = "Allowed filter types are text, number and date";
                return false;
                break;
        }
        return true;
    }
}
