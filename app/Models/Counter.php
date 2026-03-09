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
     * Uses stored procedure on MySQL, PHP fallback on other drivers (e.g. SQLite for tests)
     */
    public static function getNextTransactionNumber(string $prefix, int $companyId, int $year, int $month): string
    {
        $driver = \DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $result = \DB::select('CALL generate_transaction_number(?, ?, ?, ?)', [
                $prefix,
                (string)$companyId,
                $year,
                $month
            ]);

            return $result[0]->transaction_number;
        }

        // PHP fallback for SQLite / testing
        $counter = static::where('company', (string)$companyId)
            ->where('modul', $prefix)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($counter) {
            $counter->increment('counter_value');
            $value = $counter->counter_value;
        } else {
            static::create([
                'company' => (string)$companyId,
                'modul' => $prefix,
                'year' => $year,
                'month' => $month,
                'counter_value' => 1,
            ]);
            $value = 1;
        }

        return $prefix . '/' . str_pad($year, 4, '0', STR_PAD_LEFT) . '/' . str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . str_pad($value, 4, '0', STR_PAD_LEFT);
    }
}
