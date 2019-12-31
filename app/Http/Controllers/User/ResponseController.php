<?php

namespace App\Http\Controllers\User;

use App\Response;
use App\Review;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ResponseController extends Controller
{
    public function index(Task $task){
        $data = [];
        foreach ($task->responses as $respons){
            $data[] = [
                'id' => $respons->id,
                'text' => $respons->text,
                'price' => $respons->price,
                'created_at' => Carbon::parse($respons->created_at),
                'user' => [
                    'id' => $respons->user_id,
                    'name' => ($respons->user->company()->exists() && $respons->user->company->is_active && $respons->user->company->moderate_status === 'success') ? $respons->user->company->title : $respons->user->lastname . ' ' . $respons->user->name,
                    'logo' => $respons->user->logo,
                    'rate' => [
                        'assessment' => Review::where('user_id', $respons->user->id)->avg('assessment'),
                        'count_assessment' => Review::where('user_id', $respons->user->id)->count(),
                        'count_done' => Task::where('executor_id', $respons->user->id)->where('status', 'success')->count()
                    ]
                ],
            ];
        }
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $task->responses()->count()
        ], 200);
    }

    public function store(Task $task, Request $request){
        if($task->user_id === \auth('api')->id())
            return response()->json([
                'success' => false,
                'error' => ['message' => 'Нельзя отсавить отклик самому себе']
            ], 419);
        elseif($task->status !== 'search_executor')
            return response()->json([
                'success' => false,
                'error' => ['message' => 'Нельзя отсавить отклик на это задание, т.к. исполнитель уже найден']
            ], 401);
        elseif(Response::where('user_id', \auth('api')->id())->where('task_id', $task->id)->exists())
            return response()->json([
                'success' => false,
                'error' => ['message' => 'Вы уже оставляли отклик на это задание']
            ], 401);

        $request['price'] = preg_replace('/\D/', '', $request->price);
        $request->validate([
            'text' => 'required|string|max:1000',
            'price' => 'required|numeric|min:1|max:99999999'
        ]);

        $response = Response::create([
            'user_id' => \auth('api')->id(),
            'task_id' => $task->id,
            'text' => $request->input('text'),
            'price' => $request->input('price')
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $response->user_id,
                'task_id' => $response->task_id,
                'text' => $response->text,
                'price' => $response->price,
            ]
        ], 200);
    }
}
