@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Administrator backend')

@section('content')
<div class="card">
    <div class="row">
        <div>
            <h2>User management</h2>
            <p class="muted">Create, update, and remove administrator accounts.</p>
        </div>
        <a class="button" href="{{ route('admin.users.index') }}">Manage users</a>
    </div>
</div>
@endsection
