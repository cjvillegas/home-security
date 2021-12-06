<?php

namespace App\Jobs\Exports;

use App\Models\Export;
use App\Services\ExportPayloadService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Throwable;

class ShiftPerformanceJob implements ShouldQueue
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
        $performances = $data;

        $this->exportFromCollection($performances);
    }

    /**
     * Manipulat the Data and process the actual exporting
     *
     * @param  mixed $performances
     *
     * @return void
     */
    public function exportFromCollection($performances)
    {
        $from = Carbon::parse($this->dateRange[0]);
        $to = Carbon::parse($this->dateRange[1]);

        $period = CarbonPeriod::create($from, $to);
        $dates = $period->toArray();
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();

        // Set all autosize for all columns
        $letter = 'A';
        while ($letter !== 'N') {
            $alphabet[] = $letter++;
        }
        foreach ($alphabet as $alphabetKey) {
            $worksheet->getColumnDimension($alphabetKey)->setAutoSize(true);
        }

        $spacerPerTableDepartment = 0;

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

        foreach ($performances as $performance) {
            $tableDataIncrement = 0;

            // Display Department, Shift and Date Range selected.
            $worksheet->getStyle('A', ($spacerPerTableDepartment + 1))->getFont()->setBold(true);
            $worksheet->setCellValue('A'. ($spacerPerTableDepartment + 1), 'Department: '. $performance['department']);
            $worksheet->setCellValue('B'. ($spacerPerTableDepartment + 1), 'Shift: '. $performance['shift']);
            $worksheet->setCellValue('D'. ($spacerPerTableDepartment + 1), $from->format('Y-m-d'));
            $worksheet->setCellValue('E'. ($spacerPerTableDepartment + 1), $to->format('Y-m-d'));

            foreach ($performance['data'] as $departmentData) {
                // set Headers Design
                $worksheet->getStyle('A', ($spacerPerTableDepartment + 2))->getFont()->setBold(true);
                $worksheet->getStyle('B', ($spacerPerTableDepartment + 2))->getFont()->setBold(true);
                $worksheet->getStyle('C', ($spacerPerTableDepartment + 2))->getFont()->setBold(true);
                $worksheet->getStyle('D', ($spacerPerTableDepartment + 2))->getFont()->setBold(true);
                $worksheet->getStyle('E', ($spacerPerTableDepartment + 2))->getFont()->setBold(true);
                $worksheet->getStyle('F', ($spacerPerTableDepartment + 2))->getFont()->setBold(true);

                $worksheet->getStyle('A'. ($spacerPerTableDepartment + 2). ':G'. ($spacerPerTableDepartment + 2), ($spacerPerTableDepartment + 2))->applyFromArray($styleArray);
            }

            // Display data per department
            if ($performance['department'] != 'Despatch') {
                // set headers per department IF Department is not despatch
                $worksheet->setCellValue('A'. ($spacerPerTableDepartment + 2), 'Date Manufactured');
                $worksheet->setCellValue('B'. ($spacerPerTableDepartment + 2), 'Fully Manufactured');
                $worksheet->setCellValue('C'. ($spacerPerTableDepartment + 2), 'Date Planned');
                $worksheet->setCellValue('D'. ($spacerPerTableDepartment + 2), 'Total Planned');
                $worksheet->setCellValue('E'. ($spacerPerTableDepartment + 2), 'People Worked');
                $worksheet->setCellValue('F'. ($spacerPerTableDepartment + 2), 'Target Performance');

                //set data per department
                foreach ($performance['data'] as $departmentData) {
                    $worksheet->setCellValue('A'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['date']);
                    $worksheet->setCellValue('B'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['fully_manufactured']);
                    $worksheet->setCellValue('C'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['date']);
                    $worksheet->setCellValue('D'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['total_planned']);
                    $worksheet->setCellValue('E'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['people_worked']);
                    $worksheet->setCellValue('F'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['target_performance']['message']);

                    //This is to set nextline spacing logic on Excel
                    $tableDataIncrement++;
                }
            } else {
                // set headers for Despatch Department
                $worksheet->setCellValue('A'. ($spacerPerTableDepartment + 2), 'Date Manufactured');
                $worksheet->setCellValue('B'. ($spacerPerTableDepartment + 2), 'Machine Packed');
                $worksheet->setCellValue('C'. ($spacerPerTableDepartment + 2), 'Headrail Packed');
                $worksheet->setCellValue('D'. ($spacerPerTableDepartment + 2), 'Louvres Packed');
                $worksheet->setCellValue('E'. ($spacerPerTableDepartment + 2), 'People Worked');

                //set data per despatch department
                foreach ($performance['data'] as $departmentData) {
                    $worksheet->setCellValue('A'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['date']);
                    $worksheet->setCellValue('B'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['machine_packed']);
                    $worksheet->setCellValue('C'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['headrail_packed']);
                    $worksheet->setCellValue('D'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['louvres_packed']);
                    $worksheet->setCellValue('E'. ($spacerPerTableDepartment + $tableDataIncrement + 3), $departmentData['people_worked']);

                    $tableDataIncrement++;
                }
            }

            //This is to set nextline spacing logic per Department on Excel
            $spacerPerTableDepartment += count($dates) + 2;
        }

        // Declare file Type
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

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
