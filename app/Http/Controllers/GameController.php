<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function setName() {
        $name = request()->input('name');
        if (strlen($name) > 3) {
            $user = Auth::user();
            $user->name = $name;
            $user->save();
        }
        else {
            throw new \Exception('name too short. please provide a name with 4 or more characters');
        }
    }

    public function provideName() {
        return response()->json(["name" => Auth::user()->name]);
    }
}
