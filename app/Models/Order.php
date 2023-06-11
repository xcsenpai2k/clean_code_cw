<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;

class Order extends Model
{
    use HasFactory, BaseModel;

    protected $fillable = ['status', 'total_price', 'created_by', 'updated_by'];

    protected static function booted()
    {
        static::creating(function ($order) {
            $order->created_by = auth()->id();
            $order->updated_by = auth()->id();
            $order->status = ORDER_STATUS_UNPAID;
        });
    }

    public function isPaid()
    {
        return $this->status === ORDER_STATUS_PAID;
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function detail()
    {
        return $this->hasOne(OrderDetail::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id');
    }

    /**
     * scopeFindByName
     *
     * @param  Builder $query
     * @param  Request $request
     * @return Builder
     */
    public function scopeFindByUserName(Builder $query, Request $request): Builder
    {
        if ($search = $request->search) {
            return $query->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like',  '%' . $search . '%');
            });
        }
        return $query;
    }
}
