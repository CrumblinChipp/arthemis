<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteEntry extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'building_id',
        'residual_kg',
        'recyclable_kg',
        'biodegradable_kg',
        'infectious_kg',
        'is_read',
    ];

    public $timestamps = false;

    // Relationships
    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
