<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldUnit extends Model
{
    protected $fillable = ['sports_field_id','name','index','status','capacity'];

    public function field() { return $this->belongsTo(SportsField::class, 'sports_field_id'); }
    public function bookings() { return $this->hasMany(Booking::class, 'field_unit_id'); }
    public function closures() { return $this->hasMany(FieldClosure::class, 'field_unit_id'); }
}
