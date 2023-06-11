<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory, BaseModel;

    protected $primaryKey = 'user_id';

    protected $fillable = ['first_name', 'last_name', 'phone', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    private function _getAddresses(): HasOne
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id', 'user_id');
    }

    public function shippingAddress(): HasOne
    {
        return $this->_getAddresses()->where('type', '=', 'shipping');
    }

    public function billingAddress(): HasOne
    {
        return $this->_getAddresses()->where('type', '=', 'billing');
    }

    public function address(): HasOne
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id', 'user_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id', 'user_id');
    }

    public function scopeFindCustomer(Builder $query, Request $request)
    {
        if ($search = $request->search) {
            return $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('email', 'like', '%' . $search . '%');
                })
                ->orWhere('phone', 'like', '%' . $search . '%');
        }
        return $query;
    }

    public function scopeSortByColumn(Builder $query, Request $request)
    {
        $sortField = $request->sortField;
        if (in_array($sortField, $this->getTableColumns())) {
            $sortDirection = $request->get('sortDirection');
            $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';
            return $query->orderBy($sortField, $sortDirection);
        }
        return $query;
    }
}
