<?php

namespace App\Jobs;

use App\Models\StockOrder\StockOrder;
use App\Notifications\StockOrder\OrderPickingListNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Barryvdh\DomPDF\Facade as PDF;
use Milon\Barcode\Facades\DNS1DFacade;

class GeneratePickingListJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    CONST EMAILS = [
        'ipswich.warehouse@stylebyglobal.com'
    ];

    /**
     * @var StockOrder
     */
    private $stockOrder;

    /**
     * @var array
     */
    private $warehouseItems;

    /**
     * Create a new job instance.
     *
     * @param StockOrder $stockOrder
     * @param array $warehouseItems
     *
     * @return void
     */
    public function __construct(StockOrder $stockOrder, array $warehouseItems)
    {
        $this->stockOrder = $stockOrder;
        $this->warehouseItems = $warehouseItems;
    }

    /**
     * Tags to be tracked in the Horizon
     *
     * @return string[]
     */
    public function tags()
    {
        return [
            'generate-picking-list-job',
            'stock_order: ' . $this->stockOrder->id,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // set the file path
        $filePath = "picking-list/{$this->stockOrder->id}/Order Picking for {$this->stockOrder->sage_order_no}.pdf";
        $barcode = DNS1DFacade::getBarcodePNG($this->stockOrder->sage_order_no, 'C128', 1.5, 50 , [1,1,1], true);

        /**
         * ohh here is where we generate the PDF.
         * In this instance, we are generating it from a blade view.
         */
        $pdf = PDF::loadView('exports.picking-list', [
            'stockOrder' => $this->stockOrder,
            'items' => $this->warehouseItems,
            'barcode' => "<img src='data:image/png;base64,{$barcode}' alt='barcode''/>"
        ])
            ->setPaper('A4', 'landscape')
            ->setOptions(["isPhpEnabled" => true, 'isRemoteEnabled' => true]);

        $pdf->output();
        $domPdf = $pdf->getDomPdf();
        $fontMetrics = $domPdf->getFontMetrics();
        $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
        $font = $fontMetrics->getFont("Verdana");
        $size = 10;
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $canvas = $domPdf->get_canvas();
        $x = ($canvas->get_width() - $width) / 2;
        $y = $canvas->get_height() - 25;
        $canvas->page_text($x, $y, $text, null, $size);

        // saving the generated PDF in our storage path
        $stored = Storage::put($filePath, $pdf->output());

        // is it really stored?
        if ($stored) {
            $this->finishPdfGenerator($filePath);

            $this->sendEmail();
        }
    }

    /**
     * Mark the ending of the process
     *
     * @param string $path
     *
     * @return void
     */
    private function finishPdfGenerator(string $path): void
    {
        $url = null;
        // check if the file exists and is saved in our disk
        if (Storage::disk()->exists($path)) {
            $url = Storage::url($path);
        }

        $this->stockOrder->picking_url = $url;
        $this->stockOrder->save();
    }

    /**
     * Send email to recipients
     *
     * @return void
     */
    private function sendEmail(): void
    {
        $emails = $this->getEmails();

        // sanity check: make sure there are valid email addresses
        if (empty($emails)) {
            return;
        }

        // loop through the emails
        foreach ($emails as $email) {
            Notification::route('mail', $email)
                ->notify((new OrderPickingListNotification($this->stockOrder, $this->stockOrder->refresh()->url)));
        }
    }

    /**
     * @return false|string[]
     */
    private function getEmails()
    {
        // if environment is in local, make sure we don't send any email
        if (__is_local()) {
            return explode(',', env('TEST_EMAIL'));;
        }

        if (__is_production()) {
            $emails = self::EMAILS;
        } else {
            $emails = explode(',', env('TEST_EMAIL'));
        }

        return $emails;
    }

    /**
     * Handle when this job fails
     *
     * @param Throwable $exception
     */
    public function failed(Throwable $exception)
    {
        Log::error('Generate Picking List Failed', [
            'stock_order' => $this->stockOrder->id,
            'exception' => $exception,
        ]);
    }
}
