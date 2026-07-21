<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstantPotFunction extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'when_to_use'];
}
