<?php

namespace App;

use Caffeinated\Shinobi\Traits\ShinobiTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Mpociot\Teamwork\Traits\UserHasTeams;

class User extends Authenticatable
{
    use UserHasTeams, ShinobiTrait;

    protected $fillable = [
        'name', 'email', 'password',
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];
}
