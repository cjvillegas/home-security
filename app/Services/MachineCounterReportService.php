<?php
namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MachineCounterReportService
{

    /**
     * Get today and Yesterday's list of Machines that has Machine counter data
     *
     * @param  mixed $date
     * @return DB
     */
    public function listOfMachines($date) :Collection
    {
        return DB::table('machines')
                ->select('machines.*', 'machine_counters.created_at')
                ->rightJoin('machine_counters', 'machines.id', '=', 'machine_counters.machine_id')
                ->whereDate('machine_counters.created_at', $date)
                ->groupBy('machines.id')
                ->get();
    }

    /**
     * Query for fetching machine counter data
     *
     * @param  mixed $date
     * @param  mixed $isTotal
     * @retur
     */
    public function queryString($date, $isTotal = false)
    {
        $query =  "SELECT machines.id, machines.name, machines.created_at, mc.shift_id, mc.id as machine_counter_id,
        SUM(CASE WHEN mc.machine_id = machines.id THEN mc.total_boxes ELSE 0 END) as boxes
        FROM machines
        INNER JOIN machine_counters mc ON mc.machine_id = machines.id
        WHERE mc.created_at BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59'
        ";
        /*
        *if isTotal is true, this query is for getting the OVERALL total boxes per Machine
        *if false, this query is to get total boxes per Shift only (Machine)
        */
        if (!$isTotal) {
            $query .= "\t GROUP BY mc.id";
        } else {
            $query .= "\t GROUP BY machine_id";
        }

        return $query;
    }

    /**
     * Get Machine's total boxes per shift
     *
     * @param  mixed $date
     * @return DB
     */
    public function machineCounterData($date)
    {
        return DB::select(
            $this->queryString($date, false)
        );
    }

    /**
     * Get Machine's OVERALL total boxes
     * @param  mixed $date
     * @return DB
     */
    public function totalMachineBoxes($date)
    {
        return DB::select(
            $this->queryString($date, true)
        );
    }
}
