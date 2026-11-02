@extends('layouts.app')
@section('title', 'Thêm công việc')

@section('content')
<div class="panel">
    <h1 class="h3 mb-4">Thêm công việc</h1>
    @include('tasks._form', ['action' => route('tasks.store')])
</div>
@endsection
