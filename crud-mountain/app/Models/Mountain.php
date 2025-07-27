<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mountain extends Model
{
    /** @use HasFactory<\Database\Factories\MountainFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'height',
        'type',
        'status',
        'difficulty',
        'routes',
        'description',
        'image_url'
    ];
    public function getImageUrlAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
