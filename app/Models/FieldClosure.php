<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldClosure extends Model
{
    protected $fillable = ['sports_field_id','start_datetime','end_datetime','reason','created_by'];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    public function sportsField(): BelongsTo
    {
        return $this->belongsTo(SportsField::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
