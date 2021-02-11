<?php

namespace App\Http\Controllers;

use App\Models\Individual;
use Illuminate\Http\Request;

class IndividualsController extends Controller
{
    public function individualsView()
    {
        return view('individuals');
    }

    public function getIndividuals()
    {
        return response()->json(
            Individual::all()
        );
    }
}
