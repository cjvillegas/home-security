<?php

namespace App\Jobs\Exports;

use App\Interfaces\ServiceDataInterface;
use App\Models\Export;
use App\Models\User;
use App\Services\ExportPayloadService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TargetPerformanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ExportPayloadService
     */
    private $payload;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Export
     */
    private $export;

    /**
     * @var $dateRange
     */
    private $dateRange;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Export $export
     *
     * @return void
     */
    public function __construct(ExportPayloadService $payload, $dateRange)
    {
        $this->payload = $payload;
        $this->user = $payload->getUser();
        $this->export = $payload->getExport();
        $this->dateRange = $dateRange;
    }

    /**
     * Tags to be tracked in the Horizon
     *
     * @return string[]
     */
    public function tags()
    {
        return [
            'csv-export-job',
            'user: ' . $this->user->id,
            'export: ' . $this->export->id,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = $this->payload->getService();
        $data = $service->getData('export');
        $performances = $data['performances'];

        $this->exportFromCollection($performances);
    }

    public function exportFromCollection($performances)
    {
        $perEmployeeIncrement = 0;
        $from = Carbon::parse($this->dateRange[0]);
        $to = Carbon::parse($this->dateRange[1]);

        // Initialize Spreadsheet
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();
        //to control columns and get it length(count)
        $alphabet = array();
        $letter = 'A';
        while ($letter !== 'AO') {
            $alphabet[] = $letter++;
        }

        // To create dynamic column Header (Date Range)
        $period = CarbonPeriod::create($from, $to);
        $dates = $period->toArray();
        array_push($dates, 'Total');
        array_push($dates, 'Performance');
        // Set all autosize for all columns
        foreach ($alphabet as $alphabetKey) {
            $worksheet->getColumnDimension($alphabetKey)->setAutoSize(true);
        }

        // Loop through all Employees selected
        foreach ($performances as $performance) {
            $spacerIncrement = $perEmployeeIncrement;
            // Assign Date Range values
            $worksheet->getStyle('A'. ($perEmployeeIncrement + 1))->getFont()->setBold(true);
            $worksheet->setCellValue('A'. ($perEmployeeIncrement + 1), 'DateRange');
            $worksheet->setCellValue('B'. ($perEmployeeIncrement + 1), $from->format('Y-m-d'));
            $worksheet->setCellValue('C'. ($perEmployeeIncrement + 1), $to->format('Y-m-d'));
            // Assign Employee name value
            $worksheet->getStyle('E'. ($perEmployeeIncrement + 1))->getFont()->setBold(true);
            $worksheet->setCellValue('E'. ($perEmployeeIncrement + 1), 'Employee');
            $worksheet->setCellValue('F'. ($perEmployeeIncrement + 1), $performance['employee_name']);

            $styleArray = [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'color' => ['argb' => 'a3a49a']
                ],
            ];


            foreach ($performance['performances'] as $employeePerformance) {
                $worksheet->getStyle('E'. ($perEmployeeIncrement + 2) . ':'. ($alphabet[count($dates)+3]). ($perEmployeeIncrement + 2))->applyFromArray($styleArray);
                $targetValues = array();
                $processValues = array();
                $qcTaggedValues = array();

                // Date list Headers per Process
                $worksheet->fromArray(
                    $dates,
                    '',
                    'E'. ($spacerIncrement + 2)
                );

                $worksheet->setCellValue('A'. ($spacerIncrement + 4), 'Process');
                $worksheet->setCellValue('A'. ($spacerIncrement + 5), 'QC Tagged');

                // check if the Selected Employee has data
                if (is_array($employeePerformance)) {
                    $worksheet->setCellValue('B'. ($spacerIncrement + 4), $employeePerformance['process_name']);
                    $targetPercentage = 0;
                    foreach ($employeePerformance['data'] as $data) {

                        $targetInformation = $this->targetInfomation($performance['type'], $performance['is_new_joiner']);

                        $worksheet->setCellValue('A'. ($spacerIncrement + 3), $targetInformation['cellValue']);
                        array_push($targetValues, $data['data'][$targetInformation['targetParameter']]);
                        $targetPercentage = $employeePerformance[$targetInformation['targetPercentageParameter']];
                        $totalTargetCount = $employeePerformance[$targetInformation['totalTargetParameter']];
                        //Initialize arrays for values on column in Excel
                        array_push($qcTaggedValues, $data['data']['qc_count']);
                        array_push($processValues, $data['data']['scanners_count']);
                    }

                    // Assigning values for TOTAL and percentage
                    array_push($targetValues, $totalTargetCount);
                    array_push($processValues, $employeePerformance['total_scanners_count']);
                    array_push($processValues, $targetPercentage. '%');
                    array_push($qcTaggedValues, $employeePerformance['total_qc_tagged']);
                    array_push($qcTaggedValues, $employeePerformance['total_qc_percentage']. '%');
                }

                //Assigning values on column cells in Excel
                $worksheet->fromArray(
                    $targetValues,
                    '',
                    'E'. ($spacerIncrement + 3)
                );
                $worksheet->fromArray(
                    $processValues,
                    '',
                    'E'. ($spacerIncrement + 4)
                );
                $worksheet->fromArray(
                    $qcTaggedValues,
                    '',
                    'E'. ($spacerIncrement + 5)
                );

                // Increment that used for proper next line spacing
                $spacerIncrement += 5;
                $perEmployeeIncrement = $spacerIncrement;
                $perEmployeeIncrement += 5;
            }
            // Declare file Type
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        }

        //Check if the Folder path exists
        if (!file_exists('./public/storage/exports/'. $this->export->id. '/')) {
            mkdir('./public/storage/exports/'. $this->export->id. '/', 0777, true);
        }

        // Saving
        $filePath = './public/storage/exports/'. $this->export->id. '/'. $this->payload->getName(). $this->payload->getType();
        $writer->save($filePath);
        // Filling up Export model
        $this->finishExport('exports/'. $this->export->id. '/'. $this->payload->getName(). $this->payload->getType());

    }

    /**
     * Determine Target Type
     *
     * @param  mixed $type
     * @param  mixed $isNewJoiner
     *
     * @return Array
     */
    private function targetInfomation($type, $isNewJoiner): array
    {
        $data = array();
        // Trade Target New Joiner
        if ($type == 'trade' && $isNewJoiner) {
            $data['cellValue'] = 'Trade Target New Joiner';
            $data['targetParameter'] = 'trade_target_new_joiner';
            $data['targetPercentageParameter'] = 'trade_new_joiner_percentage';
            $data['totalTargetParameter'] = 'total_trade_target_new_joiner';
        }

        // Internet Target New Joiner
        if ($type == 'internet' && $isNewJoiner) {
            $data['cellValue'] = 'Internet New Joiner';
            $data['targetParameter'] = 'internet_target_new_joiner';
            $data['targetPercentageParameter'] = 'internet_new_joiner_percentage';
            $data['totalTargetParameter'] = 'total_internet_target_new_joiner';
        }

        //Trade Target
        if ($type == 'trade' && !$isNewJoiner) {
            $data['cellValue'] = 'Trade Target';
            $data['targetParameter'] = 'trade_target';
            $data['targetPercentageParameter'] = 'trade_target_percentage';
            $data['totalTargetParameter'] = 'total_trade_target';
        }

        //Internet Target
        if ($type == 'internet' && !$isNewJoiner) {
            $data['cellValue'] = 'Internet Target';
            $data['targetParameter'] = 'internet_target';
            $data['targetPercentageParameter'] = 'internet_target_percentage';
            $data['totalTargetParameter'] = 'total_internet_target';
        }

        return $data;
    }

    /**
     * Finalize the export. This will mark the ending of the export process
     *
     * @param string $path
     *
     * @return void
     */
    private function finishExport(string $path): void
    {
        $url = null;
        // check if the file exists and is save after the export
        if (Storage::disk()->exists($path)) {
            $url = Storage::url($path);
        }
        $export = $this->export->refresh();
        $export->status = Export::EXPORT_STATUS_COMPLETED;
        $export->url = $url;
        $export->done_at = now();
        $export->save();
    }

    /**
     * Handle when this job fails
     *
     * @param Throwable $exception
     */
    public function failed(Throwable $exception)
    {
        // set the export status to failed
        $this->export->status = Export::EXPORT_STATUS_FAILED;
        $this->export->save();

        Log::error('Error Exporting', [
            'export_id' => $this->export->id,
            'exception' => $exception,
        ]);
    }
}
