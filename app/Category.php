<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{


    public static function getAllCategories(){
        $categories = [];
        foreach(Category::where('parent', 0)->get() as $category){
            $child = [];
            foreach (Category::where('parent', $category['id'])->get() as $childCategory){
                $child[] = ['title' => $childCategory['title'], 'id' => $childCategory['id']];
            }
            $categories[] = [
                'parent' => $category['title'],
                'child' => $child
            ];
        }
        return $categories;
    }

    public function companies(){
        return $this->morphedByMany('App\Company', 'categoriable');
    }

    public function tasks(){
        return $this->morphedByMany('App\Task', 'categoriable');
    }

}
