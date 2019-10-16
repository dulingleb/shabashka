@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="p-3">
            <div class="row">
                <div class="col-4 col-md-2">
                    <div class="user-logo mx-auto" style="background: url('{{ $user->logo }}') center center">
                        @if(!$user->logo)
                            <div class="text-logo">
                                {{ mb_substr($user->name, 0, 1) }}{{  mb_substr($user->surname, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-8 col-md-10">
                    <h4>{{ ($user->company && $user->company->is_active==1) ? $user->company->title : $user->name . ' ' . $user->surname }}</h4>
                    <div class="info">
                        <span class="text-success mr-3" title="Положительных отзывов"><i class="far fa-thumbs-up"></i> - {{ $user->positive_assessments_count }}</span>
                        <span class="text-danger mr-3" title="Отрицательных отзывов"><i class="far fa-thumbs-down"></i> - {{ $user->negative_assessments_count }}</span>
                    </div>
                    @if($user->company && $user->company->is_active==1)
                        <div class="description mt-3">
                            {{ $user->company->description }}
                        </div>
                        <div class="mt-3">
                            <b>На сервисе: </b> {{ $user->on_service }} 
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <hr class="mt-5">

        <h2 class="mt-5">Отзывы</h2>

        @forelse($user->reviews as $review)
            <div class="@if($review->assessment==0) border-danger @else border-success @endif mt-3 rounded bg-white border p-3 review">
                <div class="row">
                    <div class="col-4 col-md-2">
                        <div class="user-logo mx-auto" style="background: url('{{ $review->task->user->logo }}') center center">
                            @if(!$review->task->user->logo)
                                <div class="text-logo">
                                    {{ mb_substr($review->task->user->name, 0, 1) }}{{  mb_substr($review->task->user->surname, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-8 col-md-10 position-relative">
                        <h4 class="mb-1"><a href="{{ route('tasks.show', ['task' => $review->task_id]) }}">{{ $review->task->title }}</a> <i class="ml-1 far finger @if($review->assessment == 0) fa-thumbs-down text-danger @else fa-thumbs-up text-success @endif"></i></h4>
                        <div class="info text-secondary mb-3" style="font-size: 12px;">
                            <span title="Заказчик" class="mr-2"><i class="fas fa-user"></i> {{ $review->task->user->name . ' ' . $review->task->user->surname }}</span>
                            <span title="Категория"><i class="far fa-folder"></i> {{ $review->task->category->title }}</span>
                        </div>
                        {{ $review->text }} <br>
                        <span class="text-secondary"><small>{{ date('d.m.Y', strtotime($review->created_at)) }}</small></span>
                    </div>
                </div>
            </div>
        @empty

        @endforelse
    </div>
@endsection
