<?php

namespace App\Repositories\Management;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class UserRepository {

    public function __construct(User $user) {}

    public static function datatable() {
        $query = DB::table('users')
                    ->select([
                        'users.id',
                        'c.nama as nama_company',
                        'users.company',
                        'users.name',
                        'r.nama as nama_role',
                        'users.role',
                        'users.status',
                    ])
                    ->join('company as c', 'users.company', '=', 'c.kode')
                    ->join('role as r', 'users.role', '=', 'r.slug')
                    ->where('users.status', 'active');
        
        return $query;
    }

    public static function dataTableIncomingJob()
    {
        $query = DB::table('order as o')
                    ->select([
                        'o.uid',
                        'o.nama',
                        'o.customer',
                        'o.jenis_produk',
                        'o.jenis_kertas',
                        'o.ukuran',
                        'o.jumlah',
                        'o.file_spk',
                        'd.nama as progress',
                        DB::raw("DATE_FORMAT(o.deadline, '%d/%m/%Y') as deadline"),
                        DB::raw("DATE_FORMAT(o.tanggal, '%d/%m/%Y') as tanggal")
                    ])
                    ->join('order_detail as od', 'o.uid', '=', 'od.uid_order')
                    ->join('divisi as d', function ($join) {
                        $join->on('o.uid_divisi', '=', 'd.uid')
                            ->where('d.urutan', '<', 2);
                    })
                    ->where('od.uid_divisi', 'D20241117144258638642')
                    ->where('o.status', 1);

        return $query;
    }

}
