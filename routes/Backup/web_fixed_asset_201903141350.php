<?php

Route::get('add-asset', 'FixedAsset\FixedAssetController@addAsset')->name('add-asset');
Route::post('save-general-info', 'FixedAsset\FixedAssetController@saveGeneralInfo')->name('save-general-info');

