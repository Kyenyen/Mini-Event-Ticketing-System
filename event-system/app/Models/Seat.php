<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'label', 'status'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function rsvp()
    {
        return $this->hasOne(Rsvp::class);
    }

}
