<?php

use App\User;
use Illuminate\Support\Facades\Route;

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
    // for ($i = 1; $i <= 5; $i++) {
    //     User::create([
    //         'username' => "payment_$i",
    //         'password' => bcrypt("payment_$i"),
    //         'role' => 'user',
    //         'division_id' => 1
    //     ]);
    // }
    // for ($i = 1; $i <= 3; $i++) {
    //     User::create([
    //         'username' => "procurement_$i",
    //         'password' => bcrypt("procurement_$i"),
    //         'role' => 'user',
    //         'division_id' => 2
    //     ]);
    // }
    // for ($i = 1; $i <= 7; $i++) {
    //     User::create([
    //         'username' => "it_$i",
    //         'password' => bcrypt("it_$i"),
    //         'role' => 'user',
    //         'division_id' => 3
    //     ]);
    // }
    // for ($i = 1; $i <= 3; $i++) {
    //     User::create([
    //         'username' => "finance_$i",
    //         'password' => bcrypt("finance_$i"),
    //         'role' => 'user',
    //         'division_id' => 4
    //     ]);
    // }
    // for ($i = 1; $i <= 3; $i++) {
    //     User::create([
    //         'username' => "hr_$i",
    //         'password' => bcrypt("hr_$i"),
    //         'role' => 'admin',
    //         'division_id' => 5
    //     ]);
    // }
});
