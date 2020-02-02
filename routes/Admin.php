<?php
/*
 * Route Added by Abu Bakar @10-14-2019
 */
/* ================ Menu Manager Admin Module ================= */
Route::get('menu-manager/list/{id?}', 'SystemSettings\MenuManagerController@list')->name('menu-list');
Route::post('menu-items', 'SystemSettings\MenuManagerController@items')->name('menu-items');
Route::post('single-menu-item', 'SystemSettings\MenuManagerController@menuItem')->name('single-menu-item');
Route::post('store-menu-item', 'SystemSettings\MenuManagerController@storeItem')->name('store-menu-item');
Route::post('delete-menu-item', 'SystemSettings\MenuManagerController@destroyItem')->name('delete-menu-item');
Route::post('save-menu-order', 'SystemSettings\MenuManagerController@saveMenuOrder')->name('save-menu-order');

/* ================ Menu Privilege Admin Module ================= */
Route::match(['get', 'post'], 'menu-privilege', 'SystemSettings\MenuPrivilegeManagerController@privilege')->name('menu-privilege');
Route::post('set-privilege', 'SystemSettings\MenuPrivilegeManagerController@setPrivilege')->name('set-privilege');