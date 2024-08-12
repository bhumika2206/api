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
use DB;


class PaymentController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        try {
            $payments  = DB::table('payments')
                    ->select('amount','user_id','users.email')
                    ->join('users', 'users.id', '=', 'payments.user_id')
                    ->get();
            $response = [];
            foreach($payments as $payment){
                $response[] = [
                    'user_email' => $payment->email,
                    'available_balance' => $payment->amount
                ];
            }
        
        
            return $this->sendResponse($response, 'Payment balance retrieved successfully.');
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getBalance(Request $request): JsonResponse
    {
        try {
       
            $payments  = DB::table('payments')
                    ->select('amount','user_id','users.email')
                    ->join('users', 'users.id', '=', 'payments.user_id')
                    ->where('users.email', $request->email)
                    ->first();

            if($payments){

                $response = [
                    'user_email' =>$payments->email,
                    'availbale_balance' => $payments->amount 
                ];
        
                return $this->sendResponse($response, 'Payment get balance successfully.');
            }else{
                return $this->sendError('', 'User not found.');

            }
        }catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function transfer(Request $request): JsonResponse
    {
        // $users = User::where('email', $request->email)->first();
        $user = User::where('email', $request->transfer_user_id)->first();
        if($user){
            $payments  = DB::table('payments')
                    ->select('amount','user_id','users.email')
                    ->join('users', 'users.id', '=', 'payments.user_id')
                    ->where('users.email', $request->email)
                    ->first();

            if($payments->amount >= $request->transfer_amount){
                $transfer = new PaymentTransfer();
                $transfer->user_id = $payments->user_id;
                $transfer->transfer_user_id = $user->id;
                $transfer->transfer_amount = $request->transfer_amount;
                $transfer->status = 'success';
                if($transfer->save()){
                    $payment = Payment::where('user_id', $payments->user_id)->first();
                    $payment->amount = ($payments->amount - $request->transfer_amount);
                    // $payment->save();
                    if($payment->save()){
                        $payment2 = Payment::where('user_id', $user->id)->first();
                        $payment2->amount = ($payment2->amount + $request->transfer_amount);
                        $payment2->save();
                    }
                    return $this->sendResponse($transfer, 'Payment transfer successfully.');
                }   else{
                    return $this->sendError('', 'Something went wrong !');
                } 
                
            }else{
                $transfer = new PaymentTransfer();
                $transfer->user_id = $payments->user_id;
                $transfer->transfer_user_id = $user->id;
                $transfer->transfer_amount = $request->transfer_amount;
                $transfer->status = 'false';
                $transfer->save();
                return $this->sendError('', 'Insuficient Balance');
            }
        }else{
            return $this->sendError('', 'User not found.');

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
