<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\User;

class UsdtDepositController extends Controller
{
    public function usdt_deposit_index(string $id)
    {
  //  dd($id);
	$deposits = DB::table('payins')
		->select('payins.*', 'users.name as uname', 'users.id as userid', 'users.mobile')
		->leftJoin('users', 'payins.user_id', '=', 'users.id')
		->where('payins.status', $id)
		->orderBy('payins.id', 'desc')
		->get();

        return view('usdt_deposit.deposit')->with('deposits',$deposits)->with('id',$id);
    }
  

 public function usdt_success(string $id) {
    // Fetch the details
	  $currentDate = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s');
      $details = DB::table('payins')->where('id', $id)->first();
	
    // Check if details exist
    if (!$details) {
        return redirect()->back()->with('error', 'Payin details not found.');
    }

     $userid = $details->user_id;
     $amount = $details->cash;
	 $percentageten = ($amount * 10) / 100;
	 $turnoverthree1=$percentageten*3;
	 $percentage = ($amount * 5) / 100;
	 $turnoverthree=$percentage*3;

	 
    $userdata = DB::table('users')->where('id', $userid)->where('status', 1)->first();
    if (!$userdata) {
        return redirect()->back()->with('error', 'User blocked by Admin');
    }

    $first_recharge = $userdata->first_recharge;
    $users = User::where('id', $details->user_id)->first();
    if (!$users) {
        return redirect()->back()->with('error', 'User not found.');
    }

    $referral_user = DB::table('users')->where('id', $userid)->value('referrer_id');
	


    // dd($multi_user);
    // Process for users who have their first recharge
    if($first_recharge == '1'){
        $first_recharge_status = 0;
		$bonuspersent =10;
		$total = $amount + ($amount * $bonuspersent / 100);
		$multiply_amt=$amount*5;
		//dd($multiply_amt);
		//dd($total);
        $data2 = DB::table('users')
            ->where('id', $userid)
            ->update([
                'wallet' => DB::raw("wallet + $total"),	
                'recharge' => DB::raw("recharge + $multiply_amt"),	
                'first_recharge' => $first_recharge_status
            ]);
        
        // Update the referral user's wallet
        DB::table('users')
            ->where('id', $referral_user)
            ->update([
                'wallet' => DB::raw("wallet + 0"), // No agent bonus added anymore
                'recharge' => DB::raw("recharge + 0"), // No recharge bonus added anymore
            ]);
        
        // Insert into wallet history for the user
        DB::table('wallet_histories')->insert([
            'user_id' => $userid,
            'amount' => $amount,
            'type_id' => 25, // Assuming subtypeid 26 is for regular recharge
            'created_at' => $currentDate,
            'updated_at' => $currentDate
        ]);
        
        // Update the payin status
        DB::table('payins')->where('id', $id)->update([
            'status' => 2
        ]);
		
		/// add by sudhir///
		
		$multi_user = DB::select("
    WITH RECURSIVE referral_chain AS (
        SELECT referrer_id FROM users WHERE id = $referral_user
        UNION ALL
        SELECT u.referrer_id FROM users u
        INNER JOIN referral_chain rc ON u.id = rc.referrer_id
    )
    SELECT referrer_id FROM referral_chain;
");

// Extract referrer IDs and filter out null values
$referrer_ids = array_filter(array_column($multi_user, 'referrer_id'));

// Ensure there are IDs to update
if (!empty($referrer_ids)) {
    $ids_string = implode(',', $referrer_ids);
	//dd($ids_string);
    DB::update("UPDATE `users` SET `wallet` = `wallet` + $percentage,`recharge`=`recharge`+$turnoverthree WHERE `id` IN ($ids_string)");
	DB::update("UPDATE `users` SET `wallet` = `wallet` + ?, `recharge` = `recharge` + ? WHERE `id` = ?", [$percentageten, $turnoverthree1, $referral_user]);

}
		
		/// end by sudhir///
		
		

        return redirect()->back()->with('success', 'Successfully Updated.');

    } elseif ($first_recharge == '0') {
		
		$bonuspersent =3;
		$total = $amount + ($amount * $bonuspersent / 100);
        $data2 = DB::table('users')->where('id', $userid)
            ->update([
                'wallet' => DB::raw("wallet + $total"),
                'recharge' => DB::raw("recharge + $amount")
            ]);
        
        // Update the payin status
        DB::table('payins')->where('id', $id)->update([
            'status' => 2
        ]);
		
		
				/// add by sudhir///

						$multi_user = DB::select("
					WITH RECURSIVE referral_chain AS (
						SELECT referrer_id FROM users WHERE id = $referral_user
						UNION ALL
						SELECT u.referrer_id FROM users u
						INNER JOIN referral_chain rc ON u.id = rc.referrer_id
					)
					SELECT referrer_id FROM referral_chain;
				");

				// Extract referrer IDs and filter out null values
				$referrer_ids = array_filter(array_column($multi_user, 'referrer_id'));

				// Ensure there are IDs to update
				if (!empty($referrer_ids)) {
					$ids_string = implode(',', $referrer_ids);
					//dd($ids_string);
					DB::update("UPDATE `users` SET `wallet` = `wallet` + $percentage,`recharge`=`recharge`+$turnoverthree WHERE `id` IN ($ids_string)");
					DB::update("UPDATE `users` SET `wallet` = `wallet` + ?, `recharge` = `recharge` + ? WHERE `id` = ?", [$percentageten, $turnoverthree1, $referral_user]);

				}
		
		 $monthlyDeposit = DB::table('payins')->where('user_id', $userid)->whereMonth('created_at', now()->month)->sum('cash');
		if($monthlyDeposit >= 500){
	     	$user = DB::table('users')->where('id', $userid)->first();
			if($user && $user->referrer_id){
				$referrerId = $user->referrer_id;
                $totalReferrals = DB::table('users')->where('referrer_id', $referrerId)->count();
					$activeReferrals = DB::table('users')
						->join('payins', 'users.id', '=', 'payins.user_id')
						->where('users.referrer_id', $referrerId)
						->where('payins.status', 2)
						->whereMonth('payins.created_at', now()->month)
						->groupBy('users.id')
						->havingRaw('SUM(payins.cash) >= 500')
						->select(DB::raw('COUNT(users.id) as total'))
						->get();
				$activeReferralsCount = $activeReferrals->count();
                if($totalReferrals >= 100 && $activeReferralsCount >= 100){
					  $bonusPerReferral = (2 / 100) * 500;
                    $bonusAmount = $activeReferralsCount * $bonusPerReferral; 
					 $bonusfind = DB::table('users')
						->where('id', $referrerId)
						->increment('bonus', $bonusAmount);
					  if($bonusfind){
						 DB::table('wallet_histories')->insert([
							'user_id' => $referrerId,
							'amount' => $bonusAmount,
							'type_id' => 26, // Assuming subtypeid 26 is for regular recharge
							'created_at' => $currentDate,
							'updated_at' => $currentDate
						]);  
					  }
				}
				
			}
		}

						/// end by sudhir///
        return redirect()->back()->with('success', 'Successfully Updated.');
    }
}



 public function usdt_reject(string $id){

                DB::table('payins')->where('id', $id)->update([
                        'status' => 3
                ]);

                return redirect()->back()->with('success', 'Successfully Updated.');
        }
        
        
        
        // offline payment
        
         public function offline_deposit_index(string $id)
    {

         $deposits= DB::select("SELECT payins.*,users.username AS uname,users.id As userid, users.mobile As mobile FROM `payins` LEFT JOIN 
users ON payins.user_id=users.id WHERE payins.status = '$id' && payins.type=3");

        return view('usdt_deposit.deposit')->with('deposits',$deposits)->with('id',$id);
    }


}

