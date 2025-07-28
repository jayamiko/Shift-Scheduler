<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
