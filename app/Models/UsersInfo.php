<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersInfo extends Model
{
    use HasFactory;

    // Specify exact table name because pluralization won't work properly
    protected $table = 'users_info';

    // Agar aapke table mein timestamps nahi hain (created_at, updated_at), toh:
    public $timestamps = false;

    // Fillable fields define karein jo aap mass assign karna chahte hain
    protected $fillable = [
        'userId',
        'fullname',
        'address',
        'phone',
        'passwordhint',
        'zipcode',
        'landmark',
        // jitne bhi columns hain
    ];

    // Optional: User ke saath relation (agar User model hai)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
