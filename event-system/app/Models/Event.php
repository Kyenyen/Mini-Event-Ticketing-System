<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'capacity',
    ];

    public function isFull(): bool
    {
        $capacity = $this->capacity ?? 0;
        $count = $this->rsvps()->where('status', 'confirmed')->count();
        return $count >= $capacity;
    }

    public function rsvps()
    {
        return $this->hasMany(Rsvp::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
