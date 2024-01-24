<?php

namespace App\Traits;

use SplFixedArray;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Enums\GenericFilterConditionTypeEnum;
use App\Enums\GenericFilterDateOperationsEnum;
use App\Enums\GenericFilterTextOperationsEnum;
use App\Enums\GenericFilterNumberOperationsEnum;

/**
 * This trait implements the application of generic filters to querybuilders
 */
trait GenericFilterImplementation
{
    /**
     * Applies the filters to the query (where clauses) to the querybuilder
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param array $filterModel
     * @param \Illuminate\Database\Eloquent\Model $modelInstance
     */
    protected static function applyFilters($queryBuilder = null, $filterModel = [], $modelInstance = null)
    {
        foreach ($filterModel as $filterItem) {
            $column = $filterItem["column"];
            $junction = $filterItem["junction"] ?? "and";
            $junction = Str::lower($junction);
            $conditions = $filterItem["conditions"];

            //Check if we are dealing with a relation, in case there is a . in the column name
            $relationName = self::getRelationName($column);
            $column = self::getColumnName($column);

            if (!$relationName)
                $column = $modelInstance->getTable() . "." . $column;

            $applyFilterFn = function ($queryBuilder) use ($conditions, $column, $junction) {
                foreach ($conditions as $condition) {
                    $filterType = GenericFilterConditionTypeEnum::tryFrom($condition["filterType"]);

                    //We can have as many types as we want, but out of example, here are three basic ones
                    switch ($filterType) {
                        case GenericFilterConditionTypeEnum::TEXT:
                            self::applyTextFilter($column, $condition, $junction, $queryBuilder);
                            break;
                        case GenericFilterConditionTypeEnum::DATE:
                            self::applyDateFilter($column, $condition, $junction, $queryBuilder);
                            break;
                        case GenericFilterConditionTypeEnum::NUMBER:
                            self::applyNumberFilter($column, $condition, $junction, $queryBuilder);
                            break;
                        default:
                            break;
                    }
                }
            };

            if ($relationName) {
                $queryBuilder->whereHas($relationName, $applyFilterFn);
            } else {
                $queryBuilder->where($applyFilterFn);
            }
        }
    }

    /**
     * Applies text filters to the querybuilder (where clauses) to the querybuilder
     * @param string=and|or $junction type of where clause
     * @param array $condition condition object
     * @param string $column column name
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \Illuminate\Database\Eloquent\Model $modelInstance
     */
    private static function applyTextFilter(
        $column = null,
        $condition = [],
        $junction = 'and',
        $queryBuilder
    ) {

        $operation = GenericFilterTextOperationsEnum::tryFrom($condition["operation"]) ?? GenericFilterTextOperationsEnum::CONTAINS;
        $value = $condition["value"] ?? '';

        $whereClauseParams = new SplFixedArray(3);
        $whereClauseParams[0] = DB::raw('LOWER(' . $column . ')');
        $whereClauseParams[2] = Str::lower($value);

        switch ($operation) {
            case GenericFilterTextOperationsEnum::CONTAINS:
                $whereClauseParams[1] = 'LIKE';
                $whereClauseParams[2] = '%' . $whereClauseParams[2] . '%';
                break;
        }

        if ($junction == "and") {
            $queryBuilder->where(...$whereClauseParams);
        } else
            $queryBuilder->orWhere(...$whereClauseParams);
    }

    /**
     * Applies number filters to the query (where clauses) to the querybuilder
     * @param string=and|or $junction type of where clause
     * @param array $condition condition object
     * @param string $column column name
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \Illuminate\Database\Eloquent\Model $modelInstance
     */
    private static function applyNumberFilter(
        $column = null,
        $condition = [],
        $junction = 'and',
        $queryBuilder
    ) {
        $operation = GenericFilterNumberOperationsEnum::tryFrom($condition["operation"]) ?? GenericFilterNumberOperationsEnum::EQUALS;
        $value = $condition["value"] ?? 0;
        $whereClauseParams = new SplFixedArray(3);
        $whereClauseParams[0] = $column;
        $whereClauseParams[2] = $value;

        switch ($operation) {
            case GenericFilterNumberOperationsEnum::EQUALS:
                $whereClauseParams[1] = '=';
                break;

            case GenericFilterNumberOperationsEnum::GREATER_THAN:
                $whereClauseParams[1] = '>';
                break;

            case GenericFilterNumberOperationsEnum::LESS_THAN:
                $whereClauseParams[1] = '<';
                break;
        }

        if ($junction == "and")
            $queryBuilder->where(...$whereClauseParams);
        else
            $queryBuilder->orWhere(...$whereClauseParams);
    }

    /**
     * Applies date filters to the query (where clauses to the querybuilder)
     * @param string=and|or $junction type of where clause
     * @param array $condition condition object
     * @param string $column column name
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param \Illuminate\Database\Eloquent\Model $modelInstance
     */
    private static function applyDateFilter(
        $column = null,
        $condition = [],
        $junction = 'and',
        $queryBuilder
    ) {
        $operation = GenericFilterDateOperationsEnum::tryFrom($condition["operation"]) ?? GenericFilterDateOperationsEnum::EQUALS;
        $value = new \DateTime($condition["value"]) ?? Carbon::now();
        $whereClauseParams = new SplFixedArray(3);
        $whereClauseParams[0] = $column;
        $whereClauseParams[2] = $value;
        switch ($operation) {
            case GenericFilterDateOperationsEnum::EQUALS:
                $whereClauseParams[1] = '=';
                break;

            case GenericFilterDateOperationsEnum::GREATER_THAN:
                $whereClauseParams[1] = '>=';
                break;
            case GenericFilterDateOperationsEnum::LESS_THAN:
                $whereClauseParams[1] = '<=';
                break;
        }

        if ($junction == "and")
            $queryBuilder->where(...$whereClauseParams);
        else
            $queryBuilder->orWhere(...$whereClauseParams);
    }
}
