<?php

namespace App\Repositories;

use App\Models\Company;

class UtilityRepository {

    protected $company;

    public function __construct(Company $company) {
        $this->company = $company;
    }

    public function dataCompanies($search)
    {
        $query = $this->company->select('id', 'name')->where('status', 1);

        if($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query;
    }

}
