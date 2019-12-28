<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'files', 'address', 'date', 'term', 'price', 'phone', 'category_id', 'user_id'
    ];

    protected $dates = ['term', 'created_at', 'updated_at', 'deleted_at'];

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
