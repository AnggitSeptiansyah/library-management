<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'category_id',
        'edition',
        'publication_year',
        'total_pages',
        'total_copies',
        'available_copies',
        'description',
        'cover_image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowingItems()
    {
        return $this->hasMany(BorrowingItem::class);
    }

    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
    }

    public function decrementStock()
    {
        if($this->available_copies > 0){
            $this->decrement('available_copies');
            return true;
        }
        return false;
    }

    public function incrementStock()
    {
        if($this->available_copies < $this->total_copies){
            $this->increment('available_copies');
            return true;
        }
        return false;
    }



}
