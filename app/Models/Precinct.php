<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Precinct extends Model
{
    use HasFactory;
    protected $fillable=['code', 'name', '34A', 'link', 'county_id', 'constituency_id', 'ward_id', 'rao', 'wsr', 'wajk', 'mwaure'];
}

