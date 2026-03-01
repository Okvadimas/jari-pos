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
        return $this->successResponse('Success', $data);
    }

    public function dataProductVariants(Request $request)
    {
        $search = $request->search;
        $data = $this->utilityRepository->dataProductVariants($search);
        return $this->successResponse('Success', $data);
    }

    public function dataPaymentMethods(Request $request)
    {
        $data = \App\Models\PaymentMethod::orderBy('name')->get(['id', 'name']);
        return $this->successResponse('Success', $data);
    }
    
}
