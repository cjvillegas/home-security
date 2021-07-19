<?php

namespace App\Exports\Reports\WorkAnalytics;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class HourlyWorkAnalyticsReportExport implements
    FromArray,
    WithHeadings,
    ShouldAutoSize,
    WithStrictNullComparison
{
    use Exportable;

    public $headers;
    public $data;

    /**
     * HourlyWorkAnalyticsReportExport constructor.
     */
    public function __construct(array $headers, array $data)
    {
        $this->headers = $headers;
        $this->data = $data;
    }

    /**
    * @return array
    */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Define the export headers
     *
     * @return array
     **/
    public function headings(): array
    {
        return $this->headers;
    }
}
