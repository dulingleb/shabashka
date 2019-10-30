<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'files', 'address', 'date', 'term', 'price', 'phone', 'category_id', 'user_id'
    ];

    public static function dateTerm($value){
        $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        return date('d ', strtotime($value)) . $months[(int)date('m', strtotime($value))-1] . date(' Y', strtotime($value));
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function responses(){
        return $this->hasMany(Response::class)->with('user')->with('messages');
    }
}
