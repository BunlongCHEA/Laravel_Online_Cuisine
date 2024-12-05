<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuisine extends Model
{
    use HasFactory;

    // Specify which attributes can be assigned
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'image',
        'price'
    ];

    /**
     * Get the category associated with the cuisine.
     */
    public function category() {
        return $this->belongsTo(Category::class);
    }
}
