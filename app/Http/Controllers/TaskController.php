<?php

namespace App\Http\Controllers;

use App\Func\Filter;
use App\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = (new Task)->newQuery();

        if($request->categories) {
            $categories =  json_decode($request->categories);
            $tasks = $tasks->whereIn('category_id', array_values($categories));
            $total = $tasks->count();
        } else {
            $total = Task::all()->count();
        }

        $tasks = Filter::SLS($tasks, $request);

        $data = [];
        foreach ($tasks->get() as $task){
            $data[] = [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'price' => $task->price,
                'category' => $task->category,
                'created_at' => time_ago($task->created_at),
                'term' => date_month_ru($task->term)
            ];
        }

        return response()->json([
            "success" => true,
            "data" => $data,
            "total" => $total
        ]);
    }

}
