<?php

namespace App\Http\Controllers\User;

use App\Func\ResponseJson;
use App\Response;
use App\ResponseMessage;
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

        foreach ($task->responses as $response){
             $data[] = $this->getResponseArray($response, true);
        }
        
        return ResponseJson::getSuccess($data, $task->responses()->count());
    }

    public function store(Task $task, Request $request){
        if ($task->user_id === \auth('api')->id()){
            return ResponseJson::getError(['message' => 'Нельзя отсавить отклик самому себе']);
        } elseif ($task->status !== 'search_executor') {
            return ResponseJson::getError(['message' => 'Нельзя отсавить отклик на это задание, т.к. исполнитель уже найден']);
        } elseif (Response::where('user_id', \auth('api')->id())->where('task_id', $task->id)->exists()) {
            return ResponseJson::getError(['message' => 'Вы уже оставляли отклик на это задание']);
        }

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

        $data = $this->getResponseArray($response);

        return ResponseJson::getSuccess($data);
    }

    private function getResponseArray(Response $response, $withMessage = false) {
        if ($withMessage) {
            $_messages = ResponseMessage::where('response_id', $response->id)
                ->orWhere('user_id', $response->user_id)
                ->orWhere('user_id', $response->task->user_id)
                ->get();

            $messages = [];
            foreach ($_messages as $message){
                $messages[] = [
                    'id' => $message->id,
                    'response_id' => $message->responsee_id,
                    'user_id' => $message->user_id,
                    'text' => $message->text,
                    'created_at' => $message->created_at
                ];
            }
        }

        $data = [
            'id' => $response->id,
            'text' => $response->text,
            'price' => $response->price,
            'created_at' => $response->created_at,
            'user' => [
                'id' => $response->user_id,
                'title' => $response->user->title,
                'logo' => $response->user->logo,
                'rate' => $response->user->rate
            ],
            'messages' => $messages ?? []
        ];

        return $data;
    }
}
