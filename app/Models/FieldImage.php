<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldImage extends Model
{
    protected $fillable = ['sports_field_id','path','is_cover'];

    public function sportsField(): BelongsTo
    {
        return $this->belongsTo(SportsField::class);
    }
}
