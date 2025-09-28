<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id','sports_field_id','date','start_time','end_time','status',
        'purpose','contact_phone','approved_by','approved_at','cancel_reason',
    ];

    protected $casts = [
        'date'        => 'date',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sportsField() 
    { 
        return $this->belongsTo(SportsField::class, 'sports_field_id'); 
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BookingLog::class);
    }
    
    public function unit() 
    { 
        return $this->belongsTo(FieldUnit::class, 'field_unit_id'); 
    }

}
