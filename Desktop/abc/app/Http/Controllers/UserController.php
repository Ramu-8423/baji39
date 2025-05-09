<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\{All_image,User,withdraw,Bet,Payin};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
//     public function user_create(Request $request)
//     {
// 		$u_id = $request->u_id;
// 		$mobile = $request->mobile;

// 		$perPage = 10;
		
// 		$value = $request->session()->has('id');
	
//         if(!empty($value))
//         {

// 			// $users = DB::select("SELECT e.*, m.name AS sname FROM users e LEFT JOIN users m ON e.referrer_id = m.id; ");
			
// 			$query = DB::table('users')
// 				->leftJoin('users as m', 'users.referrer_id', '=', 'm.id')
// 				->select('users.*', 'm.name as sname');
		
// 			// Apply filters if provided
// 			if (!empty($u_id)) {
// 				$query->where('users.u_id', 'LIKE', '%' . $u_id . '%');
// 			}
// 			if (!empty($mobile)) {
// 				$query->where('users.mobile', 'LIKE', '%' . $mobile . '%');
// 			}

// 			// Execute the query and paginate results
// 			$users = $query->paginate($perPage);
        
//         return view ('user.index', compact('users'));
//         }
//         else
//         {
//           return redirect()->route('login');  
//         }

//     }
    
//      public function user_create(Request $request)
//     {
// 		$u_id = $request->u_id;
// 		$mobile = $request->mobile;

// 		$perPage = 10;
		
// 		$value = $request->session()->has('id');
	
//         if(!empty($value))
//         {

// 			// $users = DB::select("SELECT e.*, m.username AS sname FROM users e LEFT JOIN users m ON e.referral_user_id = m.id; ");
			
// 			$query = DB::table('users')
// 				->leftJoin('users as m', 'users.referrer_id', '=', 'm.id')
// 				->select('users.*', 'm.name as sname');
		
// 			// Apply filters if provided
// 			if (!empty($u_id)) {
// 				$query->where('users.u_id', 'LIKE', '%' . $u_id . '%');
// 			}
// 			if (!empty($mobile)) {
// 				$query->where('users.mobile', 'LIKE', '%' . $mobile . '%');
// 			}

// 			// Execute the query and paginate results
// 			$users = $query->paginate($perPage);
        
//         return view ('user.index', compact('users'));
//         }
//         else
//         {
//           return redirect()->route('login');  
//         }
        
//     }

public function user_create(Request $request)
{
    $u_id = $request->u_id;
    $mobile = $request->mobile;

    $perPage = 10;

    if ($request->session()->has('id')) {
        
        $query = User::query()
            ->leftJoin('users as m', 'users.referrer_id', '=', 'm.id')
            ->select('users.*', 'm.mobile as smobile');

        // Apply filters if provided
        if (!empty($u_id)) {
            $query->where('users.u_id', 'LIKE', '%' . $u_id . '%');
        }
        if (!empty($mobile)) {
            $query->where('users.mobile', 'LIKE', '%' . $mobile . '%');
        }
		 $query->where('users.email', 'NOT LIKE', 'demo%');
        // Execute the query and paginate results
        $users = $query->paginate($perPage);

        return view('user.index', compact('users'));
    } else {
        return redirect()->route('login');  
    }
}
    
    public function BlockUserList(Request $request)
{
    $u_id = $request->u_id;
    $mobile = $request->mobile;
    $perPage = 10;

    // Check if session has 'id'
    if ($request->session()->has('id')) {
        
        // Create a base query using Eloquent's query builder
        $query = User::leftJoin('users as m', 'users.referrer_id', '=', 'm.id')
                     ->select('users.*', 'm.name as sname')
                     ->where('users.status', 0);

        // Apply filters if provided
        if (!empty($u_id)) {
            $query->where('users.u_id', 'LIKE', '%' . $u_id . '%');
        }
        if (!empty($mobile)) {
            $query->where('users.mobile', 'LIKE', '%' . $mobile . '%');
        }

        // Execute the query and paginate results
        $users = $query->paginate($perPage);
        
        return view('user.index', compact('users'));
    } else {
        return redirect()->route('login');
    }
}


public function export_users()
{
    // Fetching data from the User model
    $users = User::select([
        'id',
        'u_id',
        'name',
        'email',
        'mobile',
        'referrer_id',
        'wallet',
        'winning_wallet',
        'commission',
        'bonus',
        'turnover',
        'today_turnover',
        'password',
        'created_at',
        'status',
    ])->get();

    // Map users' data to the desired format
    $data = $users->map(function ($user) {
        return [
            'ID' => $user->id,
            'User ID' => $user->u_id,
            'User Name' => $user->name,
            'Email' => $user->email,
            'Mobile' => $user->mobile,
            'Sponser' => $user->referrer_id,
            'Wallet' => $user->wallet,
            'Winning Wallet' => $user->winning_wallet,
            'Commission' => $user->commission,
            'Bonus' => $user->bonus,
            'Turnover' => $user->turnover,
            'Today_Turnover' => $user->today_turnover,
            'Password' => $user->password,
            'Date' => $user->created_at,
            'Status' => $user->status,
        ];
    });

    // Convert data to array format
    $dataArray = $data->toArray();

    // Define CSV headers
    $header = [
        'ID',
        'User ID',
        'User Name',
        'Email',
        'Mobile',
        'Sponser',
        'Wallet',
        'Winning Wallet',
        'Commission',
        'Bonus',
        'Turnover',
        'Today_Turnover',
        'Password',
        'Date',
        'Status',
    ];

    // Set CSV headers for file download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="users_data.csv"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add header row to CSV
    fputcsv($output, $header);

    // Add data rows to CSV
    foreach ($dataArray as $row) {
        fputcsv($output, $row);
    }

    // Close output stream
    fclose($output);
    exit(); // Exit after download is complete
}


public function user_details(Request $request, $id)
{
    if ($request->session()->has('id')) {

        $users = Bet::where('userid', $id)->get();
        $withdrawal = withdraw::where('user_id', $id)->get();
        $dipositess = Payin::where('user_id', $id)->get();
        
        return view('user.user_detail', compact('dipositess', 'users', 'withdrawal'));
    } else {
        return redirect()->route('login');
    }
}


	public function user_active(Request $request, $id)
{
    if($request->session()->has('id')) 
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 1;
            $user->save(); 
        }
        return redirect()->route('users');
    } 
    else 
    {
        return redirect()->route('login');
    }
}



public function user_inactive(Request $request, $id)
{
    
    if ($request->session()->has('id')) 
    {
        
        User::where('id', $id)->update(['status' => 0]);

        return redirect()->route('users');
    } 
    else 
    {
        return redirect()->route('login');
    }
}


public function password_update(Request $request, $id)
{
    if ($request->session()->has('id')) {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        
        $user->password = $request->password; 
        $user->save();

        return redirect()->route('users');
    } else {
        return redirect()->route('login');
    }
}

public function wallet_store(Request $request, $id)
{
    // Check if session contains 'id' to verify if the user is logged in
    if ($request->session()->has('id')) {
        
        // Retrieve the wallet input
        $wallet = $request->input('wallet');
		$currentDate = Carbon::now('Asia/Kolkata')->format('Y-m-d h:i:s');
        // Validate the wallet input
        $request->validate([
            'wallet' => 'required|numeric|min:1',  // Ensure wallet has a valid number greater than 0
        ]);

        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if ($user) {
            $first_recharge = $user->first_recharge;
            //dd($first_recharge);
            // Check if it's the user's first recharge
            if ($first_recharge == 1) {
                // Set first_recharge to 0 and save
                $user->first_recharge = 0;
                $user->save();
            }

            // Increment the wallet and other fields
            $user->increment('wallet', $wallet);
            $user->increment('deposit_amount', $wallet);
            $user->increment('total_payin', $wallet);
            $user->increment('recharge', $wallet);

            // Create a new Payin record
            Payin::create([
                'user_id' => $user->id,
                'cash' => $wallet,
                'order_id' => 'via Admin',  // Assuming fixed order_id for admin
                'type' => 0,  // Assuming '0' is the type for the admin transaction
                'status' => 2,  // Assuming '2' represents a success status
				'updated_at' => $currentDate,
				'created_at' => $currentDate
            ]);

            // Redirect with success message
            return redirect()->route('users')->with('success', 'Wallet updated successfully.');
        } else {
            // If user is not found, redirect with error message
            return redirect()->route('users')->with('error', 'User not found.');
        }

    } else {
        // Redirect to login page if session id is not set
        return redirect()->route('login');
    }
}

public function wallet_store_old(Request $request, $id)
{
    if ($request->session()->has('id')) {
        $wallet = $request->input('wallet');

        $request->validate([
            'wallet' => 'required|numeric|min:1',  // Ensure wallet has a valid number greater than 0
        ]);

        $user = User::find($id);
		//$first_recharge=$user->first_recharge;
		//dd($first_recharge);

        if ($user) {
            $user->increment('wallet', $wallet);
            $user->increment('deposit_amount', $wallet);
            $user->increment('total_payin', $wallet);
            $user->increment('recharge', $wallet);

            Payin::create([
                'user_id' => $user->id,
                'cash' => $wallet,
                'order_id' => 'via Admin',  // Assuming fixed order_id for admin
                'type' => 0,  // Assuming '2' is the type you need
                'status' => 2,  // Assuming '2' represents success status
            ]);

            return redirect()->route('users')->with('success', 'Wallet updated successfully.');
        } else {
            return redirect()->route('users')->with('error', 'User not found.');
        }
    } else {
        return redirect()->route('login');
    }
}


public function wallet_subtract(Request $request, $id)
{
    date_default_timezone_set('Asia/Kolkata');
    $ammount = $request->wallet;

    // Check if the request has a wallet amount
    if ($request->has('wallet')) {
        // Retrieve the user using Eloquent
        $user = User::find($id);

        // Check if user exists
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Check if the wallet amount is sufficient
        if ($user->wallet < $ammount) {
            return redirect()->back()->with('error', 'Insufficient wallet balance.');
        }

        // Subtract the amount from the wallet
        $user->wallet -= $ammount;
        $user->save();

        return redirect()->route('users')->with('success', 'Amount subtracted successfully!');
    }

    return redirect()->back()->with('error', 'No amount specified.');
}


	
// 		public function password_store(Request $request ,$id)
//     {
// 		date_default_timezone_set('Asia/Kolkata');
// 		$date=date('Y-m-d H:i:s');
// 		$value = $request->session()->has('id');
	
//         if(!empty($value))
//         {
//       $password=$request->password;
			
// 			$sponser_mobile =$request->sponser_mobile;
			
//      //dd($wallet);
//          $data = DB::update("UPDATE `users` SET `password` = $password  WHERE id = $id;");
			
// 			if($sponser_mobile){

//     $sponser_data = DB::table('users')->where('mobile', $sponser_mobile)->first();
    
//     if ($sponser_data) {
     
//         $sponser_id = $sponser_data->id;
  
//         DB::table('users')->where('id', $id)->update(['referrer_id' => $sponser_id]);
//     }
// }

			
//              return redirect()->route('users');
// 			  }
//         else
//         {
//           return redirect()->route('login');  
//         }
//       }

public function password_store(Request $request, $id)
{
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d H:i:s');

    if ($request->session()->has('id')) {
        $password = $request->password;
        $sponser_mobile = $request->sponser_mobile;

        // Directly updating the user's password
        User::where('id', $id)->update(['password' => $password]);

        // Updating the referrer_id if sponsor's mobile is provided and exists
        if ($sponser_mobile) {
            $sponser = User::where('mobile', $sponser_mobile)->first();

            if ($sponser) {
                User::where('id', $id)->update(['referrer_id' => $sponser->id]);
            }
        }

        return redirect()->route('users');
    } else {
        return redirect()->route('login');
    }
}
	
	
	
	
	
		public function user_mlm(Request $request,$id)
    {
			
$value = $request->session()->has('id');
	
        if(!empty($value))
        {

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://mahajong.club/admin/index.php/Mahajongapi/level_getuserbyrefid?id=$id",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Cookie: ci_session=itqv6s6aqactjb49n7ui88vf7o00ccrf'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$data= json_decode($response);

			
      
		
             return view ('user.mlm_user_view')->with('data', $data);
			
			  }
        else
        {
           return redirect()->route('login');  
        }
      }
      
      
      
     public function registerwithref($id){
         
         $ref_id = User::where('referral_code',$id)->first();
        $country = DB::table('country')
            ->orderByRaw("id = 18 DESC") // ID 18 ko sabse pehle lane ke liye
            ->orderBy('name', 'ASC') // Baaki sab ko alphabetically sort karne ke liye
            ->get();

		// dd($country);
         return view('user.newregister')
			 ->with('country',$country)
			 ->with('ref_id',$ref_id);
         
     }
     
      protected function generateRandomUID() {
					$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
					$digits = '0123456789';

					$uid = '';

					// Generate first 4 alphabets
					for ($i = 0; $i < 4; $i++) {
						$uid .= $alphabet[rand(0, strlen($alphabet) - 1)];
					}

					// Generate next 4 digits
					for ($i = 0; $i < 4; $i++) {
						$uid .= $digits[rand(0, strlen($digits) - 1)];
					}

					return $this->check_exist_memid($uid);
					
				}
				
				protected function check_exist_memid($uid)
                    {
                        $check = User::where('u_id', $uid)->first();
                        if ($check) {
                            return $this->generateRandomUID();
                        } else {
                            return $uid;
                        }
                    }

// 	  protected function check_exist_memid($uid){
// 					$check = DB::table('users')->where('u_id',$uid)->first();
// 					if($check){
// 						return $this->generateRandomUID(); // Call the function using $this->
// 					} else {
// 						return $uid;
// 					}
// 				}
      
//         public function register_store(Request $request,$referral_code)
//       {
//           $validatedData = $request->validate([
//             'mobile' => 'required',
//             'password' => 'required|string|min:6|confirmed', 
//             'password_confirmation' =>'required|string|min:6', 
//             'email' => 'required | unique:users,email',
// 			'otp' => 'required',
//         ]);
//           //dd($ref_id);

//       $refer = DB::table('users')->where('referral_code', $referral_code)->first();
// 	 	if ($refer !== null) {
// 			$referrer_id = $refer->id;

						
// 	$userdata =  DB::table('users')->where('mobile', $request->mobile)->where('otp', $request->otp)
//     ->update([
//         'email' => $request->email,
//         'wallet' => 20,
//         'password' => $request->password,
//         'referrer_id' =>$referrer_id,
//         'status' => 1,
//     ]);

// 	if($userdata){
			
//      DB::select("UPDATE `users` SET `yesterday_register`=yesterday_register+1 WHERE `id`=$referrer_id");
	
//      return redirect(str_replace('https://admin.', 'http://', "https://nandigame.live"));

// 	}else{
		
// 		 return redirect()->back()->with('error', 'Mobile or Otp not match, Contact to admin..!');
		
// 	}
		
		
// }
// }

public function register_store_old(Request $request, $referral_code)
{
    $validatedData = $request->validate([
        'mobile' => 'required',
        'password' => 'required|string|min:6|confirmed', 
        'password_confirmation' => 'required|string|min:6', 
        'email' => 'required|unique:users,email',
        'otp' => 'required',
    ]);

    // Retrieve referrer information
    $referrer = User::where('referral_code', $referral_code)->first();

    if ($referrer) {
        $referrer_id = $referrer->id;

        // Attempt to find and update the user
        $user = User::where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->first();

        if ($user) {
            $user->update([
                'email' => $request->email,
                'wallet' => 20,
                'password' => $request->password, // Hashing the password
                'referrer_id' => $referrer_id,
                'status' => 1,
            ]);

            // Update referrer's registration count
            $referrer->increment('yesterday_register');

            return redirect(str_replace('https://admin.', 'http://', "https://jupitergames.app/"));
        } else {
            return redirect()->back()->with('error', 'Mobile or OTP not match, Contact to admin..!');
        }
    }

    return redirect()->back()->with('error', 'Invalid referral code.');
}

	
	public function register_store111(Request $request, $referral_code)
{
    $validatedData = $request->validate([
        'mobile' => 'required',
        'password' => 'required|string|min:6|confirmed', 
        'password_confirmation' => 'required|string|min:6', 
        'email' => 'required|unique:users,email',
        'otp' => 'required',
    ]);

    // Retrieve referrer information
    $referrer = User::where('referral_code', $referral_code)->first();
    $randomName = 'User_' . strtoupper(Str::random(5));
		 $randomReferralCode = 'ZUP' . strtoupper(Str::random(4));

    if ($referrer) {
        $referrer_id = $referrer->id;

        // Attempt to find and update the user
       
           DB::table('users')->insert([
    'email' => $request->email,
	'name'=>$randomName,
	'u_id' => $this->generateSecureRandomString(8),
    'mobile' => $request->mobile,
    'wallet' => 28,
	'referral_code' => $randomReferralCode,
    'password' => $request->password,  // Hash the password
    'referrer_id' => $referrer_id,
    'status' => 1,
]);
        
            
            // Update referrer's registration count
            $referrer->increment('yesterday_register');

            return redirect(str_replace('https://admin.', 'http://', "https://jupitergames.app/"));
       
    }

    return redirect()->back()->with('error', 'Invalid referral code.');
}
	


public function register_store(Request $request, $referral_code)
{
    $validatedData = $request->validate([
        'mobile' => 'required|unique:users,mobile',
		'countrycode' => 'required|',
        'password' => 'required|string|min:6|confirmed',
        'password_confirmation' => 'required|string|min:6',
        'email' => 'required|unique:users,email',
    ]);

    $referrer = User::where('referral_code', $referral_code)->first();
    $randomName = 'User_' . strtoupper(Str::random(5));
    $uid = $this->generateSecureRandomString(6);
    $uniqueId = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 16);
    $baseUrl = URL::to('/');
        $amount=100;
	    $referrer_bonus = $amount * 0.10;
		$turnover=$amount*5;
    if ($referrer) {
        $data = [
            'name' => $randomName,
            'u_id' => $uid,
            'mobile' => $request->mobile,
			'countrycode' => $request->countrycode,
            'password' => $request->password,
            'image' => $baseUrl . "/image/download.png",
            'status' => 1,
            'referral_code' => $uid,
            'referrer_id' => $referrer->id,
            'wallet' => $amount,
			'recharge' => $turnover,
            'email' => $request->email,
            'spribe_id' => $uniqueId
        ];

        $referrer->increment('yesterday_register');
		$refer_id = $referrer->id;
		if ($refer_id) {
			DB::table('users')->where('id', $referrer->id)->increment('winning_wallet', $referrer_bonus);
			  DB::table('wallet_histories')->insert([
            'user_id'     => $referrer->id,
            'amount'      => $referrer_bonus,
            'type_id'     => 8,
            'description' => 'Invitation Bonus',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
		}

        // External API setup Jilli
        $jilliHeaders = [
            'Authorization' => 'Bearer FEGISfG7LhMA6',
            'Content-Type' => 'application/json',
            'authorizationtoken' => 'Bearer 729582852564966'
        ];
        $jilliPayload = ['payload' => base64_encode(json_encode(['mobile' => $request->mobile]))];
     try {
    $jilliResponse = Http::withHeaders($jilliHeaders)->post(
        'https://api.gamebridge.co.in/seller/v1/get-newjilli-game-registration',
        $jilliPayload
    );

    Log::info('Jilli API Response:', ['response' => $jilliResponse->body()]);

    $jilliData = json_decode($jilliResponse->body(), true);
    Log::info('Jilli API Decoded Response:', ['response' => $jilliData]);

    if ($jilliResponse->successful()) {
        if (isset($jilliData['accountNo'])) {
            $data['accountNo'] = $jilliData['accountNo'];
            DB::table('users')->insert($data);
            return back()->with('message', 'Registration successful!');
        } else {
			$data['accountNo'] = $request->mobile ;
            DB::table('users')->insert($data);
			return back()->with('message', 'Registration successful!');
        }
    } else {
        return back()->with('error', 'API request failed. Please try again.')->withInput();
    }
} catch (\Exception $e) {
    Log::error('API Error:', ['error' => $e->getMessage()]);
    return back()->with('error', 'Something went wrong: ' . $e->getMessage());
}

return redirect()->back()->with('error', 'Failed to register. Please try again.')->withInput();

}
}

     private function generateSecureRandomString($length = 8)
{
	//$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Only uppercase letters
    $characters = '0123456789'; // You can expand this to include more characters if needed.
    $randomString = '';

    // Loop to generate the random string
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, strlen($characters) - 1)];
    }

    return $randomString;
}

}
      
      
	
      

     
