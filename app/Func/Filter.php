<?php


namespace App\Func;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Filter
{
    public static function SLS(Builder $builder, Request $request){
        $builder->skip(isset($request->start) ? (int)$request->start : 0);
        $builder->take(isset($request->limit) ? (int)$request->limit : 9999999999);
        if(isset($request->sort)) {
            $sorts = json_decode($request->sort, true);
            foreach ($sorts as $sort )
                $builder->orderBy($sort['property'], $sort['direction'] ?? 'ASC');
        }
        return $builder;
    }
}