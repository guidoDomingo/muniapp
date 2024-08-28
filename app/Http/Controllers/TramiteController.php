<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use Illuminate\Http\Request;

class TramiteController extends Controller
{
    public function index()
    {
        $tramites = Tramite::all();
        return view('tramites.index', compact('tramites'));
    }

    public function show($id)
    {
        $tramite = Tramite::findOrFail($id);
        return view('tramites.show', compact('tramite'));
    }
}
