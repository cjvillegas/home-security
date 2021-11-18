<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\QcRemake;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QcRemakeCheckerController extends Controller
{
    /**
     * QC Remake Index
     *
     * @return void
     */
    public function index()
    {
        return view('admin.quality-control.remake');
    }

    public function getOrders(Request $request)
    {
        $orders = Order::where('order_no', $request->order_no)
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function storeOrderRemakeChecker(Request $request)
    {
        $payload = $request->all();
        DB::beginTransaction();
        try {
            $qcRemake = new QcRemake;
            $isFullyVerified = true;
            $reportNumber = (new QcRemake())->generateReportNumber();

            $qcRemake->report_no = $reportNumber;
            $qcRemake->user_id = Auth::user()->id;
            $qcRemake->created_at = now();
            $qcRemake->save();

            $orderNo = null;

            // iterate and save each validated Blinds
            foreach ($payload as $data) {

                if (count($data['checkedQuestions']) < 6) {
                    $isFullyVerified = false;
                }

                $orderNo = $data['order_no'];
                $qcRemake->validatedBlinds()->create([
                    'blind_id' => $data['serial_id'],
                    'barcode' => $data['serial_id'],
                    'question_key' => $data['checkedQuestions'],
                    'reason' => isset($data['reason']) ? $data['reason'] : null,
                    'is_fully_verified' => $isFullyVerified
                ]);
            }

            $qcRemake->is_fully_verified = $isFullyVerified;
            $qcRemake->order_no = $orderNo;
            $qcRemake->save();
            DB::commit();
            $qcRemake = QcRemake::with('validatedBlinds')->where('id', $qcRemake->id)->first();

            return response()->json(
                [
                    'orderRemake' => $qcRemake
                ]
            );
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['message' => "Something went wrong when saving Order Remake."], 500);
        }

    }

    public function orderRemakeReport()
    {
        return view('admin.quality-control.remake-report');
    }

    public function getOrderRemake(Request $request)
    {
        Log::info($request->all());
        $orderRemakes = QcRemake::
            orderBy('created_at')
            ->with('user')
            ->with('order')
            ->with('validatedBlinds.blind')
            ->get();
        return response()->json([
            'orderRemakes' => $orderRemakes
        ]);
    }
}
