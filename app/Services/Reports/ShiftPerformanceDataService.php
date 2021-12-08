<?php

namespace App\Services\Reports;

use App\Models\Export;
use App\Models\Order;
use App\Models\Scanner;
use App\Models\ShiftAssignment;
use App\Services\Reports\ReportDataService;
use Illuminate\Support\Collection AS SupCollection;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ShiftPerformanceDataService extends ReportDataService
{
    /**
     * @var mixed
     */
    private $departments;

    /**
     * @var mixed
     */
    private $shifts;

    /**
     * @var mixed
     */
    private $dateRange;

    /**
     * @var mixed
     */
    private $from;

    /**
     * @var mixed
     */
    private $to;

    /**
     * Shift Performance Data constructor.
     */
    public function __construct(array $filters)
    {
        parent::__construct($filters);
    }

    /**
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function getData(string $type)
    {
        $data = collect();

        $this->applyFilters();

        switch ($type) {
            case 'list':
                $data = $this->buildCollection();
                break;

            case 'export':
                $data = $this->buildCollection();
                break;
        }
        return $data;
    }

    /**
     * Fetch and format the ShiftPerformance Data
     *
     * @return SupCollection
     */
    public function buildCollection(): SupCollection
    {
        $data = collect();
        $period = CarbonPeriod::create(Carbon::parse($this->dateRange[0]), Carbon::parse($this->dateRange[1]));
        $dates = $period->toArray();
        $verticalTypes = [
            'louvresHeadRailRigidOnly',
            'headRailOnly',
            'rigidPvc'
        ];
        foreach ($this->departments as $department) { //To iterate every departments
            $departmentData = collect();
            foreach ($this->shifts as $shift) { //To iterate every shift
                $shiftData = collect();
                $totalManufacturedBlinds = 0;
                $totalPlannedWork = 0;

                foreach ($dates as $date) {
                    if ($shift == 1) {
                        $from = Carbon::parse($date)->format('Y-m-d').' '. '06:00:00';
                        $to = Carbon::parse($date)->format('Y-m-d').' '. '13:59:59';
                    }
                    if ($shift == 2) {
                        $from = Carbon::parse($date)->format('Y-m-d').' '. '14:00:00';
                        $to = Carbon::parse($date)->format('Y-m-d').' '. '21:59:59';
                    }
                    if ($shift == 3) {
                        $from = Carbon::parse($date)->format('Y-m-d').' '. '22:00:00';
                        $to = Carbon::parse($date)->addDay()->format('Y-m-d').' '. '05:59:59';
                    }
                    if ($department != 'Despatch') {
                        $fullyManufactured = $this->manufacturedBlindsQuery($from, $to, $department, $shift);
                        $plannedWork = $this->totalPlannedQuery($from, $to, $department, $shift);
                        $peopleWorked = $this->peopleWorkedQuery($from, $to, $department);

                    }
                    if (Carbon::parse($date)->isWeekDay() || ($fullyManufactured > 0 && $plannedWork > 0 && $peopleWorked> 0)) {
                        if ($department == 'Vertical') {
                            $verticalManufacturedBlinds = 0;
                            foreach ($verticalTypes as $type) { // do this because Vertical department has three different types per Shift
                                $verticalManufacturedBlinds += $this->manufacturedBlindsQuery($from, $to, $department, $shift, $type);
                            }
                            $targetPerformance = $this->targetPerformanceTransformer($verticalManufacturedBlinds, $plannedWork);
                            $shiftData->push([
                                'shift' => $shift,
                                'date' => Carbon::parse($date)->format('Y-m-d'),
                                'fully_manufactured' => $verticalManufacturedBlinds,
                                'total_planned' => $plannedWork,
                                'people_worked' => $peopleWorked,
                                'target_performance' => $targetPerformance
                            ]);

                            $totalManufacturedBlinds += $verticalManufacturedBlinds;
                            $totalPlannedWork += $plannedWork;

                        } elseif ($department == 'Despatch') {
                            $shiftData->push([
                                'shift' => $shift,
                                'date' => Carbon::parse($date)->format('Y-m-d'),
                                'machine_packed' => $this->manufacturedBlindsQuery($from, $to, $department, $shift, null, 'P1014'),
                                'headrail_packed' => $this->manufacturedBlindsQuery($from, $to, $department, $shift, null, 'P1012'),
                                'louvres_packed' => $this->manufacturedBlindsQuery($from, $to, $department, $shift, null, 'P1013'),
                                'people_worked' => $this->peopleWorkedQuery($from, $to, $department)
                            ]);
                        } else {
                            $targetPerformance = $this->targetPerformanceTransformer($fullyManufactured, $plannedWork);
                            //get only weekdays data, if weekend has data return it.
                            $shiftData->push([
                                'shift' => $shift,
                                'date' => Carbon::parse($date)->format('Y-m-d'),
                                'fully_manufactured' => $fullyManufactured,
                                'total_planned' => $plannedWork,
                                'people_worked' => $peopleWorked,
                                'target_performance' => $targetPerformance
                            ]);

                            $totalManufacturedBlinds += $fullyManufactured;
                            $totalPlannedWork += $plannedWork;
                        }
                    }
                }

                // To get the total date range selected
                $fromStart = Carbon::parse($this->dateRange[0])->startOfDay()->addHours(6);
                $toEnd = Carbon::parse($this->dateRange[1])->addDay()->format('Y-m-d').' '. '05:59:59';

                // Add rows for Overall Total per column
                $totalRows = [
                    'shift' => $shift,
                    'date' => 'Total',
                    'fully_manufactured' => $totalManufacturedBlinds,
                    'total_planned' => $totalPlannedWork,
                    'people_worked' => $this->peopleWorkedQuery($fromStart, $toEnd, $department),
                    'target_performance' => $this->targetPerformanceTransformer($totalManufacturedBlinds, $totalPlannedWork, true),
                ];

                if ($department != 'Despatch') {
                    $shiftData->push($totalRows);
                }

                $data->push([
                    'department' => $department,
                    'shift' => $shift,
                    'data' => $shiftData
                ]);
            }
        }

        return $data;
    }

    /**
     * Fetch number of Manufactured Blinds
     *
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $department
     * @param  mixed $shift
     * @param  mixed $verticalType
     * @param  mixed $despatchType
     *
     * @return Array
     */
    private function manufacturedBlindsQuery($from, $to, $department, $shift, $verticalType = null, $despatchType = null)
    {
        if ($department == 'Technical') {
            $query = Order::query()
                ->select([DB::raw('COUNT(scanners.blindid) AS total')])
                ->join('scanners', 'orders.serial_id', 'scanners.blindid')
                ->where('orders.product_type', 'like', "%Technical%")
                ->where('scanners.processid', 'P1002')
                ->whereBetween('scanners.scannedtime', [$from, $to])
                ->first();
        }

        //Assign Process ID based on selected Department
        if ($department == 'Venetian') $processId = 'P1021';
        if ($department == 'Roller Express') $processId = 'P1024';
        if ($department == 'Roller') $processId = 'P1002';
        if ($department == 'Vertical') $processId = 'P5688737';
        if ($department == 'Despatch') $processId = $despatchType;

        if ($department == 'Venetian' || $department == 'Roller Express' || $department == 'Roller' || $department == 'Vertical' || $department == 'Despatch') {
            $query = Scanner::query()
                ->select([DB::raw('COUNT(scanners.blindid) AS total')])
                ->where('scanners.processid', $processId)
                ->whereBetween('scanners.scannedtime', [$from, $to])
                ->first();
        }

        // For the Vertical, add up these three query to get the Total Manufactured blinds
        if ($department == 'Vertical') {
            // Vertical - LouvresOnly, HeadRailOnly, RigidPVC
            if ($verticalType == 'louvresHeadRailRigidOnly') {
                $query = Scanner::query()
                    ->select([DB::raw('COUNT(scanners.blindid) AS total')])
                    ->where('scanners.processid', 'P5688737')
                    ->whereBetween('scanners.scannedtime', [$from, $to])
                    ->first();
            }
            // Vertical - HeadRailOnly
            if ($verticalType == 'headRailOnly') {
                $query = Order::query()
                    ->select([DB::raw('COUNT(scanners.blindid) AS total')])
                    ->join('scanners', 'orders.serial_id', 'scanners.blindid')
                    ->where('orders.product_type', 'HeadrailOnly')
                    ->whereBetween('scanners.scannedtime', [$from, $to])
                    ->first();
            }
            // Vertical - rigidPvc
            if ($verticalType == 'rigidPvc') {
                $query = Order::query()
                    ->select([DB::raw('COUNT(scanners.blindid) AS total')])
                    ->join('scanners', 'orders.serial_id', 'scanners.blindid')
                    ->where('orders.product_type', 'VerticalRigidPVC')
                    ->where('orders.stock_code', 'not like', "%HEADRAIL%")
                    ->whereBetween('scanners.scannedtime', [$from, $to])
                    ->first();
            }
        }

        return $query['total'];
    }

    /**
     *Fetch number of Total Planned
     *
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $department
     * @param  mixed $shift
     *
     * @return Array
     */
    private function totalPlannedQuery($from, $to, $department, $shift)
    {
        $folders = [];
        // to dynamically define the folders for shift assignments
        // then assign it to query

        // Technical
        if ($department == 'Technical') {

            //Different folders per Shifts
            if ($shift == 1) {
                $folders = [
                    'Contract - Shift 1 Team 1',
                    'Contract - Shift 1 Team 2',
                    'Contract - Shift 1 Team 3',
                    'Contract - Shift 1 Team 4',
                    'Contract Add - Shift 1 Team 1',
                    'Contract Sat - Team 1'
                ];
            }
            if ($shift == 2) {
                $folders = [
                    'Contract - Shift 2 Team 1',
                    'Contract - Shift 2 Team 2'
                ];
            }
            if ($shift == 3) {
                $folders = [
                    'Contract - Shift 3 Team 1'
                ];
            }
        }

        // Venetian
        if ($department == 'Venetian') {
            if ($shift == 1) {
                $folders = [
                    'Alu - Shift 1',
                    'Alu Add - Shift 1'
                ];
            }
            if ($shift == 2) {
                $folders = [
                    'Alu - Shift 2'
                ];
            }
            if ($shift == 3) {
                $folders = [
                    'Alu - Shift 3'
                ];
            }
        }

        // Roller Express
        if ($department == 'Roller Express') {
            if ($shift == 1) {
                $folders = [
                    'R.Exp - Shift 1 Team 1',
                    'R.Exp - Shift 1 Team 2',
                    'R.Exp Add - Shift 1 Team 1',
                    'R.Exp Add - Shift 1 Team 2'
                ];
            }
            if ($shift == 1) {
                $folders = [
                    'R.Exp - Shift 2 Team 1',
                    'R.Exp - Shift 2 Team 2',
                    'R.Exp Add - Shift 2 Team 1',
                    'R.Exp Add - Shift 2 Team 2'
                ];
            }
            if ($shift == 3) {
                $folders = [
                    'R.Exp - Shift 3 Team 1',
                    'R.Exp - Shift 3 Team 2',
                    'R.Exp - Shift 3 Team 3',
                    'R.Exp Add - Shift 3',
                    'R.Exp Add - Shift 3 A',
                    'R.Exp Add - Shift 3 B'
                ];
            }
        }

        //Roller
        if ($department == 'Roller') {
            if ($shift == 1) {
                $folers = [
                    'Roller - Shift 1 Team 1',
                    'Roller - Shift 1 Team 2',
                    'Roller Add - Shift 1 Team 1',
                    'Roller Add - Shift 1 Team 2'
                ];
            }
            if ($shift == 2) {
                $folers = [
                    'Roller - Shift 2 Team 1',
                    'Roller - Shift 2 Team 2'
                ];
            }
            if ($shift == 3) {
                $folers = [
                    'Roller - Shift 3 Team 1',
                    'Roller - Shift 3 Team 2'
                ];
            }
        }

        if ($department == 'Vertical') {
            if ($shift == 1) {
                $folders = [
                    'Vertical - Shift 1 Team 1',
                    'Vertical - Shift 1 Team 2',
                    'Vertical - Shift 1 Team 3',
                    'Vertical - Shift 1 Team 4',
                    'Vertical - Shift 1 Team 5',
                    'Vertical - Shift 1 Team 6',
                    'Vertical Add - Shift 1 Team 1',
                    'Vertical Add - Shift 1 Team 2',
                    'Vertical Add - Shift 1 Team 3', 'Vertical Add - Shift 1 Team 4',
                    'Vertical Add - Shift 1 Team 5', 'Vertical Add - Shift 1 Team 6'
                ];
            }

            if ($shift == 2) {
                $folders = [
                    'Vertical - Shift 2 Team 1',
                    'Vertical - Shift 2 Team 2',
                    'Vertical - Shift 2 Team 3',
                    'Vertical - Shift 2 Team 4',
                    'Vertical - Shift 2 Team 5',
                    'Vertical Add - Shift 2 Team 1',
                    'Vertical Add - Shift 2 Team 2',
                    'Vertical Add - Shift 2 Team 3'
                ];
            }

            if ($shift == 3) {
                $folders = [
                    'Vertical - Shift 3 Team 1',
                    'Vertical - Shift 3 Team 2',
                    'Vertical - Shift 3 Team 3',
                    'Vertical - Shift 3 Team 4',
                    'Vertical - Shift 3 Team 5',
                    'Vertical Add - Shift 3 Team 1',
                    'Vertical Add - Shift 3 Team 2',
                    'Vertical Add - Shift 3 Team 3'
                ];
            }
        }

        $query = ShiftAssignment::query()
            ->select([DB::raw('COUNT(DISTINCT shift_assignments.serial_id) as total')])
            ->join('orders', 'shift_assignments.serial_id', 'orders.serial_id')
            ->whereIn('shift_assignments.folder_name', $folders)
            ->whereDate('shift_assignments.work_date', Carbon::parse($from)->format('Y-m-d'))
            ->first();
        return $query['total'];
    }

    /**
     * Fetch number of People Worked
     *
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $department
     *
     * @return Array
     */
    private function peopleWorkedQuery($from, $to, $department)
    {
        $processes = [];
        // to dynamically define the Processes for People Worked Today
        // then assign it to query
        if ($department == 'Technical') {
            $processes = [
                'P1002',
                'P1003',
                'P1004',
                'P1005',
                'P1007',
                'P1008',
                'P1010'
            ];

            $query = Order::query()
                ->select([DB::raw('COUNT(DISTINCT scanners.employeeid) AS total')])
                ->join('scanners', 'orders.serial_id', 'scanners.blindid')
                ->where('orders.product_type', 'LIKE',  '%'.$department.'%')
                ->whereIn('scanners.processid', $processes)
                ->whereBetween('scanners.scannedtime', [$from, $to])
                ->first();
        }
        if ($department == 'Venetian') {
            $processes = [
                'P1018',
                'P1020',
                'P1019',
                'P1021'
            ];
        }

        if ($department == 'Roller Express') {
            $processes = [
                'P1026',
                'P1022',
                'P1023',
                'P1024'
            ];
        }

        if ($department == 'Roller') {
            $processes = [
                'P1000',
                'P1005',
                'P1003',
                'P1004',
                'P1002'
            ];
        }

        if ($department == 'Despatch') {
            $processes = [
                'P1012',
                'P1013',
                'P1014'
            ];
        }

        if ($department == 'Vertical') {
            $processes = [
                'P1002',
                'P1003',
                'P1004',
                'P1005',
                'P1007',
                'P1008',
                'P1010'
            ];
        }

        if ($department == 'Venetian' || $department == 'Roller Express' || $department == 'Roller' || $department == 'Vertical' || $department == 'Despatch') {
            $query = Scanner::query()
            ->select([DB::raw('COUNT(DISTINCT scanners.employeeid) AS total')])
            ->whereIn('scanners.processid', $processes)
            ->whereBetween('scanners.scannedtime', [$from, $to])
            ->first();
        }

        return $query['total'];
    }

    /**
     * Transformer value for 'Target Performance' column.
     *
     * @param  mixed $manufactured
     * @param  mixed $planned
     * @return void
     */
    private function targetPerformanceTransformer($manufactured, $planned, $isOverallTotal = false)
    {
        // To determine if we want to get all the Overall Efficiency on Target Performance
        if ($isOverallTotal) {
            $targetPerformanceTotal = $planned != 0 ? (($manufactured / $planned) * 100)  : 0;

            return [
                'value' => $targetPerformanceTotal,
                'message' => number_format((float)$targetPerformanceTotal, 2, '.', ''). '% Efficiency'
            ];
        }

        // The manufactured blinds are lower than Planned Work
        if ($manufactured < $planned) {
            return [
                'value' => $planned - $manufactured,
                'message' => $planned - $manufactured . ' left for next Shift to complete'
            ];
        }

        // The manufactured blinds exceeded the Planned Work
        if ($manufactured > $planned) {
            return [
                'value' => $planned - $manufactured,
                'message' => abs($planned - $manufactured). ' Extra made'
            ];
        }

        // The manufactured blinds completed all Planned work without extra
        if ($manufactured == $planned) {
            return [
                'value' => $planned - $manufactured,
                'message' => 'Target Completed'
            ];
        }
    }

    public function buildQuery(): self
    {
        return $this;
    }

    /**
     * Apply the filters
     *
     * @return self
     */
    public function applyFilters(): self
    {
        $this->departments = $this->getFilterValue('selectedDepartments');
        $this->shifts = $this->getFilterValue('selectedShifts');
        $this->dateRange = $this->getFilterValue('dateRange');
        $this->to = Carbon::parse($this->dateRange[0])->format('Y-m-d');
        $this->from = Carbon::parse($this->dateRange[1])->format('Y-m-d');

        return $this;
    }

    /**
     * Get the export type. The export type should have an Export counterpart.
     * Make sure you register a unique one in the Export model.
     *
     * @return string
     */
    public function exportType(): string
    {
        return Export::SHIFT_PERFORMANCE_REPORT;
    }
}
