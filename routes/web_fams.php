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
Route::get('fams/maintenance/receive-list', 'Fams\AssetMaintenanceController@maintenanceReceiveList')->name('fams-maintenance-receive-list');
Route::post('fams/maintenance/receive-store', 'Fams\AssetMaintenanceController@maintenanceReceiveStore')->name('fams-maintenance-receive-store');

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

Route::get('fams/asset-list/{slug}', 'Fams\AssetController@assetList')->name('fams-asset-list');
Route::get('fams/asset-edit/{id?}/{tab?}', 'Fams\AssetController@assetEdit')->name('asset-edit');

Route::post('fams/load-visit-report-entry-form', 'Fams\AssetVisitController@loadVisitReportEntryForm')->name('load-visit-report-entry-form');

Route::post('get-wo-info', 'Fams\FixedAssetController@getWoInfo')->name('get-wo-info');
Route::post('fams/get-product-wise-item', 'Fams\FixedAssetController@getProductWiseItem')->name('get-product-wise-item');

Route::post('fams/save-asset-depreciation-log', 'Fams\AssetDepreciationController@saveAssetDepreciationLog')->name('save-asset-depreciation-log');

/*============================ Start Challan & Gate Pass ===================================*/
//Route::get('fams/challan', 'Fams\AssetChallanController@index')->name('fams-challan');
Route::match(['get', 'post'], 'fams/challan', 'Fams\AssetChallanController@index')->name('fams-challan');
Route::post('fams/challan/ajax-list', 'Fams\AssetChallanController@ajaxList')->name('fams-challan-ajax-list');
Route::get('fams/challan/create/{ids}', 'Fams\AssetChallanController@create')->name('fams-challan-create');
Route::get('fams/challan/create-pdf/{id}', 'Fams\AssetChallanController@createPdf')->name('fams-challan-create-pdf');
Route::post('fams/challan/store', 'Fams\AssetChallanController@store')->name('fams-challan-store');
//Route::match(['get', 'post'], 'challan-process/{reference}/{reference_id}', 'Sales\ChallanController@challanProcess')->name('challan-process');
Route::get('fams/challan/edit/{id?}', 'Fams\AssetChallanController@edit')->name('fams-challan-edit');
Route::post('fams/challan/update', 'Fams\AssetChallanController@update')->name('fams-challan-update');
Route::get('fams/challan/show/{id?}', 'Fams\AssetChallanController@show')->name('fams-challan-show');
Route::get('fams/challan/delivery/{id?}', 'Fams\AssetChallanController@delivery')->name('fams-challan-delivery');
Route::post('fams/challan/delivered', 'Fams\AssetChallanController@delivered')->name('fams-challan-delivered');
Route::post('fams/challan/search-vendor-services', 'Fams\AssetChallanController@searchVendorServices')->name('fams-challan-search-vendor-services');

Route::get('fams/gate-pass/create/{id?}', 'Fams\AssetChallanController@createGatePass')->name('fams-gate-pass-create');
Route::get('fams/gate-pass/create-pdf/{id}', 'Fams\AssetChallanController@createGatePassPdf')->name('fams-gate-pass-create-pdf');
/*============================ End Challan & Gate Pass ===================================*/

/*============================ Start Goods Receive ===================================*/
Route::match(['get', 'post'], 'fams/asset/receive', 'Fams\AssetReceiveController@index')->name('fams-asset-receive');
Route::post('fams/asset/receive/store', 'Fams\AssetReceiveController@store')->name('fams-asset-receive-store');
/*============================ End Goods Receive ===================================*/

Route::post('fams/asset/ajax-load-products-combo-by-cat', 'Fams\AssetController@ajaxLoadProductsComboByCat')->name('fams-asset-ajax-load-products-combo-by-cat');
