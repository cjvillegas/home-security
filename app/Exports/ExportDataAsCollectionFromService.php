<?php

namespace App\Exports;

use App\Interfaces\ServiceDataInterface;
use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ExportDataAsCollectionFromService implements
    FromCollection,
    WithStrictNullComparison,
    WithHeadings,
    ShouldAutoSize
{
    use Exportable;

    /**
     * @var ServiceDataInterface
     */
    private $service;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $headerKeys;

    /**
     * ExportDataAsCollectionFromService constructor.
     *
     * @param ServiceDataInterface $service
     */
    public function __construct(ServiceDataInterface $service, array $headers)
    {
        $this->service = $service;
        $this->headers = array_values($headers);
        $this->headerKeys = array_keys($headers);
    }

    /**
    * @return SupportCollection
    */
    public function collection(): SupportCollection
    {
        return $this->service->getData('export')
            ->map(function($item) {
                return $item->only($this->headerKeys);
            })
            ->chunk(500);
    }

    /**
     * Define the export headers
     *
     * @return array
     */
    public function headings(): array
    {
        return $this->headers;
    }
}
