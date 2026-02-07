<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $table = 'counters';
    
    protected $fillable = ['company', 'modul', 'year', 'month', 'counter_value'];
    
    public $timestamps = false;

    /**
     * Get the next counter value for a specific company, module, year, and month
     * This uses the stored procedure for atomic increment
     */
    public static function getNextTransactionNumber(string $prefix, int $companyId, int $year, int $month): string
    {
        $result = \DB::select('CALL generate_transaction_number(?, ?, ?, ?)', [
            $prefix,
            (string)$companyId,
            $year,
            $month
        ]);

        return $result[0]->transaction_number;
    }
}
