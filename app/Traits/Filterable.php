<?php

namespace App\Traits;

use Validator;
use Laravel\Scout\Builder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use App\Rules\GenericFilterConditionRule;
use App\Rules\GenericFilterFilterableRule;
use Illuminate\Validation\ValidationException;
/**
 * This trait adds some basic functionality for generic filtering and full-text searching in Eloquent models.
 * - under the hood it uses laravel scout as the searching engine
 * **Use this trait in Models that need generic filtering/searching**
 */
trait Filterable
{
    use Searchable;
    use GenericFilterImplementation;
    
    /**
     * Returns the columns that can be filtered
     */
    public static function getFilterableColumns(): array{
        return  (new self())->filterable ?? [];
    }

    /**
     * Override the default scout function to select by default, all the $filterable columns that were set at the model
     */
    public function toSearchableArray(): array
    {
		$modelFilterables = $this->getFilterableColumns();

        //Remove the primary key from the full-text search and any relationship filterables (that have a '.' in the name)
        $modelPrimaryKey = $this->getKeyName();
        $fullTextSearchableColumns = array_filter($modelFilterables, function ($value) use ($modelPrimaryKey) {
            return $value != $modelPrimaryKey && !Str::contains($value, ".");
        }); 

        //We convert the column array to a column key -> column value
        $values = array_combine($fullTextSearchableColumns, array_map(function ($value) {
            return $this->{$value};
        }, $fullTextSearchableColumns));
        return $values;
    }

    protected static function getRelationName($columnName)
    {
        //get the relation name everything before the last .
        //Sometimes there is no relation name, so we return null
        $relationName = substr($columnName, 0, strrpos($columnName, "."));
        return $relationName;
    }

    protected static function getColumnName($columnName)
    {
        //get the column name everything after the last .
        //sometimes there is no relation name, so we return the whole column name
        $lastDot = strrpos($columnName, ".");
        if ($lastDot === false)
            return $columnName;
        else
            $columnName = substr($columnName, strrpos($columnName, ".") + 1);
        return $columnName;
    }

    /**
     * Validates the Request filter model
     * @param Illuminate\Database\Eloquent\Model $model
     * @throws Illuminate\Validation\ValidationException
     * @return array filter model object
     */
    private static function validateFilterModel($model = null)
    {
        /**
         * @var Request $request
         */
        $request = app()->request;

        //Validates the filtering options
        $filterModel = $request->query("filters");
        if ($filterModel) {
            $filterModel = json_decode(urldecode($filterModel), true);

            if (!is_array($filterModel))
                throw ValidationException::withMessages([
                    "filters" => __('pagination.filters.invalid_json')
                ]);
            Validator::make(
                $filterModel,
                [
                    '*.column' => ['required', new GenericFilterFilterableRule($model)],
                    '*.conditions' => ['required', "array", "min:1", "max:2"],
                    '*.conditions.*' => ['bail', 'required', new GenericFilterConditionRule],
                    '*.junction' => ['in:and,or,AND,OR']
                ],
                [
                    '*.column.required' => __('pagination.filters.column_required'),
                    '*.conditions.required' => __('pagination.filters.conditions_required'),
                    '*.conditions.array' => __('pagination.filters.conditions_not_array'),
                    '*.conditions.min' => __('pagination.filters.conditions_out_of_range'),
                    '*.conditions.max' => __('pagination.filters.conditions_out_of_range'),
                    '*.junction.in' => __('pagination.filters.junction_invalid', ['allowed_junctions' => '[and, or, AND, OR]']),
                ]
            )->validate();
            return $filterModel;
        }
        return [];
    }
    
   
    /**
     * Applies the search to the query (search engine) to the querybuilder
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string|int|float $filterModel
     */
    public static function useSearchEngine($model, $searchValue): Builder
    {
        return $model::search($searchValue);
    }

    /**
     * Adds basic filter and search functionality to models
     * Use this on models
     * 
     * - You need to whitelist which columns can be filtered by using the **$filterable** property on the target model
     * - When using the full-text search engine (search query parameter filled), the column filters are ignored. 
     */
    public static function searchAndFilter(): \Laravel\scout\Builder | \Illuminate\Database\Eloquent\Builder
    {
        //If the model is not set, we try to find it out
        $model = self::class;

        //Check the trait is in an eloquent model
        if (is_a($model, \Illuminate\Database\Eloquent\Model::class, true)) {
            $filterModel = self::validateFilterModel($model);
            $request = app()->request;
            $modelInstance = new $model();

            $searchValue = $request->has("search") ? $request->query("search") : null;
            //If there is a full text search value, we only use the search engine and ignore the filters
            if ($searchValue){
                $queryBuilder = self::search($searchValue);
            }
            else {
                $queryBuilder = $model::query();
                self::applyFilters($queryBuilder, $filterModel, $modelInstance);
            }
            return $queryBuilder;
        } else
            throw new \Exception("Filterable trait can only be used on Eloquent Models, or set the model you're using explicitly");
    }
}
