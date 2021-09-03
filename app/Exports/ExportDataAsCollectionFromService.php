<?php

namespace App\Exports;

use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Collection;
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
     * @var Collection
     */
    private $collection;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $headerKeys;

    /**
     * QcFaultDataExport constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection, array $headers)
    {
        $this->collection = $collection;
        $this->headers = array_values($headers);
        $this->headerKeys = array_keys($headers);
    }

    /**
    * @return SupportCollection
    */
    public function collection(): SupportCollection
    {
        return $this->collection->chunk(1000);
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
