<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Bet,Card,AdminWinnerResult,User,Betlog,GameSetting,VirtualGame,BetResult,MineGameBet,PlinkoBet,PlinkoIndexList};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helper\jilli;

use Illuminate\Support\Facades\DB;

class UserManualPaymentController extends Controller{
    
   public function bankdetails($bank_id){
    $data = DB::table('admin_bank_details')->where('id', $bank_id)->get();
    if(count($data) > 0){
        return response()->json([
        'status' => 200, 
        'message' => 'Data fetched successfully',
        'data' => $data
        ],200);
    } else {
        return response()->json([
            'status' => 400,
            'message' => 'No data found', 
            'data' => []
            ],200);
    }
}
    public function uploadScreenshot(Request $request){
    $validator = Validator::make($request->all(), [
        'userid' => 'required',
		'bank_id' => 'required',
        'amount' => 'required|numeric',
        'transaction_id' => 'required|',
    ]);
			if ($validator->fails()) {
				return response()->json([
					'status' => 400,
					'message' => $validator->errors()->first(),
				], 200);
			}
		$firstRecharge = DB::table('users')->where('id', $request->userid)->value('first_recharge');
		$mindeposite = DB::table('business_settings')->where('id',17)->value('longtext');
		$maxdeposite = DB::table('business_settings')->where('id',18)->value('longtext');
		if($firstRecharge == 1){
			if($request->amount < $mindeposite){
				 return response()->json([
                        'status' => 400,
                        'message' => "Minimum first recharge amount must be ৳ $mindeposite or more.",  
                    ], 200); 
			}
			
		}
		if($request->amount < $mindeposite){
				 return response()->json([
                        'status' => 400,
                        'message' => "'Minimum deposit  must be ৳$mindeposite BDT or more.",  
                    ], 200); 
			}
		if($request->amount > $maxdeposite){
			 return response()->json([
                        'status' => 400,
                        'message' => "Maximum allowed deposit amount is ৳$maxdeposite BDT.",  
                    ], 200); 
		}
            $currentDate = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s');
            $imageData = $request->screenshot;
            $screenshotId = DB::table('payins')->insertGetId([
            'user_id' => $request->userid,
            'cash' => $request->amount,
            'order_id' => $request->transaction_id, // ab user image ki jagah apna orderid insert kr raha hai
            'status' => 1,
            'bank_id' =>$request->bank_id,
            'created_at' => $currentDate,
            'updated_at' => $currentDate
				]);
            $wallet_histories = DB::table('wallet_histories')->insert([
            "user_id" =>$request->userid,
            "amount" => $request->amount,
            "type_id" => 2,
            "description" => "Payin ",
            "created_at" => $currentDate
            ]);
            if(!$screenshotId){
                return response()->json([
                    'status' => 400,
                    'message' => 'Deposit Failed!'
                ], 200);
            }
            return response()->json([
                'status' => 200,
                'message' => 'Deposit request submitted successful!'
            ], 200);
        }
        public function addaccountdetails(Request $request): JsonResponse{
            $validator = Validator::make($request->all(), [
                'userid' => 'required',
                'name' => 'required',
                'account_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ], 200);
            }
           // dd($request->all());
            $currentDate = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s');
            $bankDetails = DB::table('bank_details')->where('userid', $request->userid)->first();
            if ($bankDetails) {
                DB::table('bank_details')->where('userid', $request->userid)->update([
                    'account_num' => $request->account_id,
                    'name' => $request->name,
                    'updated_at' => $currentDate,
                ]);
                $message = 'Account details updated successfully';
            } else {
                DB::table('bank_details')->insert([
                    'userid' => $request->userid,
                    'name' => $request->name,
                    'account_num' => $request->account_id,
                    'created_at' => $currentDate,
                    'updated_at' =>$currentDate,
                ]);
                $message = 'Account details added successfully';
            }
            return response()->json([
                'status' => 200,
                'message' => $message,
            ]);
        }
        
       public function getAccountDetails($id){
            $data = DB::table('bank_details')->select('account_num','name')->where('userid', $id)->first(); 
            if($data){
                return response()->json([
                    'status' => 200,
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'status' => 400,
                    'data' => [],  
                ], 200);
            }
        }
        
        public function withdrawmanual(Request $request){
            $validator = Validator::make($request->all(), [
              'userid' => 'required|integer',
			  'bank_id' => 'required|',
			  'account_id' => 'required|',
              'amount' => 'required|numeric|min:1'
             ]);
                if ($validator->fails()) {
                    return response()->json([
                        'status' => 400,
                        'message' => $validator->errors()->first(),
                    ], 200);
                }
            $userid = $request->userid;
            $amount = $request->amount;
			$bank_id = $request->bank_id;
			$account_id = $request->account_id;
            $date = date('YmdHis');
            $rand = rand(11111, 99999);
            $orderid = $date . $rand;
            $currentDate = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s');
            $userinfo = DB::table('users')->where('id' , $userid)->first();
            $userwallet = $userinfo->wallet;
            $usermobile = $userinfo->mobile;
           // $firstrecharge = DB::table('users')->where('id', $userid)->value('first_recharge');
			$minnum = DB::table('business_settings')->where('id',15)->value('longtext');
			$maxnum = DB::table('business_settings')->where('id',16)->value('longtext');
			//$turnover = DB::table('users')->where('id', $userid)->value('recharge');
			$pendingWithdrawals = DB::table('withdraws')->where('user_id', $userid)->where('status', 1)->count();
			if($pendingWithdrawals > 0){
				return response()->json([
							'status' => 400,
							'message' => "You have a pending withdrawal. Please wait for completion.",  
						], 200);
			}
          //  if($turnover != 0){
		//	return response()->json([
		//					'status' => 400,
		//					'message' => "Need to be bet amount must be ৳0 for withdrawal.",  
		//				], 200); 
		//	}
               if($amount < $minnum) {
				return response()->json([
					'status' => 400,
					'message' => "Minimum withdrawal is ৳$minnum BDT.",
				], 200);
				}
				if ($amount > $maxnum) {
					return response()->json([
						'status' => 400,
						'message' => "Maximum withdrawal is ৳$maxnum BDT.",
					], 200);
				}
            //    if($firstrecharge == 1){
            //          return response()->json([
            //            'status' => 400,
            //            'message' => "Withdrawal is not allowed without the first recharge.",  
            //       ], 200); 
            //  }
                if($userwallet < $amount){
                     return response()->json([
                        'status' => 400,
                        'message' => "Sorry! Your balance is insufficient for this withdrawal.",  
                    ], 200);
                }
			
                    $data = DB::table('withdraws')->insert([
                    'user_id' => $userid,
                    'mobile' => $usermobile,
                    'amount' => $amount,
                    'user_bank_id' => $account_id,
                    'order_id' => $orderid,
					'type' => $bank_id,
                    'status' => 1,
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate
                  ]);
			  
                   $data = DB::table('wallet_histories')->insert([
                    'user_id' => $userid,
                    'amount' => $amount,
                    'type_id' => 3,
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate
                  ]);
                  if($data){
                     $deduct = DB::table('users')->where('id' , $userid)->decrement('wallet' , $amount);
                      return response()->json([
                        'status' => 200,
                        'message' => "Withdrawal Successfully.",  
                    ], 200);
                  }
        }
	
	public function businesssettings(){
		$data = DB::table('business_settings')
		->whereIn('id', [15, 16, 17, 18])
		->select('title', 'longtext')
		->get();
		if($data){
			 return response()->json([
				'status' => 200,
				'data'  => $data
			],200);
		}else{
			 return response()->json([
				'status' => 400,
				'data'  => []
			],200);
		}
	}
	public function withdrawalhistory(Request $request){
            $query = DB::table('withdraws')
                ->where('user_id', $request->userid)
                ->select('amount', 'status', 'order_id','created_at',)->orderBy('id', 'desc');
            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->date) {
                $query->whereDate('created_at', '=', $request->date);
            }
            $query->orderBy('created_at', 'desc');
            $result = $query->get();
            if ($result->isEmpty()) {
                return response()->json([
                    "status" => 400,
                    "data" => []
                ], 200);
            }
            return response()->json([
                "status" => 200,
                "data" => $result
            ], 200);
        }
	public function deposite(Request $request){
            $query = DB::table('payins')
                ->where('user_id', $request->userid)
                ->select('cash', 'status', 'order_id','created_at',)->orderBy('id', 'desc');
            if ($request->status) {
                $query->where('status', $request->status);
            }
		    if($request->bank_id) {
                $query->whereDate('bank_id', '=', $request->bank_id);
            }
            if ($request->date) {
                $query->whereDate('created_at', '=', $request->date);
            }
            $query->orderBy('created_at', 'desc');
            $result = $query->get();
            if ($result->isEmpty()) {
                return response()->json([
                    "status" => 400,
                    "data" => []
                ], 200);
            }
            return response()->json([
                "status" => 200,
                "data" => $result
            ], 200);
        }
	    
	   public function type(){
		  $ids = [2, 3, 25, 26, 5, 8];
		 $data = DB::table('types')->select('id', 'name')->whereIn('id', $ids)->get();
			if($data){
				return response()->json([
				'status' => 200, 
				'message' => 'Data fetched successfully',
				'data' => $data
				],200);
			} else {
				return response()->json([
					'status' => 400,
					'message' => 'No data found', 
					'data' => []
					],200);
			}
	   }
	
	    public function walletHistories(Request $request){
        $query = DB::table('wallet_histories')
                ->where('user_id', $request->userid)
                ->select('amount','created_at');
            if ($request->type_id){
                $query->where('type_id', $request->type_id);
            }    
            if ($request->date) {
                $query->whereDate('created_at', '=', $request->date);
            }
            $query->orderBy('created_at', 'desc');
            $result = $query->get();
            if ($result->isEmpty()){
                return response()->json([
                    "status" => 400,
                    "data" => []
                ], 200);
            }
            return response()->json([
                "status" => 200,
                "data" => $result
            ], 200);
     }
	
}


