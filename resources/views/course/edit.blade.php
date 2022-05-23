@extends('layout.master')
@section('content')
<form action="{{ route('courses.update', $course) }}" method="post">
    @csrf
    @method('PUT')
    Name <input type="text" name="name" value="{{ $course->name }}">
    @if($errors->has('name'))
        <span class="error">
            {{ $errors->first('name') }}
        </span>
    @endif
    <br>
    <button>Update</button>
</form>
@endsection
