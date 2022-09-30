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
    // return view('welcome');
});

Route::post('/admin/items/inventory-upload',[AdminItemsController::class, 'inventoryUpload'])->name('item-inventory.upload');
Route::get('/admin/items/inventory-template',[AdminItemsController::class, 'inventoryTemplate'])->name('item-inventory.template');
Route::get('/admin/items/inventory',[AdminItemsController::class, 'inventoryView'])->name('item-inventory.view');

Route::post('/admin/items/upload',[AdminItemsController::class, 'itemUpload'])->name('item.upload');
Route::get('/admin/items/template',[AdminItemsController::class, 'itemTemplate'])->name('item.template');
Route::get('/admin/items/upload',[AdminItemsController::class, 'itemView'])->name('item.view');

Route::post('/admin/items/get-models',[AdminItemsController::class, 'getItemModels'])->name('item.getItemModels');
Route::post('/admin/items/get-colors',[AdminItemsController::class, 'getItemColors'])->name('item.getItemColors');
Route::post('/admin/items/get-sizes',[AdminItemsController::class, 'getItemSizes'])->name('item.getItemSizes');

Route::get('/admin/items/delete-all-item',[AdminItemsController::class, 'itemDelete'])->name('item.delete-all');
Route::post('/admin/items/export',[AdminItemsController::class,'itemExport'])->name('item.export');

Route::post('/admin/users/users-upload',[AdminCmsUsersController::class, 'usersUpload'])->name('users.upload');
Route::get('/admin/users/users-template',[AdminCmsUsersController::class, 'usersTemplate'])->name('users.template');
Route::get('/admin/users/users',[AdminCmsUsersController::class, 'usersView'])->name('users.view');

Route::post('/admin/stores/store-upload',[AdminStoresController::class, 'storeUpload'])->name('store.upload');
Route::get('/admin/stores/store-template',[AdminStoresController::class, 'storeTemplate'])->name('store.template');
Route::get('/admin/stores/import',[AdminStoresController::class, 'storeView'])->name('store.view');

Route::post('/admin/orders/preorder',[AdminOrdersController::class, 'preOrderSave'])->name('preorder.order');
Route::post('/admin/orders/preorder-edit',[AdminOrdersController::class, 'preOrderEditSave'])->name('preorder.order-edit');
Route::get('/admin/orders/preorder-cancel/{id}',[AdminOrdersController::class, 'preOrderCancel'])->name('preorder.cancel-order');

Route::post('/admin/orders/item-search',[AdminItemsController::class, 'itemSearch'])->name('preorder.item-search');
Route::post('/admin/orders/freebies-search',[AdminItemsController::class, 'freebiesSearch'])->name('preorder.freebies-search');
Route::post('/admin/orders/item-reservable',[AdminItemsController::class, 'itemReservable'])->name('preorder.item-reservable');

Route::post('/admin/orders/export',[AdminOrdersController::class,'preOrderExport'])->name('preorder.export');
Route::post('/admin/customer/get-customer',[AdminCustomersController::class,'getCustomerDetails'])->name('preorder.getCustomer');
