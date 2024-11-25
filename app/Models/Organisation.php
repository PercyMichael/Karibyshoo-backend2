<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    //
    protected $fillable = ['name', 'location'];

    public function guests(): HasMany
    {

        return $this->hasMany(Guest::class);
    }


    // public function members(): BelongsToMany
    // {

    //     return $this->belongsToMany(User::class);
    // }

    public function users(): BelongsToMany
    {

        return $this->belongsToMany(User::class);
    }
}
