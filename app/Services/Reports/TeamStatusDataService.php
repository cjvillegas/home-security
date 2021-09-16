<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;
use App\Models\Export;
use App\Models\ShiftAssignment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TeamStatusDataService implements ServiceDataInterface
{
    /**
     * @var array
     */
    private $filters;

    /**
     * @var mixed
     */
    private $query;

    /**
     * TeamStatusDataService constructor.
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Retrieve team status report data
     *
     * @param string $type
     *
     * @return Collection|LengthAwarePaginator
     */
    public function getData(string $type)
    {
        $query = $this->buildQuery()->applyFilters();

        switch ($type) {
            case 'list':
                $size = $this->getFilterValue('size', 25);
                $page = $this->getFilterValue('page', 1);

                // if pagination is enabled
                if ($this->isFilterExist('size')) {
                    $teamStatus = $query->paginate($page, $size);
                } else {
                    $teamStatus = $query->getResultInCollection();
                }

                break;
            case 'export':
                $teamStatus = $query->getResultInCollection();

                break;
        }

        return $teamStatus;
    }

    /**
     * Return base query of this import
     *
     * @return self
     */
    public function buildQuery(): self
    {
        $query = ShiftAssignment::query()
            ->select([
                'shift_assignments.folder_name',
                DB::raw("COUNT(DISTINCT shift_assignments.id) AS planned_blinds"),
                DB::raw("COUNT(DISTINCT shift_assignments.id) - CAST(SUM(DISTINCT CASE WHEN sc.blindid = shift_assignments.serial_id THEN 1 ELSE 0 END) AS SIGNED) AS not_started"),
                DB::raw("CAST(SUM(DISTINCT CASE WHEN sc.blindid = shift_assignments.serial_id THEN 1 ELSE 0 END) AS SIGNED) AS started_blinds"),
                DB::raw("CAST(SUM(DISTINCT CASE WHEN sc.processid IN ('P5688737', 'P1002', 'P1021', 'P1024', 'P1025') THEN 1 ELSE 0 END) AS SIGNED) AS completed_blinds"),
                DB::raw("CAST(SUM(DISTINCT CASE WHEN sc.processid IN ('P1012', 'P1014') THEN 1 ELSE 0 END) AS SIGNED) AS packed_blinds")
            ])
            ->leftJoin('scanners AS sc', DB::raw('CAST(sc.blindid AS SIGNED)'), 'shift_assignments.serial_id')
            ->leftJoin('processes AS p', 'p.barcode', 'sc.processid')
            ->groupBy('shift_assignments.folder_name')
            ->orderBy('shift_assignments.folder_name');

        $this->query = $query;

        return $this;
    }

    /**
     * Apply relative filters
     *
     * @return self
     */
    function applyFilters(): self
    {
        $folders = $this->getFilterValue('folders', []);

        // if employee filter is present
        if ($this->isFilterExist('date') && $date = $this->getFilterValue('date')) {
            $date = Carbon::parse($date)->format('Y-m-d');
            $this->query->filterInDate($date);
        }

        // filter shift assignments by folder name
        if ($this->isFilterExist('folders') && !empty($folders)) {
            $this->query->filterByFolderName($folders);
        }

        return $this;
    }

    /**
     * @param int $page
     * @param int $size
     *
     * @return LengthAwarePaginator
     */
    private function paginate(int $page = 1, int $size = 25): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }

    /**
     * Retrieve the qc fault data in collect format from the DB
     *
     * @return Collection
     */
    private function getResultInCollection(): Collection
    {
        return $this->query->get();
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get the export type. The export type should have an Export counterpart.
     * Make sure you register a unique one in the Export model.
     *
     * @return string
     */
    public function exportType(): string
    {
        return Export::TEAM_STATUS_EXPORT_REPORT;
    }

    /**
     * Check certain key exists in the filters array
     *
     * @param string $key
     *
     * @return bool
     */
    private function isFilterExist(string $key): bool
    {
        return isset($this->filters[$key]);
    }

    /**
     * Retrieve a value from the filters using a key
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed|null
     */
    private function getFilterValue(string $key, $default = null)
    {
        return $this->filters[$key] ?? $default;
    }
}
