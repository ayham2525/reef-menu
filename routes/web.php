<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportsController;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

// Public controllers
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReefMenuController;

// Profile
use App\Http\Controllers\ProfileController;

// Admin controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\WarehouseTransferController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\BrokerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\InventoryStockController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\UserManagementController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::get('/', function () {
    return view('welcome');
});

Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

Route::get('/reef-menu', ReefMenuController::class)->name('reef.menu');


/*
|--------------------------------------------------------------------------
| Authenticated Backend Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware('auth')->group(function () {
        Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard + Modules
    |--------------------------------------------------------------------------
    */

    Route::middleware('verified')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Master Data
            |--------------------------------------------------------------------------
            */
            Route::resource('vendors', VendorController::class);
            Route::resource('warehouses', WarehouseController::class);
            Route::resource('sections', SectionController::class);
            Route::resource('positions', PositionController::class);
            Route::resource('employees', EmployeeController::class);
            Route::resource('menu-categories', MenuCategoryController::class);
            Route::resource('menu-items', MenuItemController::class);
            Route::resource('agencies', AgencyController::class);
            Route::resource('brokers', BrokerController::class);

            Route::get('/change-password', function () {
                return redirect()->to(route('profile.edit') . '#update-password');
            })->name('password.change');


            /*
            |--------------------------------------------------------------------------
            | Kitchen Orders
            |--------------------------------------------------------------------------
            */
            Route::get(
                'orders/items-by-category/{category}',
                [AdminOrderController::class, 'getItemsByCategory']
            );

            Route::get(
                'orders/item-options/{item}',
                [AdminOrderController::class, 'getItemOptions']
            );

            Route::post(
                'orders/{id}/status',
                [AdminOrderController::class, 'updateStatus']
            )->name('admin.orders.status');

            Route::post(
                'orders/bulk-status',
                [AdminOrderController::class, 'bulkStatus']
            )->name('admin.orders.bulk-status');

            Route::resource('orders', AdminOrderController::class)
                ->only(['index', 'show', 'create', 'store']);


            /*
            |--------------------------------------------------------------------------
            | Purchase Orders
            |--------------------------------------------------------------------------
            */
            Route::post(
                'purchase-orders/{purchase_order}/receive',
                [PurchaseOrderController::class, 'receive']
            )->name('purchase-orders.receive');

            Route::resource('purchase-orders', PurchaseOrderController::class);



            /*
            |--------------------------------------------------------------------------
            | Inventory Module
            |--------------------------------------------------------------------------
            */
            Route::prefix('inventory')->name('inventory.')->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Warehouse Transfers (MUST BE FIRST)
                |--------------------------------------------------------------------------
                */
                Route::resource('transfers', WarehouseTransferController::class);

                Route::post(
                    'transfers/{transfer}/approve',
                    [WarehouseTransferController::class, 'approve']
                )->name('transfers.approve');

                Route::get(
                    'transfers/{transfer}/pdf',
                    [WarehouseTransferController::class, 'pdf']
                )->name('transfers.pdf');


                /*
                |--------------------------------------------------------------------------
                | Stock Levels (MUST BE LAST – OTHERWISE THEY SHADOW TRANSFERS)
                |--------------------------------------------------------------------------
                */
                Route::get('/', [InventoryStockController::class, 'index'])->name('index');

                Route::get('stock/{stock}', [InventoryStockController::class, 'show'])->name('show');
                Route::get('stock/{stock}/restock', [InventoryStockController::class, 'restockForm'])->name('restock.form');
                Route::post('stock/{stock}/restock', [InventoryStockController::class, 'restock'])->name('restock');
                Route::get('stock/{stock}/adjust', [InventoryStockController::class, 'adjustForm'])->name('adjust.form');
                Route::post('stock/{stock}/adjust', [InventoryStockController::class, 'adjust'])->name('adjust');

                /*
                |--------------------------------------------------------------------------
                | Recipes
                |--------------------------------------------------------------------------
                */
                Route::get('menu-items/{item}/recipe', [MenuItemController::class, 'recipe'])->name('menu-items.recipe');
                Route::post('menu-items/{item}/recipe', [MenuItemController::class, 'recipeStore']);
                Route::delete('menu-items/{item}/recipe/{recipe}', [MenuItemController::class, 'recipeDelete']);
            });
            Route::prefix('reports')->name('reports.')->group(function () {

                // Stock Levels
                Route::get('stock-level', [ReportsController::class, 'stockLevel'])
                    ->name('stock-level');

                // Stock Movements
                Route::get('stock-movement', [ReportsController::class, 'stockMovement'])
                    ->name('stock-movement');

                // Transfers
                Route::get('stock-transfers', [ReportsController::class, 'stockTransfers'])
                    ->name('stock-transfers');
            });
            Route::middleware('admin')->group(function () {
                Route::resource('users', UserManagementController::class);
            });
        });
});


/*
|--------------------------------------------------------------------------
| Laravel Breeze Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
