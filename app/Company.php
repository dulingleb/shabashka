<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['user_id', 'is_active', 'is_moderate', 'inn', 'description', 'title', 'address', 'documents'];

    public function categories(){
        return $this->morphToMany('App\Category', 'categoriable');
    }
}
