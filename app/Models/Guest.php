<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guest extends Model
{
    //
    protected $fillable = [
        'name',
        'gender',
        'phone',
        'email',
        'nin',
        'nationality',
        'reason',
        'temperature',
        'organisation_id',
        'user_id',
    ];


    public function organisation(): BelongsTo
    {

        return $this->belongsTo(Organisation::class);
    }

    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class);
    }
}
