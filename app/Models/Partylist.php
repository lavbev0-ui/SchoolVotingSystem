<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partylist extends Model
{
    protected $fillable = ['name', 'description', 'logo_path'];
}