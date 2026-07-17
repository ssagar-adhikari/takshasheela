@extends('layouts.admin')

@section('title', 'Edit user')
@section('heading', 'Edit user')
@section('subheading', 'Update account details or set a new password.')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @include('admin.users._form')
    </form>
</div>
@endsection
