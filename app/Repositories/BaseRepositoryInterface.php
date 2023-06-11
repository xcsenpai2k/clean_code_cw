<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * all
     *
     * @param  array $cols
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all();
    /**
     * Get EloquentBuilder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function eloquentBuilder();

    /**
     * Get a new instance of the query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function queryBuilder();

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @param  array|string  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static|static[]
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function findOrFail($id, $columns = ["*"]);

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     * @param  array|string  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null
     */
    public function find($id, $columns = ['*']);

    /**
     * Save a new model and return the instance.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model|$this
     */
    public function create(array $data);

    /**
     * insert
     *
     * @param  array|Collection $data
     * @return bool
     */
    public function insert(array|Collection $data);

    /**
     * Update a model
     *
     * @param array         $data   Where something interesting takes place
     * @param array|integer $params Where something interesting takes place
     *
     * @return Illuminate\Database\Eloquent\Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function update(array $data, int $id);

    /**
     * Delete a model
     *
     * @param int $it
     *
     * @return bool
     */
    public function delete(int $id);
}
