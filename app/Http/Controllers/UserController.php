<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function getCustomers(): JsonResponse
    {
        $customers = User::where('role', 'customer')->get(['id', 'name', 'email']);
        return response()->json($customers);
    }
}
