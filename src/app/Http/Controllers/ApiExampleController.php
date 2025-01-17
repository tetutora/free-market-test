<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiExampleController extends Controller
{
    public function __construct()
    {
        $this->middleware(\Asm89\Stack\Cors\HandleCors::class);  // Asm89 Stack CORS ミドルウェアを適用
    }

    public function index()
    {
        return response()->json(['message' => 'API Example Controller']);
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Data received', 'data' => $request->all()]);
    }
}