<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'surname', 'email', 'password', 'logo', 'phone'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendsReviews(){
        return $this->hasMany(Review::class);
    }

    public function company(){
        return $this->hasOne(Company::class)->with('categories');
    }

    public function tasks(){
        return $this->hasMany(Task::class);
    }

    public function getRateAttribute(){

        $data = [
            'assessment' => Review::whereIn('task_id', $this->tasks->pluck('id'))->avg('assessment') ?? 0,
            'count_assessment' => Review::where('task_id', $this->tasks->pluck('id'))->count(),
            'count_done' => Task::where('executor_id', $this->id)->where('status', 'success')->count()
        ];

        return $data;
    }

    public function getTitleAttribute() {
        return $this->company()->exists() && $this->company->is_active && $this->company->moderate_status === 'success'
            ? $this->company->title
            : $this->lastname . ' ' . $this->name;
    }
}
