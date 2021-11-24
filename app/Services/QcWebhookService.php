<?php
namespace App\Services;

use App\Models\QcFault;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use stdClass;

class QcWebhookService
{

    /**
     * Get today and Yesterday's list of Machines that has Machine counter data
     *
     * @param  mixed $date
     *
     * @return array
     */
    public function qcWebhook(QcFault $qcFault): array
    {
        $data = [];
        try {
            $client = new Client(
                [
                    'header' => [
                        'Content-Type' => 'application/json',
                    ]
                ]
            );
            $qcObj = new stdClass;
            $qcObj->customer_zoho_crm_id = optional($qcFault->scanner->order->customer)->code;
            $qcObj->affected_blinds = optional($qcFault->scanner->order)->quantity;
            $qcObj->productOne = optional($qcFault->scanner->order)->blind_type;
            $qcObj->productDetailsOne = optional($qcFault->qualityControl)->description;
            $qcObj->faultDesciptionOne = optional($qcFault)->description;
            $qcObj->invoiceNumberOne = optional($qcFault->scanner->order->orderInvoice)->invoice_no;
            $qcObj->dateManufactured = Carbon::parse($qcFault->scanner->scannedtime)->format('Y-m-d');
            $qcObj->orderNo = optional($qcFault->scanner->order)->order_no;
            $qcObj->subject = optional($qcFault->scanner->order)->order_no. '-'. optional($qcFault->scanner)->blindid;
            $qcObj->customerRef = optional($qcFault->scanner->order)->customer_order_no;

            $response = $client->post('https://hooks.zapier.com/hooks/catch/5247499/bdb71r3/', [
                'json' => $qcObj
            ]);

            $data = [
                'zoho' => 'Responso from zoho here',
                'message' => 'You have successfully saved QC Tag. Please check Zoho to confirm.',
                'status' => 200
            ];

        } catch (ClientException $exception) {
            Log::info($exception);
            $data = [
                'zoho' => 'Fail posting a request',
                'message' => 'Error occured while sending a request to Zoho Webhook',
                'status' => 410
            ];
        }

        return $data;
    }
}
