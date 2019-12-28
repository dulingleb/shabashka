<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Company;
use App\Response;
use App\Review;
use App\Task;
use App\User;
use Carbon\Carbon;
use Faker\Provider\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class TaskController extends Controller
{

    public function setExecutor(Response $response){
        $task = Task::find($response->task_id);
        if(!$task || $task->user_id !== Auth::id() || $task->executor != null) return redirect()->back()->withErrors(['text_error' => 'Что-то пошло не так']);

        $task->executor = $response->user_id;
        $task->status = 'in_work';
        $task->save();

        $response->load('user');

        return redirect()->back()->with(['message' => (($response->user->company && $response->user->company->is_active==1) ? $response->user->company->title : $response->user->name . ' ' . $response->user->surname) . ' успешно назначен исполнителем!' ]);
    }

    public function addReview(Task $task, Request $request) {
        $request->validate([
            'status' => [
                function($attribute, $value, $fail){
                    if($value !== 'success' && $value !== 'close') $fail('Неверный статус!');
                }
            ],
            'assessment' => 'required|numeric|min:0|max:1',
            'text' => 'nullable|string|max:1000'
        ]);

        if($task->user_id !== Auth::id()) return redirect()->back()->withErrors(['text_error' => 'Что-то пошло не так']);

        $task->status = $request->get('status');
        $task->save();

        if($task->status == 'success'){
            $review = new Review;
            $review->user_id = $task->executor;
            $review->task_id = $task->id;
            $review->text = $request->text;
            $review->assessment = $request->assessment;
            $review->save();
            $message = 'Отзыв успешно добавлен';
        } else
            $message = 'Статуc задания установлено "не выполнено"';

        return redirect()->back()->with(['message' => $message]);

    }
}
