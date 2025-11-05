<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'key',
        'value',
    ];
}