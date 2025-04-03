<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\User;

class ManualPaymentController extends Controller
{
    public function bankdetails(){
        $data = DB::table('admin_bank_details')->get();
       
        return view('ManualPayment.bankdetails')->with('data',$data);
    }
    
    public function updatebankdetails(Request $request , $id){
        $currentDate = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s');
        $data =  DB::table('admin_bank_details')->where('id' , $id)->update([
             "beneficiaryname" => $request->beneficiaryname,
             "bankid"  => $request->bankid,
             "created_at" => $currentDate,
             "updated_at" => $currentDate
            ]);
        if($data){
        return back()->with('success', 'Record updated successfully');
        } else {
            return back()->with('error', 'Record not updated');
        }

    }
}