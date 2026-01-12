<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\UtilityRepository;

class UtilityController extends Controller
{
    protected $utilityRepository;

    public function __construct(UtilityRepository $utilityRepository)
    {
        $this->utilityRepository = $utilityRepository;
    }

    public function dataCompanies(Request $request)
    {
        $search = $request->search;
        $data = $this->utilityRepository->dataCompanies($search);
        return response()->json($data);
    }
    
}
