<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'task_id', 'assessment', 'text'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function task() {
        return $this->belongsTo(Task::class)->with('user');
    }

    protected static function getReviewArray(Review $review){
        $data = [
            'id' => $review->id,
            'user' => [
                'id' => $review->user->id,
                'title' => $review->user->title,
                'logo' => $review->user->logo
            ],
            'task' => [
                'id' => $review->task->id,
                'title' => $review->task->title
            ],
            'text' => $review->text,
            'assessment' => $review->assessment,
            'created_at' => $review->created_at
        ];

        return $data;
    }
}
