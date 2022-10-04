<?php

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

//main page
Route::get('/', [App\Http\Controllers\MainController::class, 'index'])->name('index');
Route::get('/signup', [App\Http\Controllers\MainController::class, 'signup'])->name('signup');

//form post section
Route::post('/login', [App\Http\Controllers\MainController::class, 'login'])->name('login');
Route::post('/register', [App\Http\Controllers\MainController::class, 'register'])->name('register');
Route::get('/verification', [App\Http\Controllers\MainController::class, 'verify'])->name('verify');

//Route::middleware(['SumbAuth'])->group(function () {
//    Route::get('/dashboard', [App\Http\Controllers\ModuleDashboard::class, 'index'])->name('Dashboard');
//});

Route::middleware('sumbauth')->group(function() {
    //Dashboard
    Route::get('/dashboard', [App\Http\Controllers\ModuleDashboard::class, 'index'])->name('dashboard');
    
    //Invoice and Expenses
    Route::get('/invoice', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoice');
    
        Route::get('/expenses-create', [App\Http\Controllers\InvoiceController::class, 'create_expenses'])->name('expenses-create');
        Route::post('/expenses-create-save', [App\Http\Controllers\InvoiceController::class, 'create_expenses_new'])->name('expenses-create-save');
        //Route::get('/expenses-void', [App\Http\Controllers\InvoiceController::class, 'expenses_void'])->name('expenses-void');
    
        Route::get('/invoice-create', [App\Http\Controllers\InvoiceController::class, 'create_invoice'])->name('invoice-create');
        Route::post('/invoice-create-save', [App\Http\Controllers\InvoiceController::class, 'create_invoice_new'])->name('invoice-create-save');
        
        Route::post('/invoice-particulars-add', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_add'])->name('invoice-particulars-add');
        Route::post('/invoice-particulars-delete', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_delete'])->name('invoice-particulars-delete');
        Route::get('/invoice-logo-upload', [App\Http\Controllers\InvoiceController::class, 'invoice_logo_upload'])->name('invoice-logo-upload');
        Route::post('/invoice-logo-process', [App\Http\Controllers\InvoiceController::class, 'invoice_logo_process'])->name('invoice-logo-process');
        
        //Route::get('/invoice-void', [App\Http\Controllers\InvoiceController::class, 'invoice_void'])->name('invoice-void');
        
        Route::get('/status-change', [App\Http\Controllers\InvoiceController::class, 'status_change'])->name('status-change');
    
        Route::get('/invoice-particulars-add', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_add'])->name('invoice-particulars-add2');
    
    //testing
    Route::get('/testing', [App\Http\Controllers\InvoiceController::class, 'testing'])->name('testing');
    Route::get('/testpdf', [App\Http\Controllers\InvoiceController::class, 'testpdf'])->name('testpdf');
    Route::get('/testformat', [App\Http\Controllers\InvoiceController::class, 'testformat'])->name('testformat');
    
    Route::get('/logout', [App\Http\Controllers\MainController::class, 'logout'])->name('logout');
});