<?php

namespace App\Http\Controllers;

use App\Services\ReportingService;
use Illuminate\Support\Facades\Auth;

class StudentProgressController extends Controller
{
    public function __construct(
        protected ReportingService $reporting
    ) {
    }

    public function index()
    {
        $data = $this->reporting->studentProgress(Auth::user());

        return view('progress.index', $data);
    }
}
