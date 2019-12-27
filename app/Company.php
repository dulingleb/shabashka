<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['user_id', 'is_active', 'is_moderate', 'inn', 'description', 'title', 'address', 'documents'];

    public function getDocumentsAttribute($value){
        if(!$value) return null;
        $_v = unserialize($value);
        $files = [];
        foreach ($_v as $file){
            $files[] = 'storage/users/' . $this->user_id . '/' . $file;
        }

        return json_encode($files);
    }

    public function categories(){
        return $this->morphToMany('App\Category', 'categoriable');
    }
}
