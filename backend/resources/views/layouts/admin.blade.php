<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · {{ config('app.name') }}</title>
    <style>
        :root { color-scheme: light; --ink:#172033; --muted:#667085; --line:#e4e7ec; --brand:#1f6f5f; --danger:#b42318; --paper:#fff; --bg:#f6f7f9; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--ink); font:15px/1.5 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; }
        a { color:var(--brand); text-decoration:none; }
        button,input { font:inherit; }
        .shell { min-height:100vh; display:grid; grid-template-columns:240px 1fr; }
        aside { background:#14231f; color:#fff; padding:28px 20px; }
        .brand { display:block; color:#fff; font-size:20px; font-weight:750; margin:0 10px 32px; }
        nav a { display:block; color:#cbd5d1; padding:10px 12px; border-radius:8px; margin-bottom:4px; }
        nav a:hover, nav a.active { color:#fff; background:#28443c; }
        main { padding:36px; min-width:0; }
        .topbar,.row { display:flex; align-items:center; justify-content:space-between; gap:16px; }
        .topbar { margin-bottom:28px; }
        h1,h2 { margin:0; line-height:1.2; } h1 { font-size:28px; } h2 { font-size:19px; }
        .muted { color:var(--muted); }
        .card { background:var(--paper); border:1px solid var(--line); border-radius:12px; padding:24px; box-shadow:0 1px 2px rgb(16 24 40 / 4%); }
        .button { display:inline-flex; align-items:center; justify-content:center; border:0; border-radius:8px; padding:10px 15px; background:var(--brand); color:#fff; cursor:pointer; font-weight:650; }
        .button.secondary { background:#fff; border:1px solid var(--line); color:var(--ink); }
        .button.danger { background:#fff; border:1px solid #fecdca; color:var(--danger); }
        .button.small { padding:7px 11px; font-size:14px; }
        .link-button { border:0; background:none; color:#d0d5dd; cursor:pointer; padding:10px 12px; }
        .alert { border-radius:8px; padding:12px 14px; margin-bottom:20px; }
        .alert.success { background:#ecfdf3; color:#027a48; }
        .alert.error { background:#fef3f2; color:var(--danger); }
        table { width:100%; border-collapse:collapse; }
        th,td { padding:14px 12px; text-align:left; border-bottom:1px solid var(--line); vertical-align:middle; }
        th { color:var(--muted); font-size:12px; text-transform:uppercase; letter-spacing:.05em; }
        tr:last-child td { border-bottom:0; }
        .actions { display:flex; justify-content:flex-end; gap:8px; }
        .badge { display:inline-block; padding:3px 9px; border-radius:999px; color:#027a48; background:#ecfdf3; font-size:12px; font-weight:700; }
        .field { margin-bottom:18px; } label { display:block; margin-bottom:6px; font-weight:650; }
        input[type=text],input[type=email],input[type=password],select,textarea { width:100%; border:1px solid #d0d5dd; border-radius:8px; padding:10px 12px; background:#fff; }
        textarea { min-height:120px; resize:vertical; }
        input:focus,select:focus,textarea:focus { outline:3px solid rgb(31 111 95 / 15%); border-color:var(--brand); }
        .help { color:var(--muted); margin:5px 0 0; font-size:13px; }
        .field-error { color:var(--danger); margin:5px 0 0; font-size:13px; }
        .form-card { max-width:680px; }
        .auth-body { min-height:100vh; display:grid; place-items:center; padding:24px; }
        .auth-card { width:min(430px,100%); }
        .auth-card h1 { margin-bottom:8px; }
        .checkbox { display:flex; gap:8px; align-items:center; margin:16px 0 20px; }
        .checkbox label { margin:0; font-weight:400; }
        .pagination { margin-top:22px; } .pagination svg { width:18px; } .pagination nav>div:first-child { display:none; }
        .pagination nav>div:last-child { display:flex; align-items:center; justify-content:space-between; gap:12px; }
        .pagination nav span,.pagination nav a { display:inline-flex; padding:6px 9px; }
        @media (max-width:760px) { .shell { grid-template-columns:1fr; } aside { padding:16px; } .brand { margin-bottom:12px; } nav { display:flex; gap:4px; } main { padding:22px 16px; } .topbar { align-items:flex-start; } table { min-width:650px; } .table-wrap { overflow:auto; } }
    </style>
</head>
<body class="@guest auth-body @endguest">
@auth
    <div class="shell">
        <aside>
            <a class="brand" href="{{ route('admin.dashboard') }}">Takshasheela Admin</a>
            <nav>
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
                <a class="{{ request()->routeIs('admin.password.*') ? 'active' : '' }}" href="{{ route('admin.password.edit') }}">Change password</a>
            </nav>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="link-button" type="submit">Log out</button>
            </form>
        </aside>
        <main>
            <div class="topbar">
                <div><h1>@yield('heading', 'Admin')</h1>@hasSection('subheading')<div class="muted">@yield('subheading')</div>@endif</div>
                <div>{{ auth()->user()->name }}</div>
            </div>
            @if (session('success')) <div class="alert success">{{ session('success') }}</div> @endif
            @if (session('error')) <div class="alert error">{{ session('error') }}</div> @endif
            @yield('content')
        </main>
    </div>
@else
    @yield('content')
@endauth
</body>
</html>
