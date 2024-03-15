<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshTokens extends Model
{
    use HasFactory;
    protected $table ='refresh_tokens';
    protected $guarded = false;
}
