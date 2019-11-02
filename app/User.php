<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'logo', 'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company(){
        return $this->hasOne(Company::class);
    }

    public function reviews(){
        return $this->hasMany(Review::class)->with('task');
    }

    public function getPositiveAssessmentsCountAttribute(){
        return $this->reviews()->where('assessment', 1)->count();
    }

    public function getNegativeAssessmentsCountAttribute(){
        return $this->reviews()->where('assessment', 0)->count();
    }

    public function getOnServiceAttribute(){
        $timeparts = [
            ['name' => 'год', 'name1' => 'года', 'name2' => 'лет', 'div' => 31536000, 'mod' => 10000],
            ['name'=> 'день', 'name1' => 'дня',  'name2'=> 'дней', 'div' => 86400, 'mod' => 365],
            ['name'=> 'час', 'name1' => 'часа', 'name2'=> 'часов', 'div' => 3600, 'mod' => 24],
            ['name'=> 'минуту', 'name1' => 'минуты', 'name2'=> 'минут', 'div' => 60, 'mod' => 60],
        ];

        $i = 0;
        $name = '';
        $values = '';
        $interval = time() - strtotime($this->created_at);

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

        return $values;
    }
}
