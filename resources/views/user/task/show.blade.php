@extends('layouts.app')

@section('content')
    <div class="container pr-0 pl-0 pr-sm-3 pl-sm-3">

        @if(session()->has('message'))
            <div class="alert alert-success mb-3">{{ session()->get('message') }}</div>
        @elseif($errors->any())
            @if($errors->has('text_error'))
                <div class="alert alert-danger mb-3">{{ $errors->first('text_error') }}</div>
            @else
                <div class="alert alert-danger mb-3">Исправьте ошибки и попробуйте снова</div>
            @endif
        @endif



        <div class="bg-white rounded border-light p-3 task show">
            <h1 class="">{{ $task->title }}</h1>
            <div class="info mb-4 mt-4">
                <span class="bg-success p-1 pr-2 pl-2 rounded text-white d-inline-block mt-1"><i class="fas fa-money-bill-wave"></i> {{ $task->price > 0 ? number_format($task->price, 0 , '.', ' ') . ' руб' : 'договорная' }}</span>
                <a href="/?cat%5B%5D={{ $task->category->id }}" class="bg-primary p-1 pr-2 pl-2 rounded text-white d-inline-block mt-1"><i class="far fa-folder"></i> {{ $task->category->title }}</a>
                <span class="bg-secondary p-1 pr-2 pl-2 rounded text-white d-inline-block mt-1"><i class="far fa-clock"></i> {{ $task->created_at }}</span>
                @switch($task->status)
                    @case('wait')
                        <span class="bg-info p-1 pr-2 pl-2 rounded text-white d-inline-block mt-1" title="Статус"><i class="fas fa-search"></i> Поиск иполнителя</span>
                    @break
                    @case('in_work')
                        <span class="bg-info p-1 pr-2 pl-2 rounded text-white d-inline-block mt-1" title="Статус"><i class="fas fa-tools"></i> В работе</span>
                    @break
                    @case('close')
                        <span class="bg-danger p-1 pr-2 pl-2 rounded text-white d-inline-block mt-1" title="Статус"><i class="fas fa-minus"></i> Не выполнен</span>
                    @break
                    @case('success')
                        <span class="bg-success p-1 pr-2 pl-2 rounded text-white d-inline-block mt-1" title="Статус"><i class="fas fa-check"></i> Выполнен</span>
                    @break
                @endswitch

                <div class="float-sm-right mt-2 mt-sm-0" style="font-size: 14px;">
                    @if(auth()->id() === $task->user_id && $task->executor === null)
                        <a href="{{ route('user.tasks.edit', ['id' => $task->id]) }}" class="m-1"><i class="far fa-edit"></i> Редактировать</a>
                        <a href="javascript:;" class="m-1 text-danger" onclick="if(confirm('Вы действительно хотите удалить задание?')) $(this).next().submit(); else return false;"><i class="far fa-trash-alt"></i> Удалить</a>
                        <form action="{{ route('user.tasks.destroy', ['id' => $task->id]) }}" method="post" class="d-none remove-task">
                            @csrf
                            @method('DELETE')
                        </form>
                    @elseif(auth()->id() === $task->user_id && $task->status=='in_work')
                        <a href="javascript:;" class="m-1" data-toggle="modal" data-target="#check-task-modal">Проверить задание</a>
                    @endif
                </div>

            </div>

            <div class="row description">
                @if($task->address)
                    <div class="col-md-2">
                        <b>Адресс:</b>
                    </div>
                    <div class="col-md-10 mb-2">
                        {{ $task->address }}
                    </div>
                @endif

                <div class="col-md-2">
                    <b>Выполнить до:</b>
                </div>
                <div class="col-md-10 mb-2">
                    {{ $task->term }}
                </div>

                <div class="col-md-2">
                    <b>Описание:</b>
                </div>
                <div class="col-md-10 mb-2">
                    {{ $task->description }}

                    @if($task->files)
                        <div class="row files m-0">
                            @foreach(json_decode($task->files, true) as $file)
                                <div class="col-6 col-md-3 p-1 d-flex align-items-center justify-content-center">
                                    <a href="{{'/storage/tasks/'.$task->id.'/'.$file}}" target="_blank" class="file-item w-100">
                                        @if( in_array(explode('.', $file)[count(explode('.', $file))-1], ['png', 'jpg', 'jpeg']) )
                                            <img src="{{'/storage/tasks/'.$task->id.'/'.$file}}" alt="{{ $task->title }}">
                                        @elseif( in_array(explode('.', $file)[count(explode('.', $file))-1], ['doc', 'docx']) )
                                            <div class="icon mt-1 mb-1">
                                                <i class="fas fa-file-word"></i>
                                            </div>
                                        @elseif( in_array(explode('.', $file)[count(explode('.', $file))-1], ['xls', 'xlsx']) )
                                            <div class="icon mt-1 mb-1">
                                                <i class="fas fa-file-excel"></i>
                                            </div>
                                        @elseif( in_array(explode('.', $file)[count(explode('.', $file))-1], ['pdf', 'rtf']) )
                                            <div class="icon mt-1 mb-1">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-md-2">
                    <b>Контакты:</b>
                </div>
                <div class="col-md-10 mb-2">
                    {{ $task->user->name }}<br>
                    @if(auth()->check() && $task->executor === auth()->id())
                        <i class="fas fa-phone"></i> {{ $task->user->phone }}
                    @else
                        <small><i class="text-secondary">Контакты заказчика видны только исполнителю</i></small>
                    @endif
                </div>
            </div>
        </div>

        @if(!auth()->check())
            <div class="mt-3 bg-white rounded border-light p-3 response">
                Чтобы оставить отклик, <a href="/login">войдите</a> в систему или <a href="/register">зарегистрируйтесь</a>.
            </div>
        @elseif(isset($myResponse))
            @if($task->executor === auth()->id())
                <div class="mt-3 bg-success text-white p-2 rounded-top">Вы выбраны исполнителем</div>
                <div class="bg-white rounded-bottom border border-success p-3 response">
            @else
                <div class="mt-3 bg-primary text-white p-2 rounded-top">Ваш отклик</div>
                <div class="bg-white rounded-bottom border border-primary p-3 response">
            @endif

                <div class="row">
                    <div class="col-4 col-md-2">
                        <div class="user-logo mx-auto" style="background: url('{{ auth()->user()->logo }}') center center">
                            @if(!auth()->user()->logo)
                                <div class="text-logo">
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}{{  mb_substr(auth()->user()->surname, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-8 col-md-10">
                        <h4>{{ ($myResponse->user->company && $myResponse->user->company->is_active) ? $myResponse->user->company->title : $myResponse->user->name . ' ' . $myResponse->user->surname }}</h4>
                        <span class="bg-success rounded price text-white">{{ number_format($myResponse->price, 0, '.', ' ') }} руб.</span>
                        <div class="info">
                            <span class="text-success mr-3" title="Положительных отзывов"><i class="far fa-thumbs-up"></i> - {{ $myResponse->user->positive_assessments_count }}</span>
                            <span class="text-danger mr-3" title="Отрицательных отзывов"><i class="far fa-thumbs-down"></i> - {{ $myResponse->user->negative_assessments_count }}</span>
                        </div>

                    </div>
                    <div class="col-md-10 offset-md-2 description">
                        {{ $myResponse->text }} <br>
                        <span class="text-secondary"><small>{{ $myResponse->created_at }}</small></span>

                        @if(count($myResponse->messages)>0)
                            <a href="javascript:;" class="text-secondary float-right" onclick="$(this).parent().parent().parent().find('.messages').slideToggle(200)">
                                <small>Сообщения ({{ count($myResponse->messages) }})</small>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="messages mt-3 p-3 border-top" style="display: none;">

                    @foreach($myResponse->messages as $message)
                        <div class="message @if($message->user_id===auth()->id()) right @else left @endif">
                            <span>{!! nl2br(e($message->text)) !!}</span>
                        </div>
                    @endforeach

                    <div style="clear: both;"></div>

                    <div class="text-center">
                        <a href="javascript:;" class="btn btn-primary mt-2"
                           onclick=" $('#message-modal input[name=response_id]').val('{{ $myResponse->id }}'); "
                           data-toggle="modal" data-target="#message-modal"
                        >Написать сообщение</a>
                    </div>

                </div>

            </div>
        @elseif(auth()->id() != $task->user_id && $task->executor == null)
            <div class="mt-3 bg-white rounded border border-light p-3 response">
                <h3>Оставить отклик:</h3>
                <form action="{{ route('user.response.add') }}" method="post">
                    @csrf
                    <textarea class="form-control  @error('text') is-invalid @enderror" rows="4" name="text" placeholder="Описание, максимум 1000 символов" required>{{ old('text') }}</textarea>
                    @error('text')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    <div class="align-items-center mt-3">
                        <label for="price" class="float-left mt-2 mr-3">Цена:</label>
                        <div class="input-group" style="max-width: 200px;">
                            <input type="text" name="price" class="form-control  @error('price') is-invalid @enderror" placeholder="Цена" value="{{ old('price') }}">
                            <div class="input-group-append">
                                <span class="input-group-text">руб</span>
                            </div>
                        </div>
                        @error('price')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    @error('price')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    <input type="hidden" name="task_id" value="{{ $task->id }}">
                    <button class="btn btn-success mt-3">Откликнуться</button>
                </form>

            </div>
        @endif

        @foreach($task->responses as $response)
            @if(isset($myResponse) && $myResponse->user_id == $response->user_id) @continue @endif

            @if($response->user_id === $task->executor)
                <div class="mt-3 bg-success text-white p-2 rounded-top">Выбран исполнителем</div>
            @endif

            <div class="@if($response->user_id === $task->executor) border-success rounded-bottom @else border-light mt-3 rounded @endif bg-white  border p-3 response">
                <div class="row">
                    <div class="col-4 col-md-2">
                        <a href="{{ route('user.show', ['user' => $response->user->id]) }}">
                            <div class="user-logo mx-auto" style="background: url('{{ $response->user->logo }}') center center">
                                @if(!$response->user->logo)
                                    <div class="text-logo">
                                        {{ mb_substr($response->user->name, 0, 1) }}{{  mb_substr($response->user->surname, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-8 col-md-10">
                        <h4><a class="text-dark" href="{{ route('user.show', ['user' => $response->user->id]) }}">{{ ($response->user->company && $response->user->company->is_active==1) ? $response->user->company->title : $response->user->name . ' ' . $response->user->surname }}</a></h4>
                        <span class="bg-success rounded price text-white">{{ number_format($response->price, 0, '.', ' ') }} руб.</span>
                        <div class="info">
                            <span class="text-success mr-3" title="Положительных отзывов"><i class="far fa-thumbs-up"></i> - {{ $response->user->positive_assessments_count }}</span>
                            <span class="text-danger mr-3" title="Отрицательных отзывов"><i class="far fa-thumbs-down"></i> - {{ $response->user->negative_assessments_count }}</span>
                        </div>

                    </div>
                    <div class="col-md-10 offset-md-2 description">
                        {{ $response->text }} <br>
                        <span class="text-secondary"><small>{{ $response->created_at }}</small></span>
                        @if(auth()->id() === $task->user_id)
                            <a href="javascript:;" class="text-secondary float-right"
                               onclick="$(this).parent().parent().parent().find('.messages').slideToggle(200)">
                                <small>Сообщения ({{ count($response->messages) }})</small>
                            </a>
                        @endif
                    </div>
                </div>

                @if(auth()->id() === $task->user_id)
                    <div class="messages mt-3 p-3 border-top" style="display: none;">

                        @foreach($response->messages as $message)
                            <div class="message @if($message->user_id===auth()->id()) right @else left @endif">
                                <span>{!! nl2br(e($message->text)) !!}</span>
                            </div>
                        @endforeach

                        <div class="text-center">
                            <a href="javascript:;" class="btn btn-primary mt-2 mr-2"
                               onclick=" $('#message-modal input[name=response_id]').val('{{ $response->id }}'); "
                               data-toggle="modal" data-target="#message-modal"
                            >Написать сообщение</a>

                            @if(!$task->executor)
                                <form action="{{ route('user.task.setExecutor', ['response' => $response->id]) }}" method="post" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success mt-2">Назначить исполнителем</button>
                                </form>
                            @endif
                        </div>

                    </div>


                @endif

            </div>
        @endforeach


        <!-- The Modal -->
        <div class="modal" id="message-modal">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Сообщение</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('user.message.send') }}" method="POST">
                            @csrf
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5">{{ old('message') }}</textarea>
                            <input type="hidden" name="response_id" value="{{ old('response_id') }}">

                            @if($errors->any())
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ implode(' ', $errors->all(':message')) }}</strong>
                                </span>
                            @endif



                            <button type="submit" class="btn btn-primary float-right mt-2">Отправить</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>


        @if(auth()->id() === $task->user_id && $task->executor !== null)
            <div class="modal" id="check-task-modal">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Проверить задание</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                            <form action="{{ route('user.task.addReview', ['task' => $task->id]) }}" method="POST">
                                @csrf

                                <div class="btn-group btn-group-toggle mt-2 w-100" data-toggle="buttons">
                                    <label class="btn btn-outline-success active">
                                        <input type="radio" name="status" autocomplete="off" value="success" checked> Выполнено
                                    </label>
                                    <label class="btn btn-outline-danger">
                                        <input type="radio" name="status" autocomplete="off" value="close"> Не выполнено
                                    </label>
                                </div>

                                <div class="assessment mt-3">
                                    <label>Отзыв:</label><br>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-success active">
                                            <input type="radio" name="assessment" autocomplete="off" value="1" checked> <i class='far fa-thumbs-up'></i>
                                        </label>
                                        <label class="btn btn-outline-danger">
                                            <input type="radio" name="assessment" autocomplete="off" value="0"> <i class='far fa-thumbs-down'></i>
                                        </label>
                                    </div>
                                    <textarea class="form-control mt-2" name="text"></textarea>
                                </div>

                                @if($errors->any())
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ implode(' ', $errors->all(':message')) }}</strong>
                                    </span>
                                @endif


                                <div style="clear:both;"></div>
                                <button type="submit" class="btn btn-primary float-right mt-2">Отправить</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('footer')
    <script src="/js/inputmask.js"></script>
    <script type="application/javascript">
        $(document).ready(function () {
           $('input[name=price]').inputmask("currency", {
               prefix: '',
               groupSeparator: ' ',
               placeholder: '',
               min: 1,
               digits: 0,
               numericInput: true,
               autoGroup: true
           });

           let error = "{{ $errors->first('message') }}";

           if( error ){
               $('#message-modal').modal('show');
           }

           if( "{{ $errors->first('text') }}" )
               $('#check-task-modal').modal('show');

           $('input[name=status]').on('change', function () {
                $('.assessment').slideToggle(200);
           })
        });
    </script>
@endpush
