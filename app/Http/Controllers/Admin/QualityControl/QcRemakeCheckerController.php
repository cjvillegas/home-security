<?php

namespace App\Http\Controllers\Admin\QualityControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\QcEmailRequest;
use App\Mail\QcRemakeCheckerMail;
use App\Models\Order;
use App\Models\QcEmail;
use App\Models\QcRemake;
use Exception;
use Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class QcRemakeCheckerController extends Controller
{
    /**
     * QC Remake Index
     *
     * @return void
     */
    public function index()
    {
        abort_if(Gate::denies('quality_control_remake_checker'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.quality-control.remake');
    }

    /**
     * Get Order's lists based on order_no
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function getOrders(Request $request): JsonResponse
    {
        $orders = Order::where('order_no', $request->order_no)
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * To save all checked checklist(s) on all Order Checker
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function storeOrderRemakeChecker(Request $request)
    {
        $payload = $request->all();
        DB::beginTransaction();
        try {
            $qcRemake = new QcRemake;
            $isFullyVerified = true;
            $reportNumber = $qcRemake->generateReportNumber();

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

            $qcRemake = QcRemake::with('validatedBlinds', 'user', 'order')->where('id', $qcRemake->id)->first();

            foreach (QcEmail::all() as $email) {
                Mail::to($email)->send(new QcRemakeCheckerMail($qcRemake));
            }

            return response()->json(['orderRemake' => $qcRemake]);
        }
        catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception);
            return response()->json(['message' => "Something went wrong when saving Order Remake.", "errors" => $exception], 500);
        }

    }

    /**
     * View for QC Remake Checker Report
     *
     * @return view
     */
    public function orderRemakeReport()
    {
        abort_if(Gate::denies('qc_remake_checker_report'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.quality-control.remake-report');
    }

    /**
     * Get all data for QC Remake Checker Report
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function getOrderRemake(Request $request)
    {
        $reportNumber = $request->reportNumber;
        $orderNumber = $request->orderNumber;
        $size = $request->size;

        $orderRemakes = QcRemake::
            orderBy('created_at')
            ->with('user')
            ->with('order')
            ->with('validatedBlinds.blind')
            ->when(!is_null($reportNumber), function ($query) use ($reportNumber) {
                $query->where('report_no', 'like', "%{$reportNumber}%");
            })
            ->when(!is_null($orderNumber), function ($query) use ($orderNumber) {
                $query->where('order_no', 'like', "%{$orderNumber}%");
            });

        $orderRemakes = $orderRemakes->paginate($size);

        return response()->json([
            'orderRemakes' => $orderRemakes
        ]);
    }

    /**
     * View for Email Notifications
     *
     * @return void
     */
    public function orderRemakeEmailNotification()
    {
        abort_if(Gate::denies('qc_remake_email_settings'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.quality-control.email');
    }

    /**
     * Get Emails
     *
     * @return JsonResponse
     */
    public function getEmails(): JsonResponse
    {
        $emails = QcEmail::all();

        return response()->json([
            'emails' => $emails
        ]);
    }

    /**
     * Store Qc Email
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function storeEmail(QcEmailRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $emails = QcEmail::create($request->all());
            DB::commit();

            return response()->json([
                'message' => 'Email successfully saved'
            ]);
        } catch(Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error while saving Email',
                'errors' => $exception
            ]);
        }
    }

    /**
     * Delete Email
     *
     * @param  mixed $qcEmail
     *
     * @return JsonResponse
     */
    public function deleteEmail(QcEmail $qcEmail): JsonResponse
    {
        $qcEmail->delete();
        return response()->json([
            'message' => 'Successfully Deleted'
        ]);
    }
}
