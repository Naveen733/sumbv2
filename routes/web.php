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
    
        Route::get('/expense-create', [App\Http\Controllers\InvoiceController::class, 'create_expense'])->name('expense-create');
        Route::put('/expense/{id}/update', [App\Http\Controllers\InvoiceController::class, 'update_expense'])->name('update-expense');
        Route::get('/expense/{id}/edit', [App\Http\Controllers\InvoiceController::class, 'edit_expense'])->name('edit-expense');
        Route::get('/expense/{id}/view', [App\Http\Controllers\InvoiceController::class, 'view_expense'])->name('view-expense');
        Route::get('/expense/{id}/delete', [App\Http\Controllers\InvoiceController::class, 'delete_expense'])->name('delete-expense');
        Route::post('/expense-save', [App\Http\Controllers\InvoiceController::class, 'save_expense'])->name('expense-create-save');


        Route::get('/invoice-create', [App\Http\Controllers\InvoiceController::class, 'create_invoice'])->name('invoice-create');
        Route::post('/invoice-create-save', [App\Http\Controllers\InvoiceController::class, 'create_invoice_new'])->name('invoice-create-save');
        
        Route::post('/invoice-particulars-add', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_add'])->name('invoice-particulars-add');
        Route::post('/invoice-particulars-delete', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_delete'])->name('invoice-particulars-delete');
        Route::get('/invoice-logo-upload', [App\Http\Controllers\InvoiceController::class, 'invoice_logo_upload'])->name('invoice-logo-upload');
        Route::post('/invoice-logo-process', [App\Http\Controllers\InvoiceController::class, 'invoice_logo_process'])->name('invoice-logo-process');
        
        Route::get('/invoice-void', [App\Http\Controllers\InvoiceController::class, 'invoice_void'])->name('invoice-void');
        Route::get('/expense-void', [App\Http\Controllers\InvoiceController::class, 'expense_void'])->name('expense-void');

        Route::get('/status-change/', [App\Http\Controllers\InvoiceController::class, 'status_change'])->name('status-change');
    
        Route::get('/invoice-particulars-add', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_add'])->name('invoice-particulars-add2');

        //File upload
        Route::prefix('documents')->group(function () {
            Route::get('/', [App\Http\Controllers\DocumentUploadController::class, 'index']);
            Route::post('/', [App\Http\Controllers\DocumentUploadController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\DocumentUploadController::class, 'docedit'])->name('doc-edit');
        });
        Route::get('/doc-upload', [App\Http\Controllers\DocumentUploadController::class, 'index'])->name('doc-upload');
        Route::post('/store', [App\Http\Controllers\DocumentUploadController::class, 'store'])->name('store');
        Route::get('/doc-edit', [App\Http\Controllers\DocumentUploadController::class, 'docedit'])->name('doc-edit');
        Route::patch('/doc-edit-process/{id}', [App\Http\Controllers\DocumentUploadController::class, 'doceditprocess'])->name('DocumentUploadController.doc-edit-process');
        Route::delete('/destroy', [App\Http\Controllers\DocumentUploadController::class, 'destroy'])->name('DocumentUploadController.destroy');
        // Route::get('/downloadfile/{id}', [App\Http\Controllers\DocumentUploadController::class, 'downloadfile'])->name('DocumentUploadController.downloadfile');
        Route::get('/downloadfile', [App\Http\Controllers\DocumentUploadController::class, 'downloadFile'])->name('DocumentUploadController.downloadfile');
    
        //testing
    Route::get('/testing', [App\Http\Controllers\InvoiceController::class, 'testing'])->name('testing');
    Route::get('/testpdf', [App\Http\Controllers\InvoiceController::class, 'testpdf'])->name('testpdf');
    Route::get('/testformat', [App\Http\Controllers\InvoiceController::class, 'testformat'])->name('testformat');
    
    Route::get('/logout', [App\Http\Controllers\MainController::class, 'logout'])->name('logout');
});
