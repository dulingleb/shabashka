<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $guarded = [];

    public function getCreatedAtAttribute($value){
        $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        return date('d ', strtotime($value)) . $months[(int)date('m', strtotime($value))-1] . ' в ' . date('H:i', strtotime($value));
    }

    public function user(){
        return $this->belongsTo(User::class)->with('company');
    }

    public function messages(){
        return $this->hasMany(ResponseMessage::class);
    }
}
