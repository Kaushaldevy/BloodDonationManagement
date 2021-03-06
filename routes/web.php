<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return view('home', ['title' => 'Home', 'scripts' => []]);
});

// Show the requested register page
Route::get('register/pages/{type}', function ($type){
	switch ($type)
	{
		case 'doner':
			return view('authenticate.doner.doner_register', ['title' => 'Register Doner', 'scripts'=> []]);
			break;

		case 'hospital':
			return view('authenticate.hospital.hospital_register', ['title' => "Register your hospital", 'scripts' => []]);
			break;

		case 'blood_bank':
			return view('authenticate.blood_bank.blood_bank_register', ['title' => "Register your blood bank", 'scripts' => []]);
			break;

		default:
			return redirect('/')->withErrors('Error 404');
	}
})->name('register/pages');

Route::get('auth/login', function (){
	return view('authenticate.auth.login', ['title' => "Login", 'scripts' => []]);
})->name('auth/login');

// Register blood bank
Route::post('auth/register/bloodbank', function (\Illuminate\Http\Request $request){
	$validator = Validator::make($request->all(), [
		'id' => 'unique:blood_banks'
	]);

	if ($validator->fails()){
		return redirect('register/pages/blood_bank')->withInput()->withErrors($validator);
	}

	$bloodBank = new \App\BloodBank;
	$bloodBank->id = $request->id;
	$bloodBank->name = $request->name;
	$bloodBank->telephone = $request->telephone;
	$bloodBank->password = $request->password;

	// Add the address
	$address = new \App\BloodBankAddress;
	$address->house_no = $request->house_no;
	$address->street = $request->street;
	$address->province =$request->province;
	$address->town = $request->town;

	$address->bloodbank()->associate($bloodBank);
	$bloodBank->save();
	$address->save();

	return redirect('auth/login');
});

Route::post('auth/register/doner', function (\Illuminate\Http\Request $request){
	$validator = Validator::make($request->all(), [
		'id' => 'unique:persons'
	]);


	if ($validator->fails()){
		return redirect('register/pages/doner')->withInput()->withErrors($validator);
	}

	// Add person first
	$person = new \App\Person;
	$person->id = $request->id;
	$person->weight = $request->weight;
	$person->height = $request->height;
	$person->gender = $request->gender;
	$person->blood_group = $request->blood_group;
	$person->blood_type = $request->blood_type;
	$person->first_name = $request->first_name;
	$person->last_name = $request->last_name;
	$person->telephone = $request->telephone;
	$person->password = $request->password;

	// Add him as a doner
	$doner = new \App\Doner();
	$doner->medical = $request->medical;

	// save all
	$doner->doner()->associate($person);
	$person->save();
	$doner->save();

	return redirect('auth/login');
});