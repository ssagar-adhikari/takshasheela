@extends('layouts.admin')

@section('title', 'Add user')
@section('heading', 'Add user')
@section('subheading', 'The new account will have administrator access.')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @include('admin.users._form')
    </form>
</div>
@endsection
