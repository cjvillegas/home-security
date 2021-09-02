<?php

namespace App\Services;

use App\Interfaces\ServiceDataInterface;
use App\Jobs\Exports\CsvExportJob;
use App\Models\Export;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;

class CsvExporterService
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $headers;

    /**
     * ExporterService constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function export(ServiceDataInterface $serviceData)
    {
        // generate new export
        $export = $this->createNewExport($serviceData->getFilters());

        $exportPayload = new ExportPayloadService(
            $this->user,
            $export,
            $this->getHeaders(),
            $serviceData->getData('export'),
            $this->getPath(),
            $this->getName(),
            $this->getType()
        );

        // dispatch the job for exporting
        CsvExportJob::dispatch($exportPayload)->onQueue('default');
    }

    /**
     * Create new export instance
     *
     * @param array $filters
     *
     * @return Export
     */
    public function createNewExport(array $filters = []): Export
    {
        $export = new Export();
        $export->user_id = $this->user->id;
        $export->uid = (string) Str::uuid();
        $export->status = Export::EXPORT_STATUS_IN_PROGRESS;
        $export->type = Export::QC_FAULT_EXPORT_REPORT;
        $export->filters = $filters;

        $export->save();

        return $export;
    }

    /*Setter*/
    /**
     * Set the path where you wanna save the file
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath(string $path = ''): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Set the file name of the export
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name = ''): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the export array headers
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /*Getter*/
    /**
     * Return the path where the export should be saved
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Return the name of the file
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the possible CSV headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Return the file type
     *
     * @return string
     */
    public function getType(): string
    {
        return '.xlsx';
    }
}
