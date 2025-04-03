<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{PublicApiController,GameApiController,AviatorApiController,AgencyPromotionController,SalaryApiController,VipController,ZiliApiController,TestJilliController,SpribeApiController,UserManualPaymentController};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



//// VIP Routes////
Route::get('/vip_level',[VipController::class,'vip_level']);
Route::get('/vip_level_history',[VipController::class,'vip_level_history']);
Route::post('/add_money',[VipController::class,'receive_money']);


Route::controller(PublicApiController::class)->group(function () {
    Route::get('/country','country');
    //// uses only web ///
    Route::post('/otp-register',[PublicApiController::class,'otp_register']);
     //// uses only web ///
	Route::get('/image_all','image_all');
    Route::post('/register', 'registers');
    Route::post('/check_number', 'check_existsnumber');
    Route::post('/login', 'login');
    Route::get('/profile/{id}', 'Profile');
	Route::post('/update_profile','update_profile');
    Route::get('/slider','slider_image_view');
    Route::post('/changepassword','changepassword');
    Route::post('/forget_Password','resetPassword');
    Route::post('/addAccount','addAccount');
    Route::get('/accountView','accountView');
    Route::post('/payin','payin');
    Route::get('/checkPayment','checkPayment');
    Route::get('/payin-successfully','redirect_success')->name('payin.successfully');
    
    Route::post('/withdraw','withdraw');
    Route::get('/withdraw-history','withdrawHistory');
    
    Route::get('/deposit-history','deposit_history');
    Route::get('/account-delete/{id}','accountDelete');
    Route::post('/gift_cart_apply','giftCartApply');
    Route::get('/gift_redeem_list','claim_list');
    Route::get('/customer_service','customer_service');
	Route::post('/wallet_transfers','wallet_transfer');
	Route::post('/main_wallet_transfers','main_wallet_transfer');
	Route::post('/winning_wallet_transfers','winning_wallet_transfers');
	Route::get('/version_apk_link','versionApkLink');
	Route::get('/salary_list','salary_list');
	Route::get('/betting_rebate','betting_rebate');
    Route::get('/betting_rebate_history','betting_rebate_history');
	 Route::post('/invitation_bonus_claim','invitation_bonus_claim');
	
	Route::get('/updateMissingSpribeAndJilliData','updateSpribeIdForUsers');
	Route::get('/commission_details','commission_details');
	
	Route::post('/new_register_store', 'new_register_store');
	
	
	
	
});


Route::controller(AgencyPromotionController::class)->group(function () {
    Route::get('/agency-promotion-data-{id}', 'promotion_data');
	Route::get('/new-subordinate', 'new_subordinate');
	Route::get('/tier', 'tier');
	Route::post('/subordinate-data','subordinate_data');
	Route::get('/turnovers','turnover_new');
	//Route::get('/turnover','turnover');
});
Route::controller(GameApiController::class)->group(function () {
     Route::post('/bets', 'bet');
      Route::post('/dragon_bet', 'dragon_bet');
     Route::get('/win_amount', 'win_amount');
     Route::get('/results','results');
     Route::get('/last_five_result','lastFiveResults');
     Route::get('/last_result','lastResults');
     Route::post('/bet_history','bet_history');
     Route::get('/cron/{game_id}/','cron');
     /// mine game route //
    Route::post('/mine_bet','mine_bet');
    Route::post('/mine_cashout','mine_cashout');
    Route::get('/mine_result','mine_result');
    Route::get('/mine_multiplier','mine_multiplier');
    
    //// Plinko Game Route /////
    
    Route::post('/plinko_bet','plinkoBet');
    Route::get('/plinko_index_list','plinko_index_list');
    Route::get('/plinko_result','plinko_result');
    Route::get('/plinko_cron','plinko_cron');
    Route::post('/plinko_multiplier','plinko_multiplier'); 
});

// Route::controller(AviatorApiController::class)->group(function () {
// Route::post('/aviator_bet','aviatorBet');
// Route::post('/aviator_cashout','aviator_cashout');
// Route::post('/aviator_history','aviator_history');
// Route::get('/aviator_last_result','aviator_last_result');
// Route::post('/aviator_bet_cancel','bet_cancel');
// Route::get('/result_half_new','result_half_new');
// Route::post('/result_insert_new','result_insert_new');
// });
Route::post('/aviator_bet',[AviatorApiController::class, 'aviator_bet']);
Route::get('/aviator_bet_new',[AviatorApiController::class, 'aviator_bet_new']);
Route::post('/aviator_cashout',[AviatorApiController::class, 'aviator_cashout']);
Route::post('/aviator_history',[AviatorApiController::class, 'aviator_history']);
Route::get('/aviator_last_result',[AviatorApiController::class, 'last_result']);
Route::get('/aviator_last_five_result',[AviatorApiController::class, 'last_five_result']);
 Route::get('/aviator_bet_cancel',[AviatorApiController::class, 'bet_cancel']);


Route::controller(SalaryApiController::class)->group(function () {
    Route::get('/aviator_salary', 'aviator_salary');
    Route::get('/daily_bonus','dailyBonus');
	Route::get('/monthly_bonus','monthlyBonus');

	//Route::get('/turnover','turnover');
});

   ///   akash /////

Route::post('/usdt_payin',[PublicApiController::class,'payin_usdt']);
Route::post('/payin_call_back',[PublicApiController::class,'payin_call_back']);

  

/// test Jilli Controller ////

Route::get('/end_user_register',[TestJilliController::class,'end_user_register']);
Route::get('/get_all_game_list',[TestJilliController::class,'get_all_game_list']);
Route::get('/get_game_url_gameid',[TestJilliController::class,'get_game_url_gameid']);
Route::get('/add_amount_to_user',[TestJilliController::class,'transfer_amount_to_user']);
Route::get('/get_jilli_transaction_details',[TestJilliController::class,'get_jilli_transaction_details']);
Route::get('/wallet_deduct_from_user',[TestJilliController::class,'wallet_deduct_from_user']);
Route::get('/get_bet_history',[TestJilliController::class,'get_bet_history']);
Route::get('/get_reseller_info',[TestJilliController::class,'get_reseller_info']);


  //// Zili Api ///
Route::post('/user_register',[ZiliApiController::class,'user_register']);  //not in use for registration
Route::post('/all_game_list',[ZiliApiController::class,'all_game_list']);
Route::post('/all_game_list_test',[ZiliApiController::class,'all_game_list_test']);
Route::post('/get_game_url',[ZiliApiController::class,'get_game_url']);
Route::post('/get_jilli_transactons_details',[ZiliApiController::class,'get_jilli_transactons_details']);
Route::post('/jilli_deduct_from_wallet',[ZiliApiController::class,'jilli_deduct_from_wallet']);
Route::post('/jilli_get_bet_history',[ZiliApiController::class,'jilli_get_bet_history']);
Route::post('/add_in_jilli_wallet ',[ZiliApiController::class,'add_in_jilli_wallet']);
Route::post('/update_main_wallet ',[ZiliApiController::class,'update_main_wallet']);
Route::post('/get_jilli_wallet ',[ZiliApiController::class,'get_jilli_wallet']);
Route::post('/update_jilli_wallet ',[ZiliApiController::class,'update_jilli_wallet']);
Route::post('/update_jilli_to_user_wallet ',[ZiliApiController::class,'update_jilli_to_user_wallet']);


Route::get('/test_get_user_info ',[ZiliApiController::class,'test_get_user_info']);
Route::get('/get-reseller-info/{manager_key?}',[ZiliApiController::class,'get_reseller_info']);


Route::controller(SpribeApiController::class)->group(function () {
    Route::get('/get_reseller_info', 'get_reseller_info');
    Route::post('/get_spribe_game_urls','get_spribe_game_urls');
	Route::post('/spribe_betting_history','spribe_betting_history');
	Route::post('/spribe_all_betting_history','spribe_all_betting_history');
	Route::post('/sprb/spribe/callback','handleCallback');
	Route::post('/spribe_user_register','spribe_user_register'); 
	Route::post('/spribe_transactons_details','spribe_transactons_details'); 
	Route::post('/scribe_deduct_from_wallet','scribe_deduct_from_wallet');
	Route::post('/get_spribe_wallet ','get_spribe_wallet');
	Route::post('/add_in_spribe_wallet ','add_in_spribe_wallet');
	Route::post('/update_spribe_wallet ','update_spribe_wallet');
	Route::post('/update_spribe_to_user_wallet ','update_spribe_to_user_wallet');

	
	//Route::get('/monthly_bonus','monthlyBonus');
});


   Route::get('/bankdetailes/{bank_id}', [UserManualPaymentController::class,'bankdetails']);
   Route::post('/upload_screenshot', [UserManualPaymentController::class, 'uploadScreenshot']);
   Route::post('/addaccountdetails ', [UserManualPaymentController::class, 'addaccountdetails']);
   Route::get('/getaccountdetails/{id}', [UserManualPaymentController::class, 'getaccountdetails']);
   Route::post('/withdrawmanual', [UserManualPaymentController::class, 'withdrawmanual']);
   Route::get('/businesssettings', [UserManualPaymentController::class, 'businesssettings']);
   Route::post('/withdrawalhistory', [UserManualPaymentController::class, 'withdrawalhistory']);
   Route::post('/deposite', [UserManualPaymentController::class, 'deposite']);
    Route::post('/walletHistories', [UserManualPaymentController::class, 'walletHistories']);
     Route::get('/type', [UserManualPaymentController::class, 'type']);

