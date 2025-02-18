<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Theatre extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name',
        'rows_count',
    'seats_per_row',
        'capacity',
        'screen_type',
        'is_active',


    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
          'rows_count' => 'integer',
        'seats_per_row' => 'integer'
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_theatre')->withTimestamps();
    }
    public function getSeatMapAttribute()
    {
        $seatMap = [];
        for ($row = 0; $row < $this->rows_count; $row++) {
            $rowLetter = chr(65 + $row); // Convert 0 to A, 1 to B, etc.
            $rowSeats = [];
            for ($seat = 1; $seat <= $this->seats_per_row; $seat++) {
                $rowSeats[] = $rowLetter . $seat;
            }
            $seatMap[$rowLetter] = $rowSeats;
        }
        return $seatMap;
    }

    public function isSeatValid($seatNumber)
    {
        if (strlen($seatNumber) < 2) {
            return false;
        }

        $row = strtoupper($seatNumber[0]);
        $number = (int)substr($seatNumber, 1);

        // Check if row is valid (A-Z)
        if ($row < 'A' || $row > chr(64 + $this->rows_count)) {
            return false;
        }

        // Check if seat number is valid
        if ($number < 1 || $number > $this->seats_per_row) {
            return false;
        }

        return true;
    }
}
