<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['note', 'date'];

    const LOAD_WITH = ['user', 'books'];

    /**
     * Relationship with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'order_detail', 'order_id', 'book_id')->withPivot(['quantity', 'price']);
    }

    /**
     * Find by min of date
     *
     * @param $query
     * @param $minDate
     * @return mixed
     */
    public function scopeByMinDate($query, $minDate)
    {
        return $query->whereDate('date', '>=', $minDate);
    }

    /**
     * Find by max of date
     *
     * @param $query
     * @param $maxDate
     * @return mixed
     */
    public function scopeByMaxDate($query, $maxDate)
    {
        return $query->whereDate('date', '<=', $maxDate);
    }
}
