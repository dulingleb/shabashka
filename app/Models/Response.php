<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class)->with('company');
    }

    public function messages(){
        return $this->hasMany(ResponseMessage::class);
    }

    public function task(){
        return $this->belongsTo(Task::class);
    }
}
