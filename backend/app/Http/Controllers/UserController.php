<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', Rule::in(['admin', 'user'])],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);
        $users = User::query()
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where(fn ($query) => $query
                ->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))
            ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->orderBy('name')->orderBy('id')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $user = new User;
        $user->role = 'admin';

        return view('admin.users.form', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $user = new User(collect($data)->only(['name', 'email', 'password'])->all());
        $user->role = $data['role'];
        $user->is_active = $data['is_active'];
        $user->save();

        return redirect()->route('admin.users.index')->with('status', $user->name.' account created.');
    }

    public function edit(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return redirect()->route('admin.profile.edit');
        }

        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        DB::transaction(function () use ($request, $user, $data) {
            [$user, $administrators] = $this->lockAccounts($request, $user);
            $this->protectAccount($request, $user, $administrators, $data['role'] === 'admin' && $data['is_active']);
            $oldEmail = $user->email;
            $revoke = filled($data['password'] ?? null) || $user->role !== $data['role'] || $user->is_active !== $data['is_active'] || $oldEmail !== $data['email'];
            $user->fill(collect($data)->only(['name', 'email'])->all());
            $user->role = $data['role'];
            $user->is_active = $data['is_active'];
            if (filled($data['password'] ?? null)) {
                $user->password = $data['password'];
            }
            if ($oldEmail !== $data['email']) {
                $user->email_verified_at = null;
                // Delete the reset token associated with the previous email too.
                Password::deleteToken(new User(['email' => $oldEmail]));
            }
            if ($revoke) {
                $user->setRememberToken(Str::random(60));
                Password::deleteToken($user);
            }
            $user->save();
            if ($revoke) {
                $this->deleteSessions($user);
            }
        }, 3);

        return redirect()->route('admin.users.index')->with('status', $data['name'].' account updated.');
    }

    public function destroy(Request $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            [$user, $administrators] = $this->lockAccounts($request, $user);
            $this->protectAccount($request, $user, $administrators, false);
            Password::deleteToken($user);
            $this->deleteSessions($user);
            $user->delete();
        }, 3);

        return redirect()->route('admin.users.index')->with('status', 'User deleted. Their website content has been kept.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => Str::lower(trim($request->input('email')))]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user?->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'is_active' => ['required', 'boolean'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'confirmed', PasswordRule::min(12)->letters()->numbers()],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function lockAccounts(Request $request, User $target): array
    {
        // Lock in a stable order so simultaneous removals cannot remove every admin.
        $accounts = User::where('role', 'admin')->orWhere('id', $target->id)
            ->orderBy('id')->lockForUpdate()->get();
        $actor = $accounts->firstWhere('id', $request->user()->id);
        abort_unless($actor && $actor->role === 'admin' && $actor->is_active, 403);
        $user = $accounts->firstWhere('id', $target->id);
        abort_unless($user, 404);

        return [$user, $accounts->where('role', 'admin')->where('is_active', true)->count()];
    }

    private function protectAccount(Request $request, User $user, int $administrators, bool $remainsAdministrator): void
    {
        if ($user->role === 'admin' && $user->is_active && ! $remainsAdministrator && $administrators <= 1) {
            throw ValidationException::withMessages(['user' => 'The last active administrator cannot be deleted, deactivated, or changed to a user.']);
        }
        if ($user->is($request->user())) {
            throw ValidationException::withMessages(['user' => 'Use My account to change your own profile or password. You cannot delete or change your own access here.']);
        }
    }

    private function deleteSessions(User $user): void
    {
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)->delete();
        }
    }
}
