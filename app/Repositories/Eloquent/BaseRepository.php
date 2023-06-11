<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Base Repository
 *
 * Long description for class (if any)...
 *
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Model
     *
     * @var Model
     */
    protected $model;

    /**
     * Get detail model
     *
     * @param Model $model
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }
    
    /**
     * all
     *
     * @param  array $cols
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all($cols = ['*'])
    {
        return $this->model->newQuery()->get($cols);
    }

    /**
     * Get EloquentBuilder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function eloquentBuilder()
    {
        return $this->model->newQuery();
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function queryBuilder()
    {
        return $this->eloquentBuilder()->getQuery();
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @param  array|string  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static|static[]
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function findOrFail($id, $columns = ["*"])
    {
        if($id instanceof Collection) $id = $id->toArray();
        return $this->eloquentBuilder()->findOrFail($id, $columns);
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     * @param  array|string  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null
     */
    public function find($id, $columns = ['*'])
    {
        if($id instanceof Collection) $id = $id->toArray();
        return $this->eloquentBuilder()->find($id, $columns);
    }

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model|$this
     */
    public function create($data)
    {
        return $this->eloquentBuilder()->create($data);
    }

    /**
     * insert
     *
     * @param  array $data
     * @return bool
     */
    public function insert(array|Collection $data)
    {
        if ($data instanceof Collection) $data = $data->toArray();
        return $this->queryBuilder()->insert($data);
    }

    /**
     * Update a model
     *
     * @param array         $data   Where something interesting takes place
     * @param array|integer $params Where something interesting takes place
     *
     * @return Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($data, $id)
    {
        $item = $this->findOrFail($id);

        $item->update($data);

        return $item;
    }

    /**
     * Delete a model
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id)
    {
        $item =  $this->findOrFail($id);

        return $item->delete($id);
    }
}
