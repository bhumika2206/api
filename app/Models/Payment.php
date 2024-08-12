<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;


class Payment extends Model
{
    use HasFactory, SoftDeletes;

    public function User(){
        return $this->belongsTo(User::class);
    }
}
