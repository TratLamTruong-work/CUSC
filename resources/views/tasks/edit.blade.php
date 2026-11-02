@extends('layouts.app')
@section('title', 'Sửa công việc')

@section('content')
<div class="panel">
    <h1 class="h3 mb-4">Sửa công việc</h1>
    @include('tasks._form', ['action' => route('tasks.update', $task->id)])
</div>
@endsection
