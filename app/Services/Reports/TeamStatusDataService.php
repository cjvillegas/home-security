<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;
use App\Models\Export;
use App\Models\ShiftAssignment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection AS SupCollection;
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
                    $countSql = exportSqlQuery((clone $this->query)->groupBy('shift_assignments.folder_name'));
                    $totalCount = DB::select("SELECT COUNT(*) AS aggregate FROM ($countSql) AS main_table LIMIT 1")[0]->aggregate ?? 0;

                    $items = (clone $this->query)
                        ->groupBy('shift_assignments.id')
                        ->get();

                    $items = $this->processShiftAssignmentData($items);

                    $teamStatus = new LengthAwarePaginator($items, $totalCount, $size, $page);
                } else {
                    $this->query->groupBy('shift_assignments.id');

                    $teamStatus = $this->processShiftAssignmentData($query->getResultInCollection());
                }

                break;
            case 'export':
                $this->query->groupBy('shift_assignments.id');

                $teamStatus = $this->processShiftAssignmentData($query->getResultInCollection());

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
                DB::raw("1 AS planned_blinds"),
                DB::raw("0 AS not_started"),
                DB::raw("CASE WHEN sc.blindid = shift_assignments.serial_id AND sc.blindid = shift_assignments.serial_id THEN 1 ELSE 0 END AS started_blinds"),
                DB::raw("CASE WHEN sc.blindid = shift_assignments.serial_id AND sc.processid IN ('P5688737', 'P1002', 'P1021', 'P1024', 'P1025') THEN 1 ELSE 0 END AS completed_blinds"),
                DB::raw("CASE WHEN sc.blindid = shift_assignments.serial_id AND sc.processid IN ('P1012', 'P1014') THEN 1 ELSE 0 END AS packed_blinds")
            ])
            ->leftJoin('scanners AS sc', DB::raw('CAST(sc.blindid AS SIGNED)'), 'shift_assignments.serial_id')
            ->leftJoin('processes AS p', 'p.barcode', 'sc.processid')
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
            $date = Carbon::parse($date);
            $dates = [$date->startOfDay()->format('Y-m-d H:i:s'), $date->endOfDay()->format('Y-m-d H:i:s')];
            $this->query->filterInBetweenDates($dates);
        }

        // filter shift assignments by folder name
        if ($this->isFilterExist('folders') && !empty($folders)) {
            $this->query->filterInFolderName($folders);
        }

        return $this;
    }

    /**
     * Process the shift assignments data. Remove duplicates and return only unique names.
     * We can't achieve it in the query, we need to create a processing method to do so.
     *
     * @param Collection $collection
     *
     * @return SupCollection
     */
    private function processShiftAssignmentData(Collection $collection): SupCollection
    {
        $processed = [];

        foreach ($collection as $item) {
            // check if this item is not yet added in the processed array
            if (!isset($processed[$item->folder_name])) {
                $processed[$item->folder_name] = $item->toArray();

                continue;
            }

            $processed[$item->folder_name]['planned_blinds'] += $item->planned_blinds;
            $processed[$item->folder_name]['completed_blinds'] += $item->completed_blinds;
            $processed[$item->folder_name]['packed_blinds'] += $item->packed_blinds;
            $processed[$item->folder_name]['started_blinds'] += $item->started_blinds;
            $processed[$item->folder_name]['not_started'] = $processed[$item->folder_name]['planned_blinds'] - $processed[$item->folder_name]['started_blinds'];
        }

        return collect(array_values($processed));
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
