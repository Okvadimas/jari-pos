<?php

namespace App\Services;

use App\Repositories\PosRepository;

class PosService
{
    public static function getProducts($request)
    {
        $products = PosRepository::getProducts($request);
        return $products;
    }

    public static function getCategories()
    {
        $products = PosRepository::getCategories();
        return $products;
    }

    public static function getTopSelling()
    {
        $topSelling = PosRepository::getTopSelling();
        return $topSelling;
    }

    public static function getVouchers()
    {
        $vouchers = PosRepository::getVouchers();
        return $vouchers;
    }
}
