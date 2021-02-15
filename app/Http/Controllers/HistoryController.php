<?php

namespace App\Http\Controllers;

use App\Models\FieldHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        return view('history');
    }
}
