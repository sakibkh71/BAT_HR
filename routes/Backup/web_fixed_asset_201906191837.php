<?php

Route::get('fams', 'FixedAsset\FixedAssetController@index')->name('fams');
Route::get('fams/asset', 'FixedAsset\FixedAssetController@get_asset_list')->name('asset_list');
Route::post('fams/asset/ajax-list', 'FixedAsset\AssetController@ajaxList')->name('fams-asset-ajax-list');
Route::get('fams/asset/create/{id?}/{tab?}', 'FixedAsset\FixedAssetController@addAsset')->name('add-asset');
Route::get('fams/asset/assign', 'FixedAsset\AssetController@assetAssignList')->name('fams-asset-assign');
Route::get('fams/edit-asset/{id?}/{tab?}', 'FixedAsset\FixedAssetController@editAsset')->name('edit-asset');
Route::post('fams/save-asset-register', 'FixedAsset\FixedAssetController@saveGeneralInfo')->name('save-asset-register');
Route::post('fams/load-asset-details', 'FixedAsset\FixedAssetController@loadAssetDetails')->name('load-asset-details');
Route::post('fams/save-asset-details', 'FixedAsset\FixedAssetController@saveAssetDetails')->name('save-asset-details');
Route::post('fams/save-asset-assign', 'FixedAsset\FixedAssetAssignController@saveAssetAssign')->name('save-asset-assign');
Route::post('fams/asset-assign-ajax-list', 'FixedAsset\FixedAssetAssignController@assetAssignAjaxList')->name('asset-assign-ajax-list');
Route::get('fams/items-list', 'FixedAsset\ItemsController@index')->name('items-list');
Route::post('fams/ajax-list', 'FixedAsset\ItemsController@ajaxList')->name('ajax-list');
Route::post('fams/get-asset-assign', 'FixedAsset\FixedAssetAssignController@getAssetAssign')->name('get-asset-assign');
Route::post('fams/asset-ajax-list', 'FixedAsset\FixedAssetController@assetAjaxList')->name('asset-ajax-list');
Route::post('fams/count_fam_type_name', 'FixedAsset\MasterController@countFamItems')->name('count_fam_type_name');
Route::get('fams/view-asset/{id?}/{tab?}', 'FixedAsset\FixedAssetController@viewAsset')->name('view-asset');
Route::get('fams/view-barcode/{id?}', 'FixedAsset\FixedAssetController@barcodeAsset')->name('view-barcode');
Route::post('fams/search-barcode', 'FixedAsset\FixedAssetController@searchBarcodeAsset')->name('search-barcode');
Route::post('fams/save-asset-depreciation', 'FixedAsset\AssetDepreciationController@saveAssetDepreciation')->name('save-asset-depreciation');
Route::post('fams/save-asset-insurance', 'FixedAsset\AssetInsuranceController@saveAssetInsurance')->name('save-asset-insurance');
Route::post('fams/asset-insurance-ajax-list', 'FixedAsset\AssetInsuranceController@assetInsuranceAjaxList')->name('asset-insurance-ajax-list');
Route::post('fams/save-asset-maintenance', 'FixedAsset\AssetMaintenanceController@saveAssetMaintenance')->name('save-asset-maintenance');

// For testing purpose
Route::get('fams/test', 'FixedAsset\AssetTestController@assetTest')->name('fams-test');
Route::get('fams/fams-test-employee', 'FixedAsset\AssetTestController@assetReqEmpTest')->name('fams-test-employee');
Route::post('fams/save-attachment', 'FixedAsset\AssetTestController@saveAttachment')->name('save-attachment');
Route::post('fams/delete-attachments-ajax', 'FixedAsset\AssetTestController@deleteAttachmentsAjax')->name('delete-attachments-ajax');
// End test
Route::get('fams/asset-maintenance', 'FixedAsset\AssetMaintenanceController@assetMaintenanceMenu')->name('asset-maintenance');
Route::post('fams/asset-details-name-wise/{id?}', 'FixedAsset\AssetMaintenanceController@assetDetails')->name('asset-details-name-wise');
Route::get('fams/maintenance-list', 'FixedAsset\AssetMaintenanceController@getAssetMaintenance')->name('maintenance-list');
Route::post('fams/maintenance-ajax-list', 'FixedAsset\AssetMaintenanceController@MaintenanceAjaxList')->name('maintenance-ajax-list');
Route::post('fams/maintenance-approved-by-me-ajax-list', 'FixedAsset\AssetMaintenanceController@maintenanceApprovedByMeAjaxList')->name('maintenance-approved-by-me-ajax-list');

Route::get('fams/visit-report-entry/{id?}', 'FixedAsset\AssetVisitController@visitReportEntry')->name('visit-report-entry');
Route::post('fams/save-visit-report', 'FixedAsset\AssetVisitController@saveVisitReport')->name('save-visit-report');
Route::post('fams/load-maint-req-visitiors-table', 'FixedAsset\AssetVisitController@loadMaintReqVisitiorsTable')->name('load-maint-req-visitiors-table');
Route::post('fams/get-main-req-visitor', 'FixedAsset\AssetVisitController@getMainReqVisitor')->name('get-main-req-visitor');

Route::get('fams/view-maintenance/{id?}', 'FixedAsset\AssetMaintenanceController@viewAssetMaintenance')->name('view-maintenance');
Route::get('fams/edit-maintenance/{id?}/', 'FixedAsset\AssetMaintenanceController@editMaintenance')->name('edit-maintenance');

Route::post('fams/maintenance-tab-ajax-list', 'FixedAsset\AssetMaintenanceController@MaintenanceTabAjaxList')->name('maintenance-tab-ajax-list');
Route::post('fams/approve-maintenance', 'FixedAsset\AssetMaintenanceController@approveMaintenance')->name('approve-maintenance');

Route::post('fams/save-approval-person', 'FixedAsset\AssetMaintenanceController@saveApprovalPerson')->name('save-approval-person');
Route::post('fams/maintenance-delegation-process', 'FixedAsset\AssetMaintenanceController@maintenanceDelegationProcess')->name('maintenance-delegation-process');
Route::get('fams/manage-maintenance', 'FixedAsset\AssetMaintenanceController@maintenanceManageList')->name('manage-maintenance');
Route::get('fams/maintenance-approval-list', 'FixedAsset\AssetMaintenanceController@maintenanceApprovalList')->name('maintenance-approval-list');

Route::post('fams/save-asset-revaluation', 'FixedAsset\AssetRevaluationController@saveAssetRevaluation')->name('save-asset-revaluation');
Route::post('fams/load-asset-revaluation-form', 'FixedAsset\AssetRevaluationController@loadAssetRevaluationForm')->name('load-asset-revaluation-form');
Route::post('fams/delete-board-member-ajax', 'FixedAsset\AssetRevaluationController@deleteBoardMemberAjax')->name('delete-board-member-ajax');

/*============================ Start Master Entry (Parts) ===================================*/
Route::get('fams/parts', 'FixedAsset\MasterEntry\PartsController@index')->name('fams-parts');
Route::post('fams/parts/ajax-list', 'FixedAsset\MasterEntry\PartsController@ajaxList')->name('fams-parts-ajax-list');
Route::get('fams/parts/create', 'FixedAsset\MasterEntry\PartsController@create')->name('fams-parts-create');
Route::post('fams/parts/store', 'FixedAsset\MasterEntry\PartsController@store')->name('fams-parts-store');
Route::get('fams/parts/edit/{id?}', 'FixedAsset\MasterEntry\PartsController@edit')->name('fams-parts-edit');
Route::post('fams/parts/update', 'FixedAsset\MasterEntry\PartsController@update')->name('fams-parts-update');
Route::get('fams/parts/show/{id?}', 'FixedAsset\MasterEntry\PartsController@show')->name('fams-parts-show');
Route::post('fams/parts/delete', 'FixedAsset\MasterEntry\PartsController@destroy')->name('fams-parts-delete');
/*============================ End Master Entry (Parts) ===================================*/

/*============================ Start Master Entry (Vendors Services) ===================================*/
Route::get('fams/vendors-services', 'FixedAsset\MasterEntry\VendorsServicesController@index')->name('fams-vendors-services');
Route::post('fams/vendors-services/ajax-list', 'FixedAsset\MasterEntry\VendorsServicesController@ajaxList')->name('fams-vendors-services-ajax-list');
Route::get('fams/vendors-services/create', 'FixedAsset\MasterEntry\VendorsServicesController@create')->name('fams-vendors-services-create');
Route::post('fams/vendors-services/store', 'FixedAsset\MasterEntry\VendorsServicesController@store')->name('fams-vendors-services-store');
Route::get('fams/vendors-services/edit/{id?}', 'FixedAsset\MasterEntry\VendorsServicesController@edit')->name('fams-vendors-services-edit');
Route::post('fams/vendors-services/update', 'FixedAsset\MasterEntry\VendorsServicesController@update')->name('fams-vendors-services-update');
Route::get('fams/vendors-services/show/{id?}', 'FixedAsset\MasterEntry\VendorsServicesController@show')->name('fams-vendors-services-show');
Route::post('fams/vendors-services/delete', 'FixedAsset\MasterEntry\VendorsServicesController@destroy')->name('fams-vendors-services-delete');
/*============================ End Master Entry (Vendors Services) ===================================*/
