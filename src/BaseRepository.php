<?php

namespace WendellAdriel\LaravelMore;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

abstract class BaseRepository
{
    public const ALL_COLUMNS = ['*'];

    protected Model $model;
    protected string $tableName;
    private array $disableGlobalScopes = [];

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->tableName = $model->getTable();
    }

    /**
     * Disables a named global scope
     *
     * @param string $scopeName
     * @return BaseRepository
     */
    public function disableGlobalScope(string $scopeName): BaseRepository
    {
        $this->disableGlobalScopes[$scopeName] = $scopeName;
        return $this;
    }

    /**
     * Enables a named global scope
     *
     * @param string $scopeName
     * @return BaseRepository
     */
    public function enableGlobalScope(string $scopeName): BaseRepository
    {
        unset($this->disableGlobalScopes[$scopeName]);
        return $this;
    }

    /**
     * Gets all models
     *
     * @param array $columns
     * @return Collection
     */
    public function getAll(array $columns = self::ALL_COLUMNS): Collection
    {
        return $this->newQuery($columns)->get();
    }

    /**
     * Gets all models by the given attribute
     *
     * @param string $attribute
     * @param mixed  $value
     * @param string $compareType
     * @param bool   $withTrash
     * @return Collection
     */
    public function getAllBy(string $attribute, $value, string $compareType = '=', bool $withTrash = false): Collection
    {
        return $this->getByParamsBase([[$attribute, $value, $compareType]], $compareType, $withTrash)->get();
    }

    /**
     * Gets a model by the given attribute
     *
     * @param string $attribute
     * @param mixed  $value
     * @param string $compareType
     * @param bool   $withTrash
     * @return Model|null
     */
    public function getBy(string $attribute, $value, string $compareType = '=', bool $withTrash = false): ?Model
    {
        return $this->getByParamsBase([[$attribute, $value, $compareType]], $compareType, $withTrash)->first();
    }

    /**
     * Gets a model by the given attribute or throws an exception
     *
     * @param string $attribute
     * @param mixed  $value
     * @param string $compareType
     * @param bool   $withTrash
     * @return Model
     */
    public function getByOrFail(string $attribute, $value, string $compareType = '=', bool $withTrash = false): Model
    {
        return $this->getByParamsBase([[$attribute, $value, $compareType]], $compareType, $withTrash)->firstOrFail();
    }

    /**
     * Gets a model by some given attributes
     *
     * @param array  $params
     * @param string $compareType
     * @param bool   $withTrash
     * @return Model|null
     */
    public function getByParams(array $params, string $compareType = '=', bool $withTrash = false): ?Model
    {
        return $this->getByParamsBase($params, $compareType, $withTrash)->first();
    }

    /**
     * Gets a model by some attributes or throws an exception
     *
     * @param array  $params
     * @param string $compareType
     * @param bool   $withTrash
     * @return Model
     */
    public function getByParamsOrFail(array $params, string $compareType = '=', bool $withTrash = false): Model
    {
        return $this->getByParamsBase($params, $compareType, $withTrash)->firstOrFail();
    }

    /**
     * Gets all models by some given attributes
     *
     * @param array  $params
     * @param string $compareType
     * @param bool   $withTrash
     * @return Collection
     */
    public function getAllByParams(array $params, string $compareType = '=', bool $withTrash = false): Collection
    {
        return $this->getByParamsBase($params, $compareType, $withTrash)->get();
    }

    /**
     * Updates one or more models
     *
     * @param string $attribute
     * @param        $value
     * @param array  $updateFields
     * @return int
     */
    public function updateBy(string $attribute, $value, array $updateFields): int
    {
        $formattedValue = is_array($value) || $value instanceof Enumerable ? $value : [$value];
        return $this->newQuery()
            ->whereIn($attribute, $formattedValue)
            ->update($updateFields);
    }

    /**
     * Deletes one or more models
     *
     * @param string $attribute
     * @param        $value
     * @return mixed
     */
    public function deleteBy(string $attribute, $value)
    {
        $formattedValue = is_array($value) || $value instanceof Enumerable ? $value : [$value];
        return $this->newQuery()
            ->whereIn($attribute, $formattedValue)
            ->delete();
    }

    /**
     * Creates a new model
     *
     * @param array $args
     * @return Builder|Model
     */
    public function create(array $args)
    {
        return $this->model->newQuery()->create($args);
    }

    /**
     * Gets the table for the base model of the repository
     *
     * @return string
     */
    protected function getTable(): string
    {
        return $this->tableName;
    }

    /**
     * Builds a new query
     *
     * @param array|string[]|string $columns
     * @return Builder
     */
    protected function newQuery(...$columns): Builder
    {
        if (count($columns) === 1 && is_array($columns[0])) {
            $columns = $columns[0];
        }

        if (empty($columns)) {
            $columns = [self::ALL_COLUMNS];
        }

        return $this->model->newQuery()->select(...$columns);
    }

    /**
     * Builds a base query
     *
     * @param array  $params
     * @param string $defaultCompareType
     * @param bool   $withTrashed
     * @return Builder
     */
    private function getByParamsBase(array $params, string $defaultCompareType = '=', bool $withTrashed = false): Builder
    {
        /** @var Builder $query */
        $query = $this->processDisableGlobalScopesForModel();
        foreach ($params as $param) {
            $compareType = count($param) === 2 ? $defaultCompareType : $param[2];
            if (mb_strtoupper($compareType) === 'IN') {
                $query = $query->whereIn($param[0], $param[1]);
            } else {
                $query = $query->where($param[0], $compareType, $param[1]);
            }
        }

        return $withTrashed ? $query->withTrashed() : $query;
    }

    /**
     * Process to disable global scopes registered to be removed
     *
     * @return Model
     */
    private function processDisableGlobalScopesForModel(): Model
    {
        foreach ($this->disableGlobalScopes as $scopeName) {
            $this->model = $this->model->withoutGlobalScope($scopeName);
        }

        return $this->model;
    }
}
