<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseController as BaseController;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\PaymentTransfer;
use Carbon\Carbon;


class PaymentController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        // $users = User::where('email', $request->email)->first();
        $payments = Payment::with(['User'])->get();
        $response = [];
        foreach($payments as $payment){
            $response[] = [
                'user_email' => $payment->amount,
                'available_balance' => $payment->User->email
            ];
        }
       
    
        return $this->sendResponse($response, 'Payment balance retrieved successfully.');
    }

    public function getBalance(Request $request): JsonResponse
    {
        $$users = User::where('email', $request->email)->first();
        $payments = Payment::where('user_id', $users->id)->sum('amount');
        $paymentTransfer = PaymentTransfer::where('user_id', $users->id)->sum('transfer_amount');
        $amount = $payments - $paymentTransfer;
        $response = [
            'user_email' => $users->email,
            'availbale_balance' => $amount
        ];
    
        return $this->sendResponse($response, 'Payment get balance successfully.');
    }

    public function transfer(Request $request): JsonResponse
    {
        $users = User::where('email', $request->email)->first();
        $user = User::where('email', $request->transfer_user_id)->first();
        $payments = Payment::where('user_id', $users->id)->sum('amount');
        if($payments >= $request->transfer_amount){
            $transfer = new PaymentTransfer();
            $transfer->user_id = $users->id;
            $transfer->transfer_user_id = $user->id;
            $transfer->transfer_amount = $request->transfer_amount;
            $transfer->status = 'success';
            if($transfer->save()){
                $payment = Payment::where('user_id', $users->id)->first();
                $payment->amount = ($payments - $request->transfer_amount);
                // dd($request->transfer_amount - $payments);
                $payment->save();
                return $this->sendResponse($transfer, 'Payment transfer successfully.');
            }   else{
                return $this->sendError('', 'Something went wrong !');
            } 
            
        }else{
            $transfer = new PaymentTransfer();
            $transfer->user_id = $users->id;
            $transfer->transfer_user_id = $user->id;
            $transfer->transfer_amount = $request->transfer_amount;
            $transfer->status = 'false';
            return $this->sendError('', 'Insuficient Balance');
        }
    
    }

    public function history(Request $request): JsonResponse
    {
        $query = PaymentTransfer::query();

        if ($request->filled('amount')) {
            list($min, $max) = explode(",", $request->amount);

            $query->where('transfer_amount', '>=', $min)
                  ->where('transfer_amount', '<=', $max);
        }

        if ($request->filled('date')) {
            list($start_date, $end_date) = explode(",", $request->date);
            $start = Carbon::parse($start_date)->toDateTimeString();

$end = Carbon::parse($end_date)->toDateTimeString();

            $query->whereBetween('created_at', [$start, $end]);

        }

        if ($request->filled('status')) {
            $query->where('status', '=', $request->status);
        }

        if ($request->filled('email')) {
            $users = User::where('email', $request->email)->first();

            $query->where('user_id', '=', $users->id);
        }

        $sql = $query->get();

        return $this->sendResponse($sql, 'Payment history retrieve successfully.');
    }

    
}
