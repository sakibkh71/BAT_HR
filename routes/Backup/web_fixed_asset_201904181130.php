<?php

Route::get('fixed-asset', 'FixedAsset\FixedAssetController@index')->name('fixed-asset');
Route::get('fixed-asset/add-asset', 'FixedAsset\FixedAssetController@addAsset')->name('add-asset');
Route::post('fixed-asset/save-general-info', 'FixedAsset\FixedAssetController@saveGeneralInfo')->name('save-general-info');
Route::post('fixed-asset/load-asset-details', 'FixedAsset\FixedAssetController@loadAssetDetails')->name('load-asset-details');
Route::post('fixed-asset/save-asset-details', 'FixedAsset\FixedAssetController@saveAssetDetails')->name('save-asset-details');

