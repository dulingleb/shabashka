@extends('layouts.app')

@section('content')
    <div class="container pr-0 pl-0 pr-sm-3 pl-sm-3">

        @if(session()->has('message'))
            <div class="alert alert-success mb-3">{{ session()->get('message') }}</div>
        @elseif($errors->any())
            <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
        @endif

        <div class="tasks bg-white rounded border border-light p-2">
            @forelse($tasks as $task)
                <div class="row task border-bottom m-0">
                    <div class="col-md-9">
                        <h3 class="title"><a href="/tasks/{{ $task->id }}" class="text-decoration-none">{{ $task->title }}</a> <small>{{ $task->created_at }}</small></h3>
                        <p class="description">{{ (strlen($task->description)>150) ? substr($task->description, 0, 150) . '...' : $task->description }}</p>
                        <div class="info">
                            <i class="far fa-clock"></i> до {{ $task->term }}
                            <i class="far fa-folder ml-3"></i> {{ $task->category->title }}
                            <i class="far fa-star ml-3"></i> Откликов - {{ count($task->responses) }}
                            <a href="javascript:;" class="ml-3 text-danger" onclick="if(confirm('Вы действительно хотите удалить задание?')) $(this).next().submit(); else return false;"><i class="far fa-trash-alt"></i> Удалить</a>
                            <form action="{{ route('user.tasks.destroy', ['id' => $task->id]) }}" method="post" class="d-none remove-task">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <span class="price">{{ $task->price > 0 ? number_format($task->price, 0, '.', ' ') . ' руб.' : 'договорная'  }}</span>
                        <a href="{{ route('user.tasks.edit', ['id' => $task->id]) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Редактировать</a>
                    </div>
                </div>
            @empty
                <h2 class="display-4 text-center">Заданий пока нет</h2>
                <div class="text-center text-secondary">Но мы ждем, что появятся =)</div>
            @endforelse
        </div>
    </div>
@endsection
