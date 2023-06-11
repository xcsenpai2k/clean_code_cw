<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait BaseModel
{    
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    /**
     * getCreatedAtAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    
    /**
     * getUpdatedAtAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    
    /**
     * scopeFindByStatus
     *
     * @param  Builder $query
     * @param  Request $request
     * @return Builder
     */
    public function scopeFindByStatus(Builder $query, Request $request): Builder
    {
        return $query->when($request->status, fn ($q) => $q->where('status', $request->status));
    }
    
    /**
     * scopeFilterDate
     *
     * @param  Builder $query
     * @param  mixed $fromDate
     * @return Builder
     */
    public function scopeFilterDate(Builder $query, $fromDate): Builder
    {
        return $query->when($fromDate, fn ($q) => $q->where('created_at', '>', $fromDate));
    }
}
