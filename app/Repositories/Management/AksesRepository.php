<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class UserRepository {

    protected $order;

    public function __construct(Order $order) {
        $this->order = $order;
    }

}
