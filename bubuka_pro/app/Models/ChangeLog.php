<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeLog extends Model
{
    use HasFactory, softDeletes;

    protected $table = 'changes';
    protected $guarded = false;
}
