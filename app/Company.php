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
            $files[] = '/storage/users/' . $this->user_id . '/' . $file;
        }

        return $files;
    }

    public function setDocumentsAttribute($value){

        if(is_array($value)){
            $_value = [];
            foreach ($value as $item)
                $_value[] = basename($item);
            $this->attributes['documents'] = serialize($_value);
        } else
            $this->attributes['documents'] = $value;

    }

    public function categories(){
        return $this->morphToMany('App\Category', 'categoriable');
    }
}
