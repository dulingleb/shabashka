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
        return $this->hasOne(Company::class)->with('categories');
    }

    public function reviews(){
        return $this->hasMany(Review::class)->with('task');
    }

    public function getRateAttribute(){
        if ($this->reviews()->exists()){
            $data = [
                'assessment' => Review::where('user_id', $this->id)->avg('assessment'),
                'count_assessment' => Review::where('user_id', $this->id)->count(),
                'count_done' => Task::where('executor_id', $this->id)->where('status', 'success')->count()
            ];
        } else {
            $data = [
                'assessment' => 0,
                'count_assessment' => 0,
                'count_done' => 0
            ];
        }
        return $data;
    }
}
