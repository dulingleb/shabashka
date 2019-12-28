<?php

namespace App\Http\Controllers\User;

use App\Response;
use App\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ResponseController extends Controller
{
    public function store(Task $task, Request $request){
        if($task->user_id === \auth('api')->id())
            return response()->json([
                'success' => false,
                'message' => 'Нельзя отсавить отклик самому себе'
            ], 419);
        elseif($task->status !== 'search_executor')
            return response()->json([
                'success' => false,
                'message' => 'Нельзя отсавить отклик на это задание, т.к. исполнитель уже найден'
            ], 401);
        elseif(Response::where('user_id', \auth('api')->id())->where('task_id', $task->id)->exists())
            return response()->json([
                'success' => false,
                'message' => 'Вы уже оставляли отклик на это задание'
            ], 401);

        $request['price'] = preg_replace('/\D/', '', $request->price);
        $request->validate([
            'text' => 'required|string|max:1000',
            'price' => 'required|numeric|min:1|max:99999999'
        ]);

        Response::create([
            'user_id' => \auth('api')->id(),
            'task_id' => $task->id,
            'text' => $request->input('text'),
            'price' => $request->input('price')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Отклик успешно опубликован!'
        ], 200);
    }
}
