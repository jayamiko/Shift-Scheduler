<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'start_time', 'end_time', 'location', 'role_id', 'assigned_to'
    ];    

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function requests()
    {
        return $this->hasMany(ShiftRequest::class);
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Jakarta')->toDateString(); 
    }

    public function getStartTimeAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Jakarta')->format('H:i');
    }

    public function getEndTimeAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Jakarta')->format('H:i');
    }
}
