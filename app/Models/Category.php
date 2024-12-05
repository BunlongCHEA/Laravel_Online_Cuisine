<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Specify which attributes can be assigned
    protected $fillable = [
        'name'
    ];

    /**
     * Get the cuisines for the category.
     */
    public function cuisines() {
        return $this->hasMany(Cuisine::class);
    }
}
