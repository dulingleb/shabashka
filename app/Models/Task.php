<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'files', 'address', 'date', 'term', 'price', 'phone', 'category_id', 'user_id'
    ];

    protected $dates = ['term', 'created_at', 'updated_at', 'deleted_at'];


    public static function uploadFiles($files, $task_id){
        if (!File::exists(storage_path('app/public/tasks/' . $task_id))) {
            File::makeDirectory(storage_path('app/public/tasks/' . $task_id));
        }

        $filesName = [];
        foreach ($files as $file){
            $name = $file->getClientOriginalName();
            if (Storage::exists('public/tasks/' . $task_id . '/' . $name)){
                $item = 1;
                do {
                    $name = substr($name, 0, strrpos($name, ".")) . '_' . $item . '.' . $file->getClientOriginalExtension();
                    $item++;
                } while(Storage::exists('public/tasks/' . $task_id . '/' . $name));
            }

            $path = $file->move(storage_path('app/public/tasks/' . $task_id . '/'), $name );
            $filesName[] = basename($name);
        }

        return $filesName;
    }

    public function getFilesAttribute($value){
        if (!$value) return null;
        $_v = unserialize($value);
        $files = [];
        foreach ($_v as $file){
            $files[] = '/storage/tasks/' . $this->id . '/' . $file;
        }

        return $files;
    }

    public function setFilesAttribute($value){
        if (is_array($value)){
            $_value = [];
            foreach ($value as $item)
                $_value[] = basename($item);
            $this->attributes['files'] = serialize($_value);
        } else {
            $this->attributes['files'] = $value;
        }
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
