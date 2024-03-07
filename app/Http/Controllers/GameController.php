<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index() {
        \Log::info('saving game');
    }

    public function csrf() {
        \Log::info('csrf');
        return response()->json(['csrf' => csrf_token()]);
    }

    public function provideName() {
        return response()->json(["name" => Auth::user()->name]);
    }
}
