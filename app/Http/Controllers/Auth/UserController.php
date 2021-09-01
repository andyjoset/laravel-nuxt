<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function current(Request $request)
    {
        return new Auth($request->user()->loadMissing('roles.permissions'));
    }
}
