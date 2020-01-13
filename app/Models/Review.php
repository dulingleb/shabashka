<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['author_id', 'user_id', 'task_id', 'assessment', 'text'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function author() {
        return $this->belongsTo(User::class);
    }

    public function task() {
        return $this->belongsTo(Task::class)->with('user');
    }

    protected static function getReviewArray(Review $review){
        $data = [
            'id' => $review->id,
            'author' => [
                'id' => $review->author->id,
                'title' => $review->author->title,
                'logo' => $review->author->logo
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
