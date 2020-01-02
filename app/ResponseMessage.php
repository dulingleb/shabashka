<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponseMessage extends Model
{
    protected $fillable = ['response_id', 'user_id', 'text'];
}
