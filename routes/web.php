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
    Route::get('/expense', [App\Http\Controllers\ExpenseController::class, 'index'])->name('expense');
    
        Route::get('/expense-create', [App\Http\Controllers\ExpenseController::class, 'create_expense'])->name('expense-create');
        Route::put('/expense/{id}/update', [App\Http\Controllers\ExpenseController::class, 'update_expense'])->name('update-expense');
        Route::get('/expense/{id}/edit', [App\Http\Controllers\ExpenseController::class, 'edit_expense'])->name('edit-expense');
        Route::get('/expense/{id}/view', [App\Http\Controllers\ExpenseController::class, 'view_expense'])->name('view-expense');
        Route::get('/expense/{id}/delete', [App\Http\Controllers\ExpenseController::class, 'delete_expense'])->name('delete-expense');
        Route::post('/expense-save', [App\Http\Controllers\ExpenseController::class, 'save_expense'])->name('expense-create-save');
        Route::get('/expense-void', [App\Http\Controllers\ExpenseController::class, 'expense_void'])->name('expense-void');
        Route::get('/expense-status-change/', [App\Http\Controllers\ExpenseController::class, 'status_change'])->name('expense-status-change');

        Route::get('/invoice-create', [App\Http\Controllers\InvoiceController::class, 'create_invoice'])->name('invoice-create');
        Route::get('/expenses-create', [App\Http\Controllers\InvoiceController::class, 'create_expenses'])->name('expenses-create');
        Route::post('/expenses-create-save', [App\Http\Controllers\InvoiceController::class, 'create_expenses_new'])->name('expenses-create-save');
        //Route::get('/expenses-void', [App\Http\Controllers\InvoiceController::class, 'expenses_void'])->name('expenses-void');
    
        Route::get('/invoice/create', [App\Http\Controllers\InvoiceController::class, 'store'])->name('invoice/create');
        Route::post('/invoice-create-save', [App\Http\Controllers\InvoiceController::class, 'create_invoice_new'])->name('invoice-create-save');
        
        Route::post('/invoice-particulars-add', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_add'])->name('invoice-particulars-add');
        Route::post('/invoice-particulars-delete', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_delete'])->name('invoice-particulars-delete');
        Route::get('/invoice-logo-upload', [App\Http\Controllers\InvoiceController::class, 'invoice_logo_upload'])->name('invoice-logo-upload');
        Route::post('/invoice-logo-process', [App\Http\Controllers\InvoiceController::class, 'invoice_logo_process'])->name('invoice-logo-process');
        
       // Route::get('/invoice-void', [App\Http\Controllers\InvoiceController::class, 'invoice_void'])->name('invoice-void');
        
    
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
        Route::get('/downloadfile', [App\Http\Controllers\DocumentUploadController::class, 'downloadFile'])->name('DocumentUploadController.downloadfile');
        Route::get('/docview', [App\Http\Controllers\DocumentUploadController::class, 'docview'])->name('docview');
        
        
        //testing
    Route::get('/testing', [App\Http\Controllers\InvoiceController::class, 'testing'])->name('testing');
    Route::get('/testpdf', [App\Http\Controllers\InvoiceController::class, 'testpdf'])->name('testpdf');
    Route::get('/testformat', [App\Http\Controllers\InvoiceController::class, 'testformat'])->name('testformat');
    
    Route::get('/logout', [App\Http\Controllers\MainController::class, 'logout'])->name('logout');

    Route::post('/search-client', [App\Http\Controllers\InvoiceController::class, 'searchClient'])->name('search-client');
    Route::post('/search-invoice-item', [App\Http\Controllers\InvoiceController::class, 'searchInvoiceItem'])->name('search-invoice-item');
    Route::post('/add-invoice-item', [App\Http\Controllers\InvoiceController::class, 'InvoiceItemForm'])->name('add-invoice-item');
    Route::post('/invoice-items', [App\Http\Controllers\InvoiceController::class, 'InvoiceItemFormList'])->name('invoice-items');
    Route::get('/invoice-items/{id}', [App\Http\Controllers\InvoiceController::class, 'InvoiceItemFormListById'])->name('invoice-items/{id}');
    Route::get('/invoice/{id}/edit', [App\Http\Controllers\InvoiceController::class, 'update'])->name('/invoice/{id}/edit');
    Route::post('/invoice/send-email', [App\Http\Controllers\InvoiceController::class, 'sendInvoice'])->name('/invoice/send-email');
    Route::get('/status-change', [App\Http\Controllers\InvoiceController::class, 'statusUpdate'])->name('status-change');
    Route::get('/invoice/search', [App\Http\Controllers\InvoiceController::class, 'invoiceSearch'])->name('/invoice/search');
    Route::get('/invoice/settings', [App\Http\Controllers\InvoiceSettingsController::class, 'invoiceSettingsForm'])->name('/invoice/settings');
    Route::post('/invoice/settings/add', [App\Http\Controllers\InvoiceSettingsController::class, 'store'])->name('/invoice/settings/add');
    Route::post('/invoice-logo-upload', [App\Http\Controllers\InvoiceSettingsController::class, 'logoUpload'])->name('/invoice-logo-upload');
    Route::post('/invoice/settings/edit', [App\Http\Controllers\InvoiceSettingsController::class, 'update'])->name('/invoice/settings/edit');
    Route::get('/invoice/{id}/delete', [App\Http\Controllers\InvoiceController::class, 'delete'])->name('/invoice/{id}/delete');
    Route::post('/add-invoice-chart-account', [App\Http\Controllers\ChartAccountController::class, 'InvoiceChartAccountForm'])->name('add-invoice-chart-account');
    Route::get('/chart-accounts-parts/{id}', [App\Http\Controllers\ChartAccountController::class, 'chartAccountsPartsById'])->name('chart-accounts-parts/{id}');
    Route::get('/chart-accounts-parts', [App\Http\Controllers\ChartAccountController::class, 'chartAccountsPartsList'])->name('chart-accounts-parts');
    Route::get('/invoice-tax-rates', [App\Http\Controllers\InvoiceController::class, 'invoiceTaxRates'])->name('invoice-tax-rates');
    Route::get('/chart-accounts', [App\Http\Controllers\ChartAccountController::class, 'index'])->name('chart-accounts');
    Route::post('/edit-chart-account/{id}', [App\Http\Controllers\ChartAccountController::class, 'update'])->name('edit-chart-account/{id}');
    Route::get('/profit-loss', [App\Http\Controllers\ProfitAndLossController::class, 'index'])->name('profit-loss');
});
