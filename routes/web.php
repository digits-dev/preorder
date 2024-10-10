<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminItemsController;
use App\Http\Controllers\AdminStoresController;
use App\Http\Controllers\AdminCmsUsersController;
use App\Http\Controllers\AdminOrdersController;
use App\Http\Controllers\AdminCustomersController;
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
    return redirect('admin/login');
});

Route::group(['middleware' => ['web','\crocodicstudio\crudbooster\middlewares\CBBackend'],'prefix' => config('crudbooster.ADMIN_PATH')], function(){
    Route::group(['prefix' => 'users'], function () {
        Route::get('change-password',[AdminCmsUsersController::class,'showChangePasswordForm'])->name('show-change-password');
        Route::post('change-password',[AdminCmsUsersController::class,'changePassword'])->name('change-password');
        Route::post('waive-change-password',[AdminCmsUsersController::class,'waiveChangePassword'])->name('waive-change-password');
    });
});

Route::group(['middleware' => ['web','\crocodicstudio\crudbooster\middlewares\CBBackend'], 'prefix' => config('crudbooster.ADMIN_PATH'), 'namespace' => 'App\Http\Controllers'], function(){

    Route::group(['prefix' => 'items'], function () {
        Route::post('inventory-upload',[AdminItemsController::class, 'inventoryUpload'])->name('item-inventory.upload');
        Route::get('inventory-template',[AdminItemsController::class, 'inventoryTemplate'])->name('item-inventory.template');
        Route::get('inventory',[AdminItemsController::class, 'inventoryView'])->name('item-inventory.view');
        Route::post('upload',[AdminItemsController::class, 'itemUpload'])->name('item.upload');
        Route::get('template',[AdminItemsController::class, 'itemTemplate'])->name('item.template');
        Route::get('upload',[AdminItemsController::class, 'itemView'])->name('item.view');
        Route::post('get-models',[AdminItemsController::class, 'getItemModels'])->name('item.getItemModels');
        Route::post('get-colors',[AdminItemsController::class, 'getItemColors'])->name('item.getItemColors');
        Route::post('get-sizes',[AdminItemsController::class, 'getItemSizes'])->name('item.getItemSizes');
        Route::get('delete-all-item',[AdminItemsController::class, 'itemDelete'])->name('item.delete-all');
        Route::post('export',[AdminItemsController::class,'itemExport'])->name('item.export');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::post('users-upload',[AdminCmsUsersController::class, 'usersUpload'])->name('users.upload');
        Route::get('users-template',[AdminCmsUsersController::class, 'usersTemplate'])->name('users.template');
        Route::get('users',[AdminCmsUsersController::class, 'usersView'])->name('users.view');
    });

    Route::group(['prefix' => 'stores'], function () {
        Route::post('store-upload',[AdminStoresController::class, 'storeUpload'])->name('store.upload');
        Route::get('store-template',[AdminStoresController::class, 'storeTemplate'])->name('store.template');
        Route::get('import',[AdminStoresController::class, 'storeView'])->name('store.view');
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::post('preorder',[AdminOrdersController::class, 'preOrderSave'])->name('preorder.order');
        Route::post('preorder-edit',[AdminOrdersController::class, 'preOrderEditSave'])->name('preorder.order-edit');
        Route::get('preorder-cancel/{id}',[AdminOrdersController::class, 'preOrderCancel'])->name('preorder.cancel-order');
        Route::post('item-search',[AdminItemsController::class, 'itemSearch'])->name('preorder.item-search');
        Route::post('freebies-search',[AdminItemsController::class, 'freebiesSearch'])->name('preorder.freebies-search');
        Route::post('item-reservable',[AdminItemsController::class, 'itemReservable'])->name('preorder.item-reservable');
        Route::post('export',[AdminOrdersController::class,'preOrderExport'])->name('preorder.export');
        Route::post('get-customer-order-count',[AdminOrdersController::class,'getCustomerOrderCount'])->name('preorder.getCustomerOrders');
    });

    Route::post('customer/get-customer',[AdminCustomersController::class,'getCustomerDetails'])->name('preorder.getCustomer');
});









