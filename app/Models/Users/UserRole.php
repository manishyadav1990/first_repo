<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'userrole';

    protected $fillable = [

        'id','role_name','created_at','updated_at'
    ];
}
