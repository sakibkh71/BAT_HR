<?php

Route::get('fams', 'Fams\FixedAssetController@index')->name('fams');
Route::get('fams/asset', 'Fams\FixedAssetController@get_asset_list')->name('asset_list');
Route::post('fams/asset/ajax-list', 'Fams\AssetController@ajaxList')->name('fams-asset-ajax-list');
Route::get('fams/asset/create/{id?}/{tab?}', 'Fams\FixedAssetController@addAsset')->name('add-asset');

Route::get('fams/edit-asset/{id?}/{tab?}', 'Fams\FixedAssetController@editAsset')->name('edit-asset');
Route::post('fams/save-asset-register', 'Fams\FixedAssetController@saveGeneralInfo')->name('save-asset-register');
Route::post('fams/load-asset-details', 'Fams\FixedAssetController@loadAssetDetails')->name('load-asset-details');
Route::post('fams/save-asset-details', 'Fams\FixedAssetController@saveAssetDetails')->name('save-asset-details');
Route::post('fams/save-asset-assign', 'Fams\FixedAssetAssignController@saveAssetAssign')->name('save-asset-assign');
Route::post('fams/asset-assign-ajax-list', 'Fams\FixedAssetAssignController@assetAssignAjaxList')->name('asset-assign-ajax-list');
Route::get('fams/items-list', 'Fams\ItemsController@index')->name('items-list');
Route::post('fams/ajax-list', 'Fams\ItemsController@ajaxList')->name('ajax-list');
Route::post('fams/get-asset-assign', 'Fams\FixedAssetAssignController@getAssetAssign')->name('get-asset-assign');
Route::post('fams/asset-ajax-list', 'Fams\FixedAssetController@assetAjaxList')->name('asset-ajax-list');
Route::post('fams/count_fam_type_name', 'Fams\MasterController@countFamItems')->name('count_fam_type_name');
Route::get('fams/view-asset/{id?}/{tab?}', 'Fams\FixedAssetController@viewAsset')->name('view-asset');
Route::get('fams/view-barcode/{id?}', 'Fams\FixedAssetController@barcodeAsset')->name('view-barcode');
Route::post('fams/search-barcode', 'Fams\FixedAssetController@searchBarcodeAsset')->name('search-barcode');
Route::post('fams/save-asset-depreciation', 'Fams\AssetDepreciationController@saveAssetDepreciation')->name('save-asset-depreciation');
Route::post('fams/save-asset-insurance', 'Fams\AssetInsuranceController@saveAssetInsurance')->name('save-asset-insurance');
Route::post('fams/asset-insurance-ajax-list', 'Fams\AssetInsuranceController@assetInsuranceAjaxList')->name('asset-insurance-ajax-list');
Route::post('fams/save-asset-maintenance', 'Fams\AssetMaintenanceController@saveAssetMaintenance')->name('save-asset-maintenance');

/*============================ Start Testing Purpose ===================================*/
Route::get('fams/test', 'Fams\AssetTestController@assetTest')->name('fams-test');
Route::get('fams/fams-test-employee', 'Fams\AssetTestController@assetReqEmpTest')->name('fams-test-employee');
Route::post('fams/save-attachment', 'Fams\AssetTestController@saveAttachment')->name('save-attachment');
Route::post('fams/delete-attachments-ajax', 'Fams\AssetTestController@deleteAttachmentsAjax')->name('delete-attachments-ajax');
/*============================ End Testing Purpose ===================================*/

Route::get('fams/asset-maintenance', 'Fams\AssetMaintenanceController@assetMaintenanceMenu')->name('asset-maintenance');
Route::post('fams/asset-details-name-wise/{id?}', 'Fams\AssetMaintenanceController@assetDetails')->name('asset-details-name-wise');
Route::get('fams/maintenance-list', 'Fams\AssetMaintenanceController@getAssetMaintenance')->name('maintenance-list');
Route::post('fams/maintenance-ajax-list', 'Fams\AssetMaintenanceController@MaintenanceAjaxList')->name('maintenance-ajax-list');
Route::post('fams/maintenance-approved-by-me-ajax-list', 'Fams\AssetMaintenanceController@maintenanceApprovedByMeAjaxList')->name('maintenance-approved-by-me-ajax-list');

Route::get('fams/visit-report-entry/{id?}', 'Fams\AssetVisitController@visitReportEntry')->name('visit-report-entry');
Route::post('fams/save-visit-report', 'Fams\AssetVisitController@saveVisitReport')->name('save-visit-report');
Route::post('fams/load-maint-req-visitiors-table', 'Fams\AssetVisitController@loadMaintReqVisitiorsTable')->name('load-maint-req-visitiors-table');
Route::post('fams/get-main-req-visitor', 'Fams\AssetVisitController@getMainReqVisitor')->name('get-main-req-visitor');

Route::get('fams/view-maintenance/{id?}', 'Fams\AssetMaintenanceController@viewAssetMaintenance')->name('view-maintenance');
Route::get('fams/edit-maintenance/{id?}/', 'Fams\AssetMaintenanceController@editMaintenance')->name('edit-maintenance');

Route::post('fams/maintenance-tab-ajax-list', 'Fams\AssetMaintenanceController@MaintenanceTabAjaxList')->name('maintenance-tab-ajax-list');
Route::post('fams/approve-maintenance', 'Fams\AssetMaintenanceController@approveMaintenance')->name('approve-maintenance');

Route::post('fams/save-approval-person', 'Fams\AssetMaintenanceController@saveApprovalPerson')->name('save-approval-person');
Route::post('fams/maintenance-delegation-process', 'Fams\AssetMaintenanceController@maintenanceDelegationProcess')->name('maintenance-delegation-process');
Route::get('fams/manage-maintenance', 'Fams\AssetMaintenanceController@maintenanceManageList')->name('manage-maintenance');
Route::get('fams/maintenance-approval-list', 'Fams\AssetMaintenanceController@maintenanceApprovalList')->name('maintenance-approval-list');

Route::post('fams/save-asset-revaluation', 'Fams\AssetRevaluationController@saveAssetRevaluation')->name('save-asset-revaluation');
Route::post('fams/load-asset-revaluation-form', 'Fams\AssetRevaluationController@loadAssetRevaluationForm')->name('load-asset-revaluation-form');
Route::post('fams/delete-board-member-ajax', 'Fams\AssetRevaluationController@deleteBoardMemberAjax')->name('delete-board-member-ajax');

/*============================ Start Master Entry (Parts) ===================================*/
Route::get('fams/parts', 'Fams\MasterEntry\PartsController@index')->name('fams-parts');
Route::post('fams/parts/ajax-list', 'Fams\MasterEntry\PartsController@ajaxList')->name('fams-parts-ajax-list');
Route::get('fams/parts/create', 'Fams\MasterEntry\PartsController@create')->name('fams-parts-create');
Route::post('fams/parts/store', 'Fams\MasterEntry\PartsController@store')->name('fams-parts-store');
Route::get('fams/parts/edit/{id?}', 'Fams\MasterEntry\PartsController@edit')->name('fams-parts-edit');
Route::post('fams/parts/update', 'Fams\MasterEntry\PartsController@update')->name('fams-parts-update');
Route::get('fams/parts/show/{id?}', 'Fams\MasterEntry\PartsController@show')->name('fams-parts-show');
Route::post('fams/parts/delete', 'Fams\MasterEntry\PartsController@destroy')->name('fams-parts-delete');
/*============================ End Master Entry (Parts) ===================================*/

/*============================ Start Master Entry (Vendors Services) ===================================*/
Route::get('fams/vendors-services', 'Fams\MasterEntry\VendorsServicesController@index')->name('fams-vendors-services');
Route::post('fams/vendors-services/ajax-list', 'Fams\MasterEntry\VendorsServicesController@ajaxList')->name('fams-vendors-services-ajax-list');
Route::get('fams/vendors-services/create', 'Fams\MasterEntry\VendorsServicesController@create')->name('fams-vendors-services-create');
Route::post('fams/vendors-services/store', 'Fams\MasterEntry\VendorsServicesController@store')->name('fams-vendors-services-store');
Route::get('fams/vendors-services/edit/{id?}', 'Fams\MasterEntry\VendorsServicesController@edit')->name('fams-vendors-services-edit');
Route::post('fams/vendors-services/update', 'Fams\MasterEntry\VendorsServicesController@update')->name('fams-vendors-services-update');
Route::get('fams/vendors-services/show/{id?}', 'Fams\MasterEntry\VendorsServicesController@show')->name('fams-vendors-services-show');
Route::post('fams/vendors-services/delete', 'Fams\MasterEntry\VendorsServicesController@destroy')->name('fams-vendors-services-delete');
/*============================ End Master Entry (Vendors Services) ===================================*/

Route::get('fams/asset/assign', 'Fams\AssetController@assetAssign')->name('fams-asset-assign');
Route::get('fams/asset/transfer', 'Fams\AssetController@assetTransfer')->name('fams-asset-transfer');
Route::get('fams/asset/insurance', 'Fams\AssetController@assetInsurance')->name('fams-asset-insurance');
Route::get('fams/asset/depreciation', 'Fams\AssetController@assetDepreciation')->name('fams-asset-depreciation');
Route::get('fams/asset/revaluation', 'Fams\AssetController@assetRevaluation')->name('fams-asset-revaluation');
