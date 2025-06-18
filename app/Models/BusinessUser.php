<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class BusinessUser extends Authenticatable
{
  protected $table = 'business_user';
}