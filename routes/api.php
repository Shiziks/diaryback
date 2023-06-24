<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaylogController;
use App\Http\Controllers\EnergyController;
use App\Http\Controllers\GratitudeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\MoodUserController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserEnergyController;
use App\Http\Controllers\UserSleepController;
use App\Http\Controllers\AffirmationController;
use App\Http\Controllers\AppImageController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ProfileCategoryController;
use App\Http\Controllers\ProfileSubcategoryController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SleepHoursController;
use App\Http\Controllers\TestemonialController;
use App\Http\Controllers\UserPhotosController;
use App\Http\Controllers\UserProfileSettingsController;
use App\Http\Controllers\UserstepController;
use App\Http\Controllers\UserWaterController;
use App\Http\Controllers\UserWorkoutController;
use App\Http\Controllers\WaterglassController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('profile', 'profile');
    Route::get('userdata', 'userData');
    Route::get('check', 'authenticated');
    Route::post('edituserdata', 'edituserdata');
    Route::post('changepassword', 'changepassword');
    Route::get('getuser/{email}', 'getuser');
    Route::post('makeadmin', 'makeadmin');
    Route::post('removeadmin', 'removeadmin');
    Route::get('getallroles', 'getallroles');

    //Route::get('checkpassword/{id}', 'checkpassword');

});


//EMAIL VERIFY
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::get('/email/resend/{email}', [EmailVerificationController::class, 'resend'])->name('verification.resend');

//RESET PASSWORD
Route::post('/sendemail', [ResetPasswordController::class, 'sendemail']);
Route::post('/resetpassword', [ResetPasswordController::class, 'resetpassword']);



//PROFILE CATEGORIES
Route::get('/profilecategories', [ProfileCategoryController::class, 'getprofilecategories']);
Route::post('/changeadminstatus', [ProfileCategoryController::class, 'changeadminstatus']);

///USER PROFILE SETTINGS
Route::get('userprofilesettings/{id}', [UserProfileSettingsController::class, 'getuserprofilesettings'])->middleware('auth');
Route::post('edituserprofilesettings', [UserProfileSettingsController::class, 'edituserprofilesettings']);

/////WELCOME IMAGE
Route::post('addimages', [AppImageController::class, 'addimages']);
Route::get('getallimages', [AppImageController::class, 'getallimages']);
Route::post('deleteappimage', [AppImageController::class, 'deleteappimage']);

///QUOTE
Route::get('/quote', [QuoteController::class, 'getQuote']);
Route::get('/allquotes', [QuoteController::class, 'allQuotes']);
Route::post('deletequote', [QuoteController::class, 'deletequote']);
Route::post('addquote', [QuoteController::class, 'addquote']);
Route::post('editquote', [QuoteController::class, 'editquote']);


///MOODS
Route::get('/moods', [MoodController::class, 'getMoods']);
Route::post('editmood', [MoodController::class, 'editmood']);
Route::get('/allusermoods/{user_id}', [MoodUserController::class, 'getAllUserMoods']);
Route::post('/usermood', [MoodUserController::class, 'setUserMood']);
Route::post('/usermoodupdate', [MoodUserController::class, 'UpdateUserMood']);
Route::get('currentmood/{id}', [MoodUserController::class, 'currentMood']);

///ENERGY
Route::get('/energynames', [EnergyController::class, 'getEnergyLevels']);
Route::post('/editenergy', [EnergyController::class, 'editenergy']);
Route::get('/alluserenergy/{id}', [UserEnergyController::class, 'getUserEnergy']);
Route::post('/setuserenergy', [UserEnergyController::class, 'setUserEnergy']);
Route::get('/currentuserenergy/{id}', [UserEnergyController::class, 'getCurrentUserEnergy']);
Route::post('/updateuserenergy', [UserEnergyController::class, 'updateUserEnergy']);


///SLEEP
Route::get('/getallusersleepinghours/{id}', [UserSleepController::class, 'getallusersleepinghours']);
Route::get('/getcurrentsleepingtime/{id}', [UserSleepController::class, 'getUserCurrentSleepingHours']);
Route::post('/setusersleep', [UserSleepController::class, 'setUserSleepingHours']);
Route::post('/updatesleephours', [UserSleepController::class, 'updateSleepHours']);
Route::get('getsleephours', [SleepHoursController::class, 'getsleephours']);
Route::post('editsleephour', [SleepHoursController::class, 'editsleephour']);

///GRATITUDE
Route::get('allusergratitudes/{id}', [GratitudeController::class, 'allusergratitudes']);
Route::post('setgratitudes', [GratitudeController::class, 'setusergratitudes']);
Route::post('updategratitudes', [GratitudeController::class, 'updategratitudes']);
Route::get('gratitudesbygroup/{id}', [GratitudeController::class, 'getgratitudesbygroup']);
Route::post('currentusergratitudes', [GratitudeController::class, 'getcurrentusergratitudes']);
Route::get('getgratitudesbygroup/{id}', [GratitudeController::class, 'getgratitudesbygroup']);
Route::post('deletegratitudes', [GratitudeController::class, 'deletegratitudes']);

///IMAGES
Route::post('daylogimage', [ImageController::class, 'postimage']);
Route::get('getallpostimages/{id}', [ImageController::class, 'getallpostimages']);
Route::post('deleteimage', [ImageController::class, 'deleteimage']);

///DAYLOGS
Route::post('postdaylog', [DaylogController::class, 'postdaylog']);
Route::get('getalluserdaylogs/{id}', [DaylogController::class, 'getalluserdaylogs']);
Route::post('updatedaylog', [DaylogController::class, 'updatedaylog']);
Route::get('getdaylog/{id}', [DaylogController::class, 'getdaylog']);
Route::post('deletedaylog', [DaylogController::class, 'deletedaylog']);


///AFFIRMATIONS
Route::get('getallaffirmations', [AffirmationController::class, 'getallaffirmations']);
Route::post('editaffirmation', [AffirmationController::class, 'editaffirmation']);
Route::post('addaffirmation', [AffirmationController::class, 'addaffirmation']);
Route::post('deleteaffirmation', [AffirmationController::class, 'deleteaffirmation']);


/////WATER
Route::get('getallwaterlogs/{id}', [UserWaterController::class, 'getallwaterlogs']);
Route::post('logwaterintake', [UserWaterController::class, 'logwaterintake']);
Route::post('updatewaterintake', [UserWaterController::class, 'updatewaterintake']);
Route::get('getallwaterglasses', [WaterglassController::class, 'getallwaterglasses']);
Route::post('editwaterglasses', [WaterglassController::class, 'editwaterglasses']);



/////STEPS
Route::post('saveusersteps', [UserstepController::class, 'saveusersteps']);
Route::post('updateusersteps', [UserstepController::class, 'updateusersteps']);
Route::get('allusersteps/{id}', [UserstepController::class, 'allusersteps']);

/////WORKOUT
Route::get('getalluserworkouts/{id}', [UserWorkoutController::class, 'getalluserworkouts']);
Route::post('loguserworkout', [UserWorkoutController::class, 'loguserworkout']);
Route::post('deleteworkout', [UserWorkoutController::class, 'deleteworkout']);

///USERPHOTOS
Route::post('savephoto', [UserPhotosController::class, 'savephoto']);
Route::get('getuserphotos/{id}', [UserPhotosController::class, 'getuserphotos']);
Route::post('deletephoto', [UserPhotosController::class, 'deletephoto']);
Route::post('editprofilephoto', [UserPhotosController::class, 'editprofilephoto']);


///PROFILE SUBCATEGORIES
Route::get('getallsubcategories', [ProfileSubcategoryController::class, 'getallsubcategories']);
Route::get('getcategorysubcategories/{id}', [ProfileSubcategoryController::class, 'getcategorysubcategories']);
Route::post('addNewSubcategory', [ProfileSubcategoryController::class, 'addNewSubcategory']);
Route::post('deletesubcategory', [ProfileSubcategoryController::class, 'deletesubcategory']);
Route::post('changesubcategorystatus', [ProfileSubcategoryController::class, 'changesubcategorystatus']);

///TESTEMONIALS
Route::get('gettestemonial/{id}', [TestemonialController::class, 'gettestemonial']);
Route::post('cratetestemonial', [TestemonialController::class, 'cratetestemonial']);
Route::get('getalltestemonials', [TestemonialController::class, 'getalltestemonials']);
Route::post('deletetestemonial', [TestemonialController::class, 'deletetestemonial']);
Route::post('edittestemonial', [TestemonialController::class, 'edittestemonial']);





// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
