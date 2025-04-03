<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UsdtWidthdrawController extends Controller
{
    public function usdt_widthdrawl_index($id)
    {
		//dd($id);
        // Fetch all records from the Project_maintenance model
        $widthdrawls = DB::table('withdraws')->where('status', $id)->get();
        return view('usdt_withdraw.index', compact('widthdrawls'))->with('id', $id);
    }

    public function usdt_success(Request $request, $id)
    {
        // Check if the session has an 'id' key
        if ($request->session()->has('id')) {
            // Use parameter binding to prevent SQL injection
            DB::table('withdraws')
                ->where('id', $id)
                ->update(['status' => 2]);
            // Redirect with route and parameters
            return back()->with('message' ,"Successfully updated");
        } else {
            // Redirect to login if session does not have 'id'
            return redirect()->route('login');
        }
    }

    public function usdt_reject(Request $request, $id)
    {
        // Retrieve the withdrawal history for the given id
        $data = DB::table('withdraw_histories')->where('id', $id)->first();
        
        // If no data is found, handle it appropriately
        if (!$data) {
            // Handle the case where no withdrawal history is found
            return redirect()->route('usdt_widthdrawl', ['status' => 1])->with('error', 'Withdrawal history not found.');
        }

        $amt = $data->amount;
        $useid = $data->user_id;

        // Check if the session has an 'id' key
        if ($request->session()->has('id')) {
            // Use Query Builder to perform updates safely
            DB::table('withdraw_histories')->where('id', $id)->update(['status' => 3]);
            DB::table('users')->where('id', $useid)->increment('wallet', $amt);
            
            // Redirect with route and parameters
            return redirect()->route('usdt_widthdrawl', ['status' => 1])->with('key', 'value');
        } else {
            // Redirect to login if session does not have 'id'
            return redirect()->route('login');
        }
    }

    public function all_success(Request $request)
    {
        // Check if the session has an 'id' key
        if ($request->session()->has('id')) {
            // Use Query Builder to perform the update safely
            DB::table('withdraw_histories')
                ->where('status', 1)
                ->update(['status' => 2]);

            // Retrieve updated withdrawal histories
            $widthdrawls = DB::table('withdraw_histories')->get();

            // Return the view with the updated data
            return view('widthdrawl.index', compact('widthdrawls'))->with('id', '1');
        } else {
            // Redirect to login if session does not have 'id'
            return redirect()->route('login');
        }
    }
}
