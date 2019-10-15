<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title', 'description', 'files', 'address', 'date', 'term', 'price', 'phone', 'category_id', 'user_id'
    ];

    public function getCreatedAtAttribute($value)
    {
        return self::timeAgoNaive($value);
    }

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

    public static function timeAgoNaive ($comparisonDate) {
        $timeparts = [
            ['name' => 'год', 'name1' => 'года', 'name2' => 'лет', 'div' => 31536000, 'mod' => 10000],
            ['name'=> 'день', 'name1' => 'дня',  'name2'=> 'дней', 'div' => 86400, 'mod' => 365],
            ['name'=> 'час', 'name1' => 'часа', 'name2'=> 'часов', 'div' => 3600, 'mod' => 24],
            ['name'=> 'минуту', 'name1' => 'минуты', 'name2'=> 'минут', 'div' => 60, 'mod' => 60],
        ];

        $i = 0;
        $name = '';
        $values = '';
        $interval = time() - strtotime($comparisonDate);

        while ($i < 4) {
            $calc = round($interval / $timeparts[$i]['div'], 0) % $timeparts[$i]['mod'];

            if ($calc > 0) {
                $n = abs($calc) % 100;
                $n1 = $calc % 10;
                if ($n > 10 && $n < 20) $name = $timeparts[$i]['name2'];
                elseif (($n1 > 4 && $n1 < 10) || $n1==0) $name = $timeparts[$i]['name2'];
                elseif ($n1 > 1 && $n1 < 5) $name = $timeparts[$i]['name1'];
                elseif ($n1 == 1) $name = $name = $timeparts[$i]['name'];

                $values .= $calc . ' ' . $name . ' ';
                break;
            }
            $i += 1;
        }

        if (!$values) $values = 'пару секунд ';

        return $values . 'назад';
    }
}
