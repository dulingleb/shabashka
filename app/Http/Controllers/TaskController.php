<?php

namespace App\Http\Controllers;

use App\Category;
use App\Func\Filter;
use App\Response;
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

        $tasks = Filter::SLS($tasks, $request);

        $data = [];
        foreach ($tasks->get() as $task) {
            $data[] = $this->get_task_array($task);
        }

        return $this->get_response_success($data, $total);
    }

    public function store(Request $request)
    {
        $this->validate_task($request);

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

        return $this->get_task_response($task);
    }

    public function update(Task $task, Request $request) {
        if ($task->user_id !== \auth('api')->id()) {
          return $this->get_response_err(['message' => 'Не ваше задание']);
        }

        if ($task->status !== 'search_executor') {
          return $this->get_response_err(['message' => 'Не ваше задание'], 401);
        }

        $this->validate_task($request);


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

        return $this->get_task_response($task);
    }

    public function show(Task $task) {
        $task->load(['responses' => function($q) {
            $q->selectRaw('responses.*, (SELECT executor_id FROM tasks WHERE responses.user_id=tasks.executor_id) as executor_id')->orderBy('executor_id', 'DESC');
        }])->load('category')->load('user');

        if ($task->executor_id == null || $task->executor_id === Auth::id())
            $myResponse = Response::with('user')->with('messages')->where('user_id', Auth::id())->where('task_id', $task->id)->first();

        return $this->get_task_response($task);
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
          return $this->get_response_err(['message' => 'Нельзя удалить задание, если уже назначен исполнитель']);
        } else {
          return $this->get_response_err(['message' => 'Что-то пошло не так или ты хаккер']);
        }
    }

    public function categories() {
      $categories = Category::all();
      $total = $categories->count();
      return $this->get_response_success($categories, $total);
    }

    private function get_task_response($task) {
      $data = $this->get_task_array($task);
      return $this->get_response_success($data);
    }

    private function get_task_array($task) {
      $data = [
          'id' => $task->id,
          'title' => $task->title,
          'description' => $task->description,
          'address' => $task->address,
          'phone' => $this->get_task_phone($task),
          'files' => $task->files,
          'price' => $task->price,
          'category_id' => $task->category_id,
          'user_id' => $task->user_id,
          'user_title' => $this->get_user_title($task),
          'executor_id' => $task->executor_id,
          'status' => $task->status,
          'term' => $task->term,
          'created_at' => $task->created_at,
      ];

      return $data;
    }

    private function get_user_title($task) {
      return $task->user->company()->exists() && $task->user->company->is_active && $task->user->company->moderate_status === 'success'
              ? $task->user->company->title
              : $task->user->lastname . ' ' . $task->user->name;
    }

    private function get_task_phone($task) {
      return (\auth('api')->id() === $task->user_id || \auth('api')->id() === $task->executor_id) ? $task->phone : null;
    }

    private function validate_task($request) {
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

    private function get_response_success($data = [], $total = 1, $status = 200) {
      return $this->get_response(true, $data, [], $total, $status);
    }

    private function get_response_err($error = [], $status = 419) {
      return $this->get_response(true, [], $error, 1, $status);
    }

    private function get_response($success, $data = [], $error = [], $total = 1, $status = 200) {
      $response['success'] = $success;
      if ($success) {
        $response['data'] = $data;
        $response['total'] = $total;
      } else {
        $response['error'] = $error;
      }

      return response()->json($response, $status);
    }

}
