<?php

namespace App\Exports;

use App\Models\Scanner;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Excel;

class RawScannersDataExport implements
    FromQuery,
    Responsable,
    WithStrictNullComparison,
    ShouldAutoSize,
    WithHeadings
{
    use Exportable;

    public $start;
    public $end;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'Raw Scanners Data.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /**
     * HourlyWorkAnalyticsReportExport constructor.
     *
     * @param string $start
     * @param string $end
     */
    public function __construct(string $start, string $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return Scanner::query()
            ->select([
                'orders.order_no',
                DB::raw('DATE_FORMAT(orders.ordered_at, "%Y-%m-%d") as order_at'),
                'orders.customer',
                'orders.blind_type',
                'orders.quantity',
                'orders.serial_id',
                'processes.name',
                'employees.fullname',
                DB::raw('DATE_FORMAT(scanners.scannedtime, "%Y-%m-%d %H:%i") as scanned_date_time'),
            ])
            ->whereBetween('scannedtime', [$this->start, $this->end])
            ->leftJoin('orders', 'orders.serial_id', '=', 'scanners.blindid')
            ->join('processes', 'processes.barcode', '=', 'scanners.processid')
            ->join('employees', 'employees.barcode', '=', 'scanners.employeeid');
    }

    /**
     * Define the export headers
     *
     * @return array
     **/
    public function headings(): array
    {
        return [
            'Order No.',
            'Ordered At',
            'Customer',
            'Blind Type',
            'Quantity',
            'Serial ID',
            'Operation',
            'Employee',
            'Scanned Date',
        ];
    }
}
