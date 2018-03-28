<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    protected $table = 'administrator';

    protected $fillable = [

        'id', 'login_id', 'first_name', 'last_name', 'gender', 'address', 'city', 'state', 'country',
        'email', 'phone_number', 'zip_code', 'dob', 'created_at', 'updated_at'
    ];
}
