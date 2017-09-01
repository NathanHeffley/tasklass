@extends('layouts.master')

@section('body')
    <div id="todo-list"></div>
@endsection

@push('beforeScripts')
<script>
    window.Todo.todos = {!! $todos !!};
</script>
@endpush
