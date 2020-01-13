<?php

namespace App\Http\Controllers;

use App\Category;
use App\Func\Filter;
use App\Func\ResponseJson;
use App\Response;
use App\Review;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = (new Task)->newQuery();

        if ($request->categories) {
            $categories =  json_decode($request->categories);
            $tasks = $tasks->whereIn('category_id', array_values($categories));
            $total = $tasks->count();
        } else {
            $total = Task::all()->count();
        }

        if ($request->search)
            $tasks = $tasks->where("title", 'LIKE', '%'.$request->search.'%')->orWhere('description', 'LIKE', '%'.$request->search.'%');
        if ($request->user_id)
            $tasks = $tasks->where('user_id', (int)$request->user_id);
        if ($request->status)
            $tasks = $tasks->where('status', $request->status);
        if ($request->executor_id)
            $tasks = $tasks->where('executor_id', $request->executor_id);

        $tasks = Filter::SLS($tasks, $request);

        $data = [];
        foreach ($tasks->get() as $task) {
            $data[] = $this->getTaskArray($task);
        }

        return ResponseJson::getSuccess($data, $total);
    }

    public function store(Request $request)
    {
        $this->validateTask($request);

        $task = Task::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'address' => $request->address,
            'term' => Carbon::parse($request->term),
            'price' => $request->price,
            'phone' => $request->phone,
            'user_id' => auth('api')->id(),
        ]);

        if ($request->has('files')) {
            $filesName = Task::uploadFiles($request->file('files'), $task->id);
            $task->files = $filesName;
            $task->save();
        }

        return $this->getTaskResponse($task);
    }

    public function update(Task $task, Request $request) {
        if ($task->user_id !== \auth('api')->id()) {
          return ResponseJson::getError(['message' => 'Не ваше задание']);
        }

        if ($task->status !== 'search_executor') {
          return ResponseJson::getError(['message' => 'Не ваше задание'], 401);
        }

        $this->validateTask($request);

        $task->category_id = $request->category_id;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->address = $request->address;
        $task->term = Carbon::parse($request->term);
        $task->price = $request->price;
        $task->phone = $request->phone;

        if ($request->has('files')) {
            $filesName = Task::uploadFiles($request->file('files'), $task->id);
            $task->files = $filesName;
            $task->save();
        }

        if ($request->has('files_remove')) {
            foreach ($request->input('files_remove') as $item) {
                $item = basename($item);
                if (File::exists(storage_path('app\public\tasks\\' . $task->id . '\\' . $item)))
                    File::delete(storage_path('app\public\tasks\\' .$task->id . '\\' . $item));
            }

            $task->files = array_diff($task->files, $request->input('files_remove'));
            $task->save();
        }

        return $this->getTaskResponse($task);
    }

    public function show(Task $task) {
        return $this->getTaskResponse($task);
    }

    public function setExecutor(Task $task, Request $request){
        if ($task->user_id !== \auth('api')->id()){
            return ResponseJson::getError(['message' => 'Это не ваше задание']);
        }

        $request->validate([
            'executor_id' => 'required|integer|exists:users,id'
        ]);

        $task->executor_id = $request->executor_id;
        $task->status = 'doing';
        $task->save();

        return $this->getTaskResponse($task);
    }

    public function done(Task $task, Request $request){
        if ($task->status !== 'doing') {
            return ResponseJson::getError(['message' => 'Статус должен быть "в работе"']);
        } elseif ($task->user_id !== \auth('api')->id()){
            return ResponseJson::getError(['message' => 'Это не ваше задание']);
        }

        $request->validate([
            'assessment' => 'required|integer|between:1,5',
            'text' => 'required|string|min:5|max:1000'
        ]);

        $review = Review::create([
            'author_id' => auth('api')->id(),
            'user_id' => $task->executor_id,
            'task_id' => $task->id,
            'text' => $request->text,
            'assessment' => $request->assessment
        ]);

        $task->status = 'done';
        $task->save();

        $data = Review::getReviewArray($review);

        return ResponseJson::getSuccess($data);
    }

    public function destroy(Task $task) {
        if ($task->user_id === auth('api')->id() && $task->executor_id === null) {
            if (Storage::exists('public\tasks\\' . $task->id))
                Storage::deleteDirectory('public\tasks\\' . $task->id);
            $task->delete();

            return response()->json([
                'success' => true
            ], 200);
        } elseif ($task->executor_id !== null) {
          return ResponseJson::getError(['message' => 'Нельзя удалить задание, если уже назначен исполнитель']);
        } else {
          return ResponseJson::getError(['message' => 'Что-то пошло не так или ты хаккер']);
        }
    }

    public function categories() {
      $categories = Category::all();
      $total = $categories->count();
      return ResponseJson::getSuccess($categories, $total);
    }

    private function getTaskResponse($task) {
      $data = $this->getTaskArray($task);
      return ResponseJson::getSuccess($data);
    }

    private function getTaskArray(Task $task) {
      $data = [
          'id' => $task->id,
          'title' => $task->title,
          'description' => $task->description,
          'address' => $task->address,
          'phone' => $this->getTaskPhone($task),
          'files' => $task->files,
          'price' => $task->price,
          'category_id' => $task->category_id,
          'user_id' => $task->user_id,
          'user_title' => $task->user->title,
          'executor_id' => $task->executor_id,
          'status' => $task->status,
          'term' => $task->term,
          'created_at' => $task->created_at,
      ];

      return $data;
    }

    private function getTaskPhone($task) {
      return (\auth('api')->id() === $task->user_id || \auth('api')->id() === $task->executor_id) ? $task->phone : null;
    }

    private function validateTask($request) {
      $request->validate([
          'category_id' => [
              'required',
              function($attribute, $value, $fail) {
                  $category = Category::find($value);
                  if (!$category || $category->parent==0) return $fail('Неверная категория');
                  else
                      return true;
              },
          ],
          'title' => 'required|string|min:5|max:100',
          'description' => 'required|string|min:20|max:1000',
          'address' => 'nullable|string|min:5|max:255',
          'term' => 'required|date|date-format:Y-m-d\TH:i:s.\0\0\0\Z|after:yesterday',
          'price' => 'required|numeric|min:0|max:999999999',
          'phone' => 'required|string|size:18',
          'files' => 'array',
          'files.*' => 'file|mimes:jpeg,png,jpg,doc,docx,xls,xlsx,pdf,rtf|max:4096'
      ]);
    }
}
