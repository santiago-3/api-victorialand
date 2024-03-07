<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards) {
        $this->authenticate($request, $guards);
        $response = $next($request);

        \Log::info('Authenticate middleware, responding');
        if (! $request->header('User-id')) {
            \Log::info('Authenticate middleware, no user id found');
            $user = Auth::user();
            $response->header('User-id', $user->userid);
            $response->header('Access-Control-Expose-Headers', 'User-id');
        }

        return $response;
    }

    public function authenticate($request, ...$guards) {

        if ($request->header('User-id')) {
            $this->authenticateFromUserid($request->header('User-id'), $request, $guards);
        }
        else {
            $n = 32;
            $userid = bin2hex(random_bytes($n));
            $expirationtime = 60 * 60 * 24 * 30;

            $biggestId = ($user = User::orderBy('id', 'desc')->first()) ? $user->id : 0;
            \Log::info('userid: ' . $userid);
            User::create([
                'name' => 'user_' . $biggestId+1,
                'email' => 'email_' . $biggestId+1,
                'userid' => $userid,
                'password' => Hash::make('doesntmatter'),
            ]);
            $this->authenticateFromUserid($userid, $request, $guards);
        }

    }

    private function authenticateFromUserid(String $userid, $request, $guards) {
        $user = User::where('userid', $userid)->first();
        if ($user) {
            \Log::info('login user: ' . $user->name);
            Auth::login($user);
        }
        else {
            $this->unauthenticated($request, $guards);
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
