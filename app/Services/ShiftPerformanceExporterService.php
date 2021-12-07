<?php

namespace App\Services;

use App\Interfaces\ServiceDataInterface;
use App\Jobs\Exports\ShiftPerformanceJob;
use Exception;
use Illuminate\Support\Str;
use App\Models\{Export, User};

class ShiftPerformanceExporterService
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
     * ExporterService constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function export(ServiceDataInterface $serviceData, $dateRange)
    {
        $export = null;

        try {
            // generate new export
            $export = $this->createNewExport($serviceData->exportType(), $serviceData->getFilters());

            $exportPayload = new ExportPayloadService(
                $this->user,
                $export,
                [],
                $serviceData,
                $this->getPath(),
                $this->getName(),
                $this->getType()
            );

            ShiftPerformanceJob::dispatch($exportPayload, $dateRange)->onQueue('default');
        } catch (Exception $exception) {
            if (!empty($export)) {
                $export->status = Export::EXPORT_STATUS_FAILED;
                $export->save();
            }
        }
    }

    /**
     * Create new export instance
     *
     * @param string $exportType
     * @param array $filters
     *
     * @return Export
     */
    public function createNewExport(string $exportType, array $filters = []): Export
    {
        $export = new Export();
        $export->user_id = $this->user->id;
        $export->uid = (string) Str::uuid();
        $export->status = Export::EXPORT_STATUS_IN_PROGRESS;
        $export->type = $exportType;
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
     * Return the file type
     *
     * @return string
     */
    public function getType(): string
    {
        return '.xlsx';
    }
}
