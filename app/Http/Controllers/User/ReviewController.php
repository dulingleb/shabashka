<?php

namespace App\Http\Controllers\User;

use App\Func\ResponseJson;
use App\Review;
use App\Task;
use App\User;
use App\Http\Controllers\Controller;
use http\Client\Request;

class ReviewController extends Controller
{
    public function index(User $user){
        $reviews = Review::whereIn('task_id', $user->tasks->pluck('id'))->get();
        $data = [];

        foreach ($reviews as $review) {
            $data[] = Review::getReviewArray($review);
        }

        return ResponseJson::getSuccess($data, count($reviews));
    }
}
