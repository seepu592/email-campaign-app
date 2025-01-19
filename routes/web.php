<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CampaignController;
use App\Models\Campaign;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user(); 

    $campaigns = Campaign::where('user_id', $user->id)->get(); 

    return Inertia::render('Dashboard', [
        'user' => $user,
        'campaigns' => $campaigns, 
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/campaign/create', [CampaignController::class, 'create'])->name('campaign.create');

    Route::post('/campaign/store', [CampaignController::class, 'store'])->name('campaign.store');
    
    Route::get('/campaign-progress', [CampaignController::class, 'overview'])->name('campaign-progress');  // For overview page
    Route::get('/campaign/{campaign}/progress/update', [CampaignController::class, 'progressUpdate'])->name('campaign.progress.update');  // For progress update
    
});

require __DIR__.'/auth.php';
