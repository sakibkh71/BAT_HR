<?php
Route::fallback(function(){
    return response()->view('errors.404');
});

Route::get('/home', 'HomeController@index')->name('home');

//Route::get('id-test', 'HomeController@idTest')->name('home');
/*================================================================================================*/
/*=============================================customer===================================================*/
/**
 * USER AUTHENTICATION ===
 */
Route::get('/','Auth\LoginController@showLoginForm')->name('login');
Auth::routes();
Route::post('multi-login-action','User\UserController@multiLoginAction');
Route::post('notify-dismiss','User\UserController@notifyDismiss');
/*================================================================================================*/
/**
 * USER MANAGEMENT ===
 */
Route::resource('users', 'User\UserController');
Route::post('getUserRaw', 'User\UserController@getUserRaw')->name('getUserRaw');
//Route::get('profile', 'User\UserController@getUserProfile')->name('getUserProfile');
Route::post('profile', 'User\UserController@getUserProfile')->name('get-user-profile');
/*================================================================================================*/
/**
 * SYSTEM ACCESS ===
 */
Route::get('/moduleChanger/{id}','ModuleController@moduleChanger')->name('moduleChanger');
/*================================================================================================*/
/**
 * MENU MANAGER ===
 */
Route::get('menu_list', 'MenuManagement@menu_list')->name('menu_list');
Route::post('menu_entry', 'MenuManagement@menu_entry')->name('menu_entry');
Route::post('getMenuRaw', 'MenuManagement@getMenuRaw')->name('getMenuRaw');
Route::post('saveMenuOrder', 'MenuManagement@saveMenuOrder')->name('saveMenuOrder');
Route::post('menuDelete', 'MenuManagement@menuDelete')->name('menuDelete');
Route::get('get_menu_for_level', 'MenuManagement@get_menu_for_level')->name('get_menu_for_level');

/*======================================================================================================================*/
/**
 * Form Maker ===
 */
Route::get('form/{formname}', 'Master\MasterFormController@buildMasterForm')->name('form');
Route::get('entryform/{formname}', 'Master\MasterFormController@buildFormForEntry')->name('entryform');
Route::get('editform/{formname}/{table_name?}/{primary_key_field?}/{id?}', 'Master\MasterFormController@buildFormForEdit')->name('editform');

Route::post('masterFormDataStore', 'Master\MasterFormController@masterFormDataStore')->name('masterFormDataStore');

/*======================================================================================================================*/
/**
 * Grid Maker ===
 */
Route::get('grid/{gridname}', 'Master\MasterGridController@getGrid')->name('grid');
Route::get('modalgrid/{gridname}', 'Master\MasterGridController@getModalGrid')->name('modalgrid');
Route::post('delete-record', 'Master\MasterGridController@deleteRecord');

/*======================================================================================================================*/
Route::resource('designations', 'Master\DesignationController');
Route::post('getDesignationRaw', 'Master\DesignationController@getDesignationRaw')->name('getDesignationRaw');

Route::resource('currencys', 'Master\CurrencyController');
Route::post('getCurrencyRaw', 'Master\CurrencyController@getCurrencyRaw')->name('getCurrencyRaw');

Route::resource('currencys', 'Currency\CurrencyController');
Route::post('getCurrencyRaw', 'Currency\CurrencyController@getCurrencyRaw')->name('getCurrencyRaw');
Route::resource('product_categorys', 'Product_category\Product_categoryController');

Route::get('add-product-category', 'ProductCategory\ProductCategoryController@create')->name('add-product-category');
Route::get('add-product-category/{id}', 'ProductCategory\ProductCategoryController@create')->name('edit-product-category');

Route::post('product-category-store', 'ProductCategory\ProductCategoryController@store')->name('product-category-store');
Route::post('product-category-update/{id}', 'ProductCategory\ProductCategoryController@update')->name('product-category-update');

Route::post('getProduct_categoryRaw', 'Product_category\Product_categoryController@getProduct_categoryRaw')->name('getProduct_categoryRaw');
Route::post('saveProductCategory', 'Product_category\Product_categoryController@saveProductCategory')->name('saveProductCategory');

Route::resource('product_brands', 'Product_brand\Product_brandController');
Route::post('getProduct_brandRaw', 'Product_brand\Product_brandController@getProduct_brandRaw')->name('getProduct_brandRaw');
Route::resource('product_models', 'Product_model\Product_modelController');
Route::post('getProduct_modelRaw', 'Product_model\Product_modelController@getProduct_modelRaw')->name('getProduct_modelRaw');
Route::resource('product_uoms', 'Product_uom\Product_uomController');
Route::post('getProduct_uomRaw', 'Product_uom\Product_uomController@getProduct_uomRaw')->name('getProduct_uomRaw');
Route::resource('product_spec_standards', 'Product_spec_standard\Product_spec_standardController');
Route::post('getProduct_spec_standardRaw', 'Product_spec_standard\Product_spec_standardController@getProduct_spec_standardRaw')->name('getProduct_spec_standardRaw');
Route::resource('products', 'Product\ProductController');
Route::post('getProductRaw', 'Product\ProductController@getProductRaw')->name('getProductRaw');
Route::resource('product_spec_others', 'Product_spec_other\Product_spec_otherController');
Route::post('getProduct_spec_otherRaw', 'Product_spec_other\Product_spec_otherController@getProduct_spec_otherRaw')->name('getProduct_spec_otherRaw');

/*============ product-vs-vendors ==========================================*/
Route::get('product-vs-vendors', 'Product_vs_vendor\ProductVsVendor@index');
Route::post('getVendor', 'Product_vs_vendor\ProductVsVendor@getVendor');
Route::post('getProduct', 'Product_vs_vendor\ProductVsVendor@getProduct');
Route::post('saveProduct', 'Product_vs_vendor\ProductVsVendor@saveProduct');
Route::post('removeProduct', 'Product_vs_vendor\ProductVsVendor@removeProduct');
Route::post('saveVendor', 'Product_vs_vendor\ProductVsVendor@saveVendor');
Route::post('removeVendor', 'Product_vs_vendor\ProductVsVendor@removeVendor');

/*============ brand-vs-models ==========================================*/
Route::get('brand-vs-models', 'Brand_vs_model\BrandVsModel@index');
Route::post('getBrand', 'Brand_vs_model\BrandVsModel@getBrand');
Route::post('getModel', 'Brand_vs_model\BrandVsModel@getModel');
Route::post('saveBrand', 'Brand_vs_model\BrandVsModel@saveBrand');
Route::post('removeBrand', 'Brand_vs_model\BrandVsModel@removeBrand');
Route::post('saveModel', 'Brand_vs_model\BrandVsModel@saveModel');
Route::post('removeModel', 'Brand_vs_model\BrandVsModel@removeModel');

/*============Purchase Requisition ========================================*/
Route::match(['get', 'post'],'purchase-requisitions', 'Purchase\RequisitionController@requisitionList')->name('purchase-requisitions');
Route::get('new-purchase-requisition', 'Purchase\RequisitionController@getNewPurchaseRequisition')->name('new-purchase-requisition');
Route::get('new-purchase-requisition/{requisition_id}', 'Purchase\RequisitionController@getNewPurchaseRequisition')->name('edit-purchase-requisition');
Route::post('get-product-list', 'Purchase\RequisitionController@getProductList')->name('get-product-list');
Route::post('get-purchase-requisition-items', 'Purchase\RequisitionController@getPurchaseRequisitionItems')->name('get-purchase-requisition-items');
Route::post('get-req-data', 'Purchase\RequisitionController@getReqData')->name('get-req-data');

Route::post('getSpecStandards', 'Purchase\RequisitionController@getSpecStandards')->name('getSpecStandards');
Route::post('getSpecOthers', 'Purchase\RequisitionController@getSpecOthers')->name('getSpecOthers');
Route::post('save-spec', 'Purchase\RequisitionController@saveSpec')->name('save-spec');
Route::post('save-requisition', 'Purchase\RequisitionController@saveRequisition')->name('save-requisition');
Route::post('saveRequisitionDetails', 'Purchase\RequisitionController@saveRequisitionDetails')->name('saveRequisitionDetails');
Route::post('get-confirm-requisition', 'Purchase\RequisitionController@getConfirmRequisition')->name('get-confirm-requisition');
Route::post('getRequisition', 'Purchase\RequisitionController@getRequisition')->name('getRequisition');
Route::post('update-requisition-details', 'Purchase\RequisitionController@updateRequisitionDetails')->name('update-requisition-details');
Route::post('update-requisition-status', 'Purchase\RequisitionController@updateRequisitionStatus')->name('update-requisition-status');

Route::post('getPurchaseRequisitionModal', 'Purchase\RequisitionController@getPurchaseRequisitionModal')->name('getPurchaseRequisitionModal');
Route::post('delete-requisition-item', 'Purchase\RequisitionController@deleteRequisitionItem')->name('delete-requisition-item');
Route::post('get-spec-standards-and-others', 'Purchase\RequisitionController@getSpecStandardsAndOthers')->name('get-spec-standards-and-others');
Route::post('get-exist-requisition-items', 'Purchase\RequisitionController@getExistRequisitionItems')->name('get-exist-requisition-items');
Route::post('updatePurchaseRequisition', 'Purchase\RequisitionController@updatePurchaseRequisition')->name('updatePurchaseRequisition');
Route::post('getSelectedProducts', 'Purchase\RequisitionController@getSelectedProducts')->name('getSelectedProducts');
Route::post('update-requisition', 'Purchase\RequisitionController@updateRequisition')->name('update-requisition');
Route::post('delete-purchase-requisition', 'Purchase\RequisitionController@deletePurchaseRequisition')->name('delete-purchase-requisition');

Route::match(['get', 'post'], 'product-wise-list', 'Purchase\RequisitionController@productWiseList')->name('product-wise-list');
Route::post('requisition-update-by-requisitionId', 'Purchase\RequisitionController@requisitionUpdateByRequisitionId')->name('requisition-update-by-requisitionId');
Route::get('requisition-update-by-product/{prdid}', 'Purchase\RequisitionController@requisitionUpdateByProduct')->name('requisition-update-by-product');
Route::get('cs/{cs_code}/{requisition_id?}/{mode?}', 'Purchase\RequisitionController@getCSForm')->name('cs');
Route::post('get-vendor-form', 'Purchase\RequisitionController@getVendorForm')->name('get-vendor-form');
Route::post('save-vendor-information/{id?}', 'Purchase\RequisitionController@saveVendorInformation')->name('save-vendor-information');
Route::post('get-all-vendors', 'Purchase\RequisitionController@getAllVendors')->name('get-all-vendors');
Route::post('get-tag-vendors', 'Purchase\RequisitionController@getTagVendors')->name('get-tag-vendors');
Route::post('remove-purchase-cs-by-vendor', 'Purchase\RequisitionController@removePurchaseCSBYVendor')->name('remove-purchase-cs-by-vendor');
Route::post('getConfirmPurchaseCS', 'Purchase\RequisitionController@getConfirmPurchaseCS')->name('getConfirmPurchaseCS');

Route::post('edit-purchase-cs-by-vendor', 'Purchase\RequisitionController@editPurchaseCSBYVendor')->name('edit-purchase-cs-by-vendor');
Route::post('update-purchase-cs-by-vendor', 'Purchase\RequisitionController@updatePurchaseCSBYVendor')->name('update-purchase-cs-by-vendor');

Route::match(['get','post'],'requisition-wise-list', 'Purchase\RequisitionController@requisitionWiseList')->name('requisition-wise-list');
Route::post('requisition-wise-product', 'Purchase\RequisitionController@getRequisitionWiseProduct')->name('requisition-wise-product');
Route::post('requisition-details', 'Purchase\RequisitionController@getRequisitionWiseRequisitionDetails')->name('requisition-details');

/*============ Stock wise requisition ========================================*/
Route::get('stock-wise-requisition/{type}', 'Purchase\StockWiseRequisition\StockWiseRequisitionController@stockWiseRequisition');
Route::post('update-cart', 'Purchase\StockWiseRequisition\StockWiseRequisitionController@updateCart');
Route::post('remove-cart', 'Purchase\StockWiseRequisition\StockWiseRequisitionController@removeCart');
Route::post('save-cart-requisition', 'Purchase\StockWiseRequisition\StockWiseRequisitionController@saveCartRequisition');
Route::get('view-cart/{type}', 'Purchase\StockWiseRequisition\StockWiseRequisitionController@viewCart');


/*============ Purchase Requisition APPROVAL ========================================*/
Route::get('requisition-approve-list', 'Purchase\RequisitionController@requisitionApprovalList')->name('requisition-approve-list');
Route::get('requisition-approval-details/{pr_id}', 'Purchase\RequisitionController@requisitionApprovalDetails')->name('requisition-approval-details');
Route::match(['get','post'],'requisition-approve-form/{id}/{type?}', 'Purchase\RequisitionController@getPurchaseRequisitionApprovalForm')->name('requisition-approve-form');
Route::post('requisition-approve', 'Purchase\RequisitionController@requisitionApproved')->name('requisition-approve');
Route::post('requisition-bulk-approved', 'Purchase\RequisitionController@requisitionBulkApproved')->name('requisition-bulk-approved');

/*============ Purchase CS APPROVAL ========================================*/
Route::match(['get','post'],'purchase-cs-list', 'Purchase\CsController@getCsList')->name('purchase.getCsList');
Route::get('purchase-cs-confirm-list', 'Purchase\CsController@getConfirmCsList')->name('purchase.getConfirmCsList');
Route::get('purchase-cs-details/{cs_code}', 'Purchase\CsController@getCsDetails')->name('purchase.getCsDetails');
Route::post('vendor-spec', 'Purchase\CsController@getVendorSpec')->name('vendor-spec');
Route::post('store-cs-approval', 'Purchase\CsController@csApproval')->name('store-cs-approval');
Route::post('requisition-approve', 'Purchase\RequisitionController@requisitionApproved')->name('requisition-approve');
Route::post('get-user-designation', 'Purchase\CsController@getUserDesignation');
Route::post('get-add-more-html', 'Purchase\CsController@getAddMreHtml');

Route::get('purchase-cs-view/{cs_code}', 'Purchase\CsController@csView')->name('purchase-cs-view');

/*============ Purchase PO CREATE ========================================*/
Route::match(['get','post'], 'po-processing-list', 'Purchase\PoController@poProcesingList')->name('po-processing-list');

/*Route::get('po-processing-list/{type}', 'Purchase\PoController@poProcesingList')->name('po-processing-list');*/
Route::get('po-processing/{type}/{id}', 'Purchase\PoController@poProcessing')->name('po-processing');
Route::get('po-confirm/{po_no}', 'Purchase\PoController@poConfirmation')->name('po-confirm');
Route::post('po-create', 'Purchase\PoController@store')->name('po-store');
Route::post('po-update', 'Purchase\PoController@update')->name('po-update');
Route::post('remove-product-form-po', 'Purchase\PoController@removeProduct')->name('po-rm-product');

//PO List
Route::match(array('GET', 'POST'),'po-list', 'Purchase\PoController@polist')->name('po-list');
Route::post('remove-po-items', 'Purchase\PoController@removePoItems')->name('remove-po-items');
Route::get('po-details/{po_no}/{viewfor?}', 'Purchase\PoController@poDetails')->name('po-details');
Route::post('get-purchase-products-landing-cost', 'Purchase\PoController@productLandingCost')->name('get-purchase-products-landing-cost');
Route::post('store-purchase-landing-cost', 'Purchase\PoController@productLandingCostStore')->name('store-purchase-landing-cost');


// Manual Product Order (PO)
Route::get('manual-po', 'Purchase\PoController@manualPo')->name('manual-po');
Route::get('ajax-vendor-list', 'Purchase\PoController@getVendorList')->name('ajax-vendor-list');
Route::get('ajax-vendor-info', 'Purchase\PoController@getVendorInfo')->name('ajax-vendor-info');
Route::get('ajax-product-list', 'Purchase\PoController@getProductList')->name('ajax-product-list');
Route::post('ajax-product-item', 'Purchase\PoController@getProductItem')->name('ajax-product-item');
Route::post('manual-po-store', 'Purchase\PoController@manualPoStore')->name('manual-po-store');
Route::get('manual-po/{po_no}', 'Purchase\PoController@editManualPo')->name('manual-po-edit');
Route::post('manual-po-update/{po_no}', 'Purchase\PoController@manualPoUpdate')->name('manual-po-update');

//Manual PI
Route::get('manual-pi/{po_no}', 'Purchase\PoController@manualPI')->name('manual-pi');
Route::post('manual-pi-store/{po_no}', 'Purchase\PoController@manualPiStore')->name('manual-pi-store');

/*==========================PO Approval List================================*/
Route::get('po-approval-list', 'Purchase\PoController@getApprovalPOList')->name('po-approval-list');
Route::post('po-bulk-approved', 'Purchase\PoController@poBulkApproved')->name('po-bulk-approved');
Route::match(['get', 'post'], 'po-approval-view/{po_no}/{viewfor}', 'Purchase\PoController@poDetails')->name('po-approval-view');
Route::post('po-approval-decline', 'Purchase\PoController@poApprovalDecline');


/*============ Purchase related Reports ========================================*/
//Product Wise Current Stock
Route::get('product-wise-current-stock', 'Purchase\reports\ProductWiseCurrentStock@productWiseCurrentStock');
Route::post('dataTableSubmit', 'Purchase\reports\ProductWiseCurrentStock@dataTableSubmit' )->name('dataTableSubmit');
Route::post('product-wise-current-stock-export', 'Purchase\reports\ProductWiseCurrentStock@productWiseCurrentStockExport' );



//Warehouse Wise Data
Route::get('warehouse-wise-current-stock', 'Purchase\reports\WarehouseWiseCurrentStock@warehouseWiseCurrentStock');
Route::post('get-submitted-warehouse-data', 'Purchase\reports\WarehouseWiseCurrentStock@dataTableSubmit')->name('get-submitted-warehouse-data');
Route::post('warehouse-wise-current-stock-export', 'Purchase\reports\WarehouseWiseCurrentStock@warehouseWiseStockExport')->name('warehouse-wise-current-stock-export');


//Order VS Delivery
Route::get('order-vs-delivery', 'Purchase\reports\OrderVsDelivery@fnOrderVsDelivery')->name('order-vs-delivery');
Route::post('order-delivery-submit', 'Purchase\reports\OrderVsDelivery@orderDeliverySubmit' );
Route::post('order-vs-delivery-export', 'Purchase\reports\OrderVsDelivery@orderVsDeliveryExport' );


/*============ Stoc Statement Report ==========================*/
Route::get('stock-statement', 'Purchase\reports\StockStatement@fnStockStatement')->name('stock-statement');
Route::post('stock-data-table-submit', 'Purchase\reports\StockStatement@dataTableSubmit' )->name('stockDataTableSubmit');
Route::post('stock-statement-data-export', 'Purchase\reports\StockStatement@stockStatementDataExport' );



/*============ Stock Receive ========================================*/
Route::get('stock-receive', 'Purchase\StockReceive@stockReceive');
Route::get('stock-receive-vendor-list', 'Purchase\StockReceive@getVendorList')->name('stock-receive-vendor-list');
Route::get('stock-receive/{type}/{receive_code?}', 'Purchase\StockReceive@stockReceive');
Route::get('received-stock-list', 'Purchase\StockReceive@receivedStockList');
Route::post('menual-product-list', 'Purchase\StockReceive@getMenualSelectedProducts')->name('menual-product-list');
Route::post('ajax-get-vendor-info', 'Purchase\StockReceive@getVendorInfo')->name('ajax-get-vendor-info');
Route::post('ajax-vendor-polist', 'Purchase\StockReceive@getVendorPoList')->name('ajax-vendor-polist');
Route::post('powise-product-list', 'Purchase\StockReceive@getPoWiseProducts')->name('powise-product-list');
Route::post('stock-receive-store', 'Purchase\StockReceive@store')->name('stock-receive-store');
Route::post('stock-receive-store/{receive_code}', 'Purchase\StockReceive@store')->name('stock-receive-store');
Route::get('stock-receive-preview', 'Purchase\StockReceive@preview')->name('stock-receive-preview');
Route::get('stock-receive-items/{received_code}', 'Purchase\StockReceive@items_add');
Route::post('get-item-spec-standards-and-others', 'Purchase\StockReceive@item_spec');
Route::post('save-item-spec', 'Purchase\StockReceive@save_item_spec');
Route::post('save-item-warranty-and-sl', 'Purchase\StockReceive@save_item_warranty_sl');
Route::post('update-item-warranty-and-sl', 'Purchase\StockReceive@update_item_warranty_sl');

Route::post('save-production-spec', 'Purchase\StockReceive@save_production_spec');
Route::post('save-production-receive', 'Purchase\StockReceive@StockReceiveProduction')->name('save-production-receive');
Route::post('get-production-spec-standards-and-others', 'Purchase\StockReceive@production_spec');
//Route::get('stock-receive/Production', 'Purchase\StockReceive@stockReceive');

/*================================================================================================*/
/*
 * Delegation Process
 */
Route::get('delegation-link-view-test/{code?}', 'Delegation\DelegationProcess@delegationLinkViewTest');
Route::any('delegation-initialize/{path?}', 'Delegation\DelegationProcess@delegationInitialize');
//Route::post('delegation-approval-process', 'Delegation\DelegationProcess@delegationApprovalProcess');
Route::post('delegation-approval-process', 'Delegation\DelegationProcess@delegationApprove');
Route::post('delegation-decline-process', 'Delegation\DelegationProcess@delegationDeclineProcess');
Route::get('waiting-for-approval/{slug}', 'Delegation\DelegationProcess@waitingForApproval');

//Sales Module by Khairul
Route::get('customer','Customer\CustomerController@createNewCustomer')->name('new-customer');
//Route::get('new-customer','Customer\CustomerController@createNewCustomer')->name('new-customer');
Route::post('store-customer','Customer\CustomerController@storeCustomer')->name('store-customer');
Route::post('delete-customers','Customer\CustomerController@deleteCustomer')->name('delete-customers');
Route::get('customer/{id}','Customer\CustomerController@createNewCustomer')->name('new-customer');
Route::get('check-email-availablity','Customer\CustomerController@checkEmailValidity')->name('check-validity');
Route::post('chk-email-availablity','Customer\CustomerController@chkEmailValidity')->name('check-e-validity');
Route::get('check-contact-availability','Customer\CustomerController@checkContactValidity')->name('check-cont-validity');

Route::get('add-more-customer-details', function() {
    return View::make('Customer.contact_person_details',array('exData'=>array()));
});

//User Profile By Khairul 02-12-18
Route::get('profile', 'User\UserController@getUserProfiles')->name('getUserProfile');
Route::post('update-user-profile','User\UserController@updateUserProfile')->name('update-user-profile');

//Password Resetpo-list
Route::get('reset_password','User\UserController@resetUserPassword')->name('resetPassword');
Route::post('reset-password-submit','User\UserController@resetPasswordSubmit');

//DataTable Practice
Route::get('dataTableContent', 'Customer\TestController@getData')->name('getDataTableContent');
Route::post('deleteUsersData', 'Customer\TestController@deleteUserData');

//Delegation Process By Khairul 2018-12-09
Route::get('get-approval-modules', 'Delegation\MyApprovalList@index')->name('approvalModules');

//Delegation Modules
Route::get('get-delegation-list', 'Delegation\MyApprovalList@getDelegationList')->name('get-delegation-list');
Route::match(['get','post'],'get-delegation-list', 'Delegation\MyApprovalList@getDelegationList')->name('get-delegation-list');

//LC
Route::match(['get', 'post'],'list-for-new-lc', 'Purchase\LC\PurchaseLC@weatingForNewLc')->name('list-for-new-lc');

Route::get('purchase-lc/{mode}/{pur_order_code}', 'Purchase\LC\PurchaseLC@createLC')->name('purchase-lc');
Route::post('store-lc-info', 'Purchase\LC\PurchaseLC@storeLCInfo')->name('store-lc-info');
Route::match(['get', 'post'],'get-lc-list', 'Purchase\LC\PurchaseLC@index')->name('get-lc-list');
Route::post('delete-lc-data','Purchase\LC\PurchaseLC@deleteLCData')->name('delete-lc-data');
Route::post('get-bank-branches','Purchase\LC\PurchaseLC@getBankBranchName')->name('branchList');
Route::get('edit-lc-info/{mode}/{id}','Purchase\LC\PurchaseLC@createLC')->name('edit-purchase-lc');
Route::post('view-lc-info','Purchase\LC\PurchaseLC@wiewLC')->name('view-lc-info');
Route::match(['get', 'post'],'lc-approval-view/{code}','Purchase\LC\PurchaseLC@lcApprovalView')->name('lc-approval-view');

/*==========================LC Approval List================================*/
Route::get('lc-approval-list', 'Purchase\LC\PurchaseLC@getApprovalLCList')->name('lc-approval-list');
Route::post('lc-bulk-approved', 'Purchase\LC\PurchaseLC@lcBulkApproved')->name('lc-bulk-approved');
Route::post('lc-approval-decline', 'Purchase\LC\PurchaseLC@lcApprovalDecline');


/*=========================== create Product ==========================================*/
Route::get('create-product','Product\ProductController@create')->name('create-product');
Route::get('create-product/{id}','Product\ProductController@edit')->name('edit-product');
Route::get('view-product/{id}','Product\ProductController@view')->name('view-product');
Route::post('store-product','Product\ProductController@store')->name('store-product');
Route::post('update-product','Product\ProductController@update')->name('update-product');
Route::post('delete-product','Product\ProductController@destroy')->name('delete-product');

Route::post('products-list','Product\ProductController@getProductsList')->name('products-list');
Route::post('get-vendor-list','Product\ProductController@getVendorList')->name('get-vendor-list');
Route::post('delete-vendor','Product\ProductController@deleteVendor')->name('delete-vendor');
Route::post('get-brand-list','Product\ProductController@getBrandList')->name('get-brand-list');
Route::post('delete-brand','Product\ProductController@deleteBrand')->name('delete-brand');
Route::post('delete-default-spec-standard','Product\ProductController@deleteDefaultSpecStandard')->name('delete-default-spec-standard');
Route::post('delete-other-spec','Product\ProductController@deleteOtherSpec')->name('delete-other-spec');
Route::post('get-default-spec-info','Product\ProductController@getDefaultSpecStandard')->name('get-default-spec-info');
Route::post('get-other-spec-info','Product\ProductController@getOtherSpec')->name('get-other-spec-info');
Route::post('save-vendor-for-product','Product\ProductController@saveVendorForProduct')->name('save-vendor-for-product');
Route::post('save-brand-for-product','Product\ProductController@saveBrandForProduct')->name('save-brand-for-product');
Route::post('save-product-default-standard-specifications','Product\ProductController@saveProductSpecStandard')->name('save-product-default-standard-specifications');
Route::post('save-product-other-specifications','Product\ProductController@saveProductotherSpec')->name('save-product-other-specifications');



/*============ Vendor ==============================*/
Route::get('create-vendor', 'Vendor\VendorController@create')->name('create-vendor');
Route::get('create-vendor/{id}', 'Vendor\VendorController@create')->name('edit-vendor');

Route::post('vendor-store', 'Vendor\VendorController@store')->name('vendor-store');
Route::post('vendor-update/{id}', 'Vendor\VendorController@update')->name('vendor-update');

Route::match(['get', 'post'],'ajax-vendor-data', 'Vendor\VendorController@getVendorList')->name('ajax-vendor-data');
Route::post('ajax-bank-branch-data', 'Vendor\VendorController@getBankBranchList')->name('ajax-bank-branch-data');

Route::match(['get', 'post'],'ajax-get-product-list-for-tag', 'Vendor\VendorController@getProductList')->name('ajax-get-product-list-for-tag');
Route::post('ajax-tagged-product-items', 'Vendor\VendorController@tagProductItem')->name('ajax-tagged-product-items');
Route::post('ajax-untagged-product-item', 'Vendor\VendorController@untagProducts')->name('ajax-untagged-product-item');



/*=========================Stock Requisition===================================*/
Route::match(['get', 'post'],'stock-requisitions', 'Inventory\StockRequisitionController@requisitionList')->name('stock-requisitions');
Route::get('create-stock-requisition/{type?}/{requisition_id?}', 'Inventory\StockRequisitionController@createStockRequisition')->name('edit-stock-requisition');
Route::post('get-stock-req-data', 'Inventory\StockRequisitionController@getReqData')->name('get-stock-req-data');
Route::post('get-exist-stock-requisition-items', 'Inventory\StockRequisitionController@getExistRequisitionItems')->name('get-exist-stock-requisition-items');
Route::post('get-confirm-stock-requisition', 'Inventory\StockRequisitionController@getConfirmRequisition')->name('get-confirm-stock-requisition');
Route::post('save-stock-spec', 'Inventory\StockRequisitionController@saveSpec')->name('save-stock-spec');
Route::post('get-stock-spec-standards-and-others', 'Inventory\StockRequisitionController@getSpecStandardsAndOthers')->name('get-stock-spec-standards-and-others');
Route::post('get-stock-allocation', 'Inventory\StockRequisitionController@getStockAllocation')->name('get-stock-allocation');
Route::post('stock-allocation-save', 'Inventory\StockRequisitionController@storeStockAllocation')->name('stock-allocation-save');
Route::post('delete-stock-requisition', 'Inventory\StockRequisitionController@deleteStockRequisition')->name('delete-stock-requisition');
Route::get('stock-requisition-approval-list', 'Inventory\StockRequisitionController@requisitionApprovalList')->name('stock-requisition-approval-list');
Route::post('stock-requisition-approve', 'Inventory\StockRequisitionController@requisitionApproved')->name('stock-requisition-approve');
Route::post('get-stock-requisition-product-list', 'Inventory\StockRequisitionController@getProductList')->name('get-stock-requisition-product-list');

Route::post('save-stock-requisition', 'Inventory\StockRequisitionController@saveRequisition')->name('save-stock-requisition');
Route::post('get-stock-requisition-items', 'Inventory\StockRequisitionController@getStockRequisitionItems')->name('get-stock-requisition-items');
Route::post('delete-stock-requisition-item', 'Inventory\StockRequisitionController@deleteRequisitionItem')->name('delete-stock-requisition-item');
Route::post('update-stock-requisition', 'Inventory\StockRequisitionController@updateRequisition')->name('update-stock-requisition');
Route::post('update-stock-requisition-details', 'Inventory\StockRequisitionController@updateRequisitionDetails')->name('update-stock-requisition-details');

Route::get('stock-transfer-list', 'Inventory\StockRequisitionController@stockTransferList')->name('stock-transfer-list');
Route::get('stock-requisition-transfer', 'Inventory\StockRequisitionController@stockTransferForm')->name('stock-requisition-transfer');


Route::post('stock-requisition-document-upload', 'Inventory\StockRequisitionController@documentUpload')->name('stock-requisition-document-upload');

//Delegation process

Route::post('stock-requisition-bulk-approved', 'Inventory\StockRequisitionController@stockRequisitionBulkApproved')->name('stock-requisition-bulk-approved');
Route::post('go-to-sr-delegation-process', 'Inventory\StockRequisitionController@goToSRDelegationProcess')->name('go-to-sr-delegation-process');
Route::post('go-to-pr-delegation-process', 'Purchase\RequisitionController@goToPRDelegationProcess')->name('go-to-pr-delegation-process');
Route::post('go-to-cs-delegation-process', 'Purchase\CsController@goToCSDelegationProcess')->name('go-to-cs-delegation-process');
Route::post('go-to-po-delegation-process', 'Purchase\PoController@goToPODelegationProcess')->name('go-to-po-delegation-process');
Route::post('go-to-lc-delegation-process', 'Purchase\LC\PurchaseLC@goToLCDelegationProcess')->name('go-to-lc-delegation-process');
Route::post('go-to-sales-order-delegation-process', 'Sales\SalesController@goToSODelegationProcess')->name('go-to-sales-order-delegation-process');
Route::post('go-to-price-list-delegation-process', 'Sales\PriceListController@goToPriceListDelegationProcess')->name('go-to-price-list-delegation-process');
Route::post('go-to-hr-delegation-process', 'HrIncrementPromotion\HrIncrementController@goToHRDelegationProcess')->name('go-to-hr-delegation-process');
Route::post('hr-salary-bulk-approved', 'HrIncrementPromotion\HrIncrementController@HRsalaryBulkApproved')->name('hr-salary-bulk-approved');
Route::post('go-to-leave-delegation-process', 'HR\LeaveManager@goToLeaveDelegationProcess')->name('go-to-leave-delegation-process');

//Sales
Route::post('get-customers', 'Sales\SalesController@getCustomers')->name('get-customers');
Route::post('get-saleable-product-list', 'Sales\SalesController@getSaleableProduct')->name('get-saleable-product-list');
Route::match(['get', 'post'],'sales-orders', 'Sales\SalesController@salesOrderList')->name('sales-orders');
Route::match(['get', 'post'],'create-sales-order/{socode?}', 'Sales\SalesController@salesOrderCreate')->name('create-sales-order');
Route::post('save-sales-order', 'Sales\SalesController@saveSalesOrder')->name('save-sales-order');
Route::post('get-sales-order-details-product', 'Sales\SalesController@getSalesOrderDetailsProduct')->name('get-sales-order-details-product');
Route::post('update-sales-order-details', 'Sales\SalesController@updateSalesOrderDetails')->name('update-sales-order-details');
Route::post('delete-sales-order', 'Sales\SalesController@deleteSalesOrder');
Route::post('delete-sales-item', 'Sales\SalesController@deleteSalesItem')->name('delete-sales-item');
Route::post('update-sales-order', 'Sales\SalesController@updateSalesOrder')->name('update-sales-order');
Route::post('update-sales-order-value', 'Sales\SalesController@updateSalesOrderValue')->name('update-sales-order-value');

//Route::post('support-document-upload', 'Sales\SalesController@supportDocumentUpload')->name('support-document-upload');
//Route::post('get-supporting-files', 'Sales\SalesController@getSupportingFiles')->name('get-supporting-files');
//Route::post('delete-attachments-item', 'Sales\SalesController@deleteAttachmentsItem')->name('delete-attachments-item');
Route::get('sales-order-approval-list', 'Sales\SalesController@salesOrderApprovalList')->name('sales-order-approval-list');
Route::post('sales-order-approval', 'Sales\SalesController@salesOrderApproval')->name('sales-order-approval');


Route::get('sales-order', 'Sales\SalesController@delete-requisition-item')->name('sales-order');

//Route::match(['get', 'post'],'challan-list', 'Sales\ChalanController@chalanList');
Route::get('sales-order-details/{id?}/{type?}', 'Sales\SalesOrderDetailsController@SalesOrderDetails');


//Route::match(['get', 'post'],'challan-list', 'Sales\ChalanController@chalanList');
//Route::get('sales-order-details/{id?}', 'Sales\SalesOrderDetailsController@SalesOrderDetails');
//Route::get('sales-order-details/{id?}', 'Sales\SalesOrderDetailsController@SalesOrderDetails');
Route::get('sales-order-pdf/{id?}', 'Sales\SalesOrderDetailsController@makeSalesOrderPDF');



/*======================== PRICE LIST ROUTES =============================*/
//Route::match(['get', 'post'], 'price-list', 'Sales\PriceListController@list')->name('price-list');
Route::match(['get', 'post'],'price-list', 'Sales\PriceListController@priceList')->name('price-list');
Route::get('price-list-create/{id?}', 'Sales\PriceListController@createPriceList')->name('price-list-create');
Route::post('price-list-store', 'Sales\PriceListController@storePriceList')->name('price-list-store');
Route::post('price-list-update/{id}', 'Sales\PriceListController@updatePriceList')->name('price-list-update');
Route::match(['get', 'post'], 'price-list-details/{id}/{type?}', 'Sales\PriceListController@viewPriceList')->name('price-list-details');

Route::get('price-approval-list', 'Sales\PriceListController@priceListApprovalList')->name('price-approval-list');
Route::post('pl-approval-decline', 'Sales\PriceListController@plApprovalDecline');


Route::get('test-pdf-download', 'TestController@testPdfDownload');
//Sales and Gate Pass By Khairul
Route::match(['get','post'],'create-gate-pass/{reference}/{reference_id}/{type}','GatePass\GatePassController@createGatePass')->name('create-gate-pass');
Route::get('pdf-download/{basic}/{challan}/{type?}', 'GatePass\GatePassController@pdfDownload');

/*======================== CHALLAN ROUTES =============================*/
Route::match(['get', 'post'],'challan-list/{mode?}', 'Sales\ChallanController@challanList')->name('challan-list');
Route::get('challan-details', 'Sales\ChallanController@challanDetails')->name('challan-details');
Route::match(['get','post'],'challan-process/{reference}/{reference_id}','Sales\ChallanController@challanProcess')->name('challan-process');
Route::get('challan-create/{id?}', 'Sales\ChallanController@editChallan')->name('challan-create');
Route::post('challan-store', 'Sales\ChallanController@challanStore')->name('challan-store');
Route::get('make-challan-pdf/{id?}/{ref?}', 'Sales\ChallanController@makeChallanPDF')->name('make-challan-pdf');
Route::post('ajax-allocate-product-item', 'Sales\ChallanController@allocateProduct')->name('ajax-allocate-product-item');
Route::post('ajax-get-available-qty', 'Sales\ChallanController@availableProductQty')->name('ajax-get-available-qty');

//Attachment
Route::post('support-document-upload', 'Attachment\AttachmentController@supportDocumentUpload')->name('support-document-upload');
Route::post('get-supporting-files', 'Attachment\AttachmentController@getSupportingFiles')->name('get-supporting-files');
Route::post('delete-attachments-item', 'Attachment\AttachmentController@deleteAttachmentsItem')->name('delete-attachments-item');

//get-purchasewise-cs-status
Route::post('get-purchasewise-cs-status', 'Purchase\RequisitionController@getPurchasewiseCSStatus')->name('get-purchasewise-cs-status');



/*======================== ACCOUNTS =============================*/

//CHART OF ACCOUNTS
Route::match(['get', 'post'],'chart-of-accounts/{head_id?}/{selected_node_id?}', 'Accounts\Chart_of_accounts\ChartOfAccountsController@chartOfAccounts')->name('chart-of-accounts');
Route::post('get-account-head', 'Accounts\Chart_of_accounts\ChartOfAccountsController@getAccountHead');
Route::post('save-chart-of-account-head-details','Accounts\Chart_of_accounts\ChartOfAccountsController@saveChartOfAccountHeadDetails')->name('save-chart-of-account-head-details');
Route::post('delete-account-head','Accounts\Chart_of_accounts\ChartOfAccountsController@deleteAccountHead')->name('delete-account-head');

//JOURNAL
Route::get('journal-entry/{voucher_no?}','Accounts\Journal_entry\JournalEntryController@journalEntry')->name('journal-entry');
Route::match(['get', 'post'],'journal-entry-day-close', 'Accounts\Journal_entry\JournalEntryController@journalEntryDayClose')->name('journal-entry-day-close');
Route::post('temporary-journal-save', 'Accounts\Journal_entry\JournalEntryController@temporaryJournalSave')->name('temporary-journal-save');
Route::post('temporary-journal-remove', 'Accounts\Journal_entry\JournalEntryController@removeJournalFromTemp')->name('temporary-journal-remove');
Route::post('journal-edit', 'Accounts\Journal_entry\JournalEntryController@journalEdit')->name('journal-edit');
Route::post('get-tagged-account', 'Accounts\Journal_entry\JournalEntryController@getTaggedAccount')->name('get-tagged-account');
Route::post('journal-final-submit', 'Accounts\Journal_entry\JournalEntryController@journalFinalSubmit')->name('journal-final-submit');
Route::match(['get', 'post'], 'get-voucher-list/{type?}', 'Accounts\Journal_entry\JournalEntryController@getVoucherList')->name('get-voucher-list');


Route::get('journal-entry-single/{type?}/{voucher_no?}','Accounts\Journal_entry\JournalEntryController@journalEntrySingle')->name('journal-entry-single');

//PLBS SETTING
Route::match(['get', 'post'],'plbs-settings/{id?}', 'Accounts\Plbs_settings\PlbsSettingsController@plbsSettings')->name('plbs-settings');
Route::post('get-plbs-account-head', 'Accounts\Plbs_settings\PlbsSettingsController@getPlbsAccountHead');
Route::post('is-plbs-item-code-exists', 'Accounts\Plbs_settings\PlbsSettingsController@isItemCodeExists');
Route::post('delete-plbs-account-head', 'Accounts\Plbs_settings\PlbsSettingsController@deletePlbsAccountHead');

//Report
Route::match(['get', 'post'],'journal-details-report/{id?}', 'Accounts\Accounts_report\AccountsReportController@journalDetailsReport')->name('journal-details-report');
Route::match(['get', 'post'], 'voucher-list', 'Accounts\Accounts_report\AccountsReportController@getVoucherList')->name('voucher-list');

Route::match(['get', 'post'], 'pl-bs-report', 'Accounts\Accounts_report\AccountsReportController@plbsGenerate')->name('pl-bs-report');
Route::post('show-plbs-report', 'Accounts\Accounts_report\AccountsReportController@showPlbsReport')->name('show-plbs-report');
Route::get('reference-details/{statemenmt_type?}/{query?}','Accounts\Accounts_report\AccountsReportController@referenceDetails')->name('reference-details');

Route::post('pl-bs-pdf-report','Accounts\Accounts_report\AccountsReportController@plbsPdfReport')->name('pl-bs-pdf-report');
Route::post('pl-bs-excel-report','Accounts\Accounts_report\AccountsReportController@plbsExcelReport')->name('pl-bs-excel-report');



// Autovoucher process testing
Route::match(['get', 'post'],'autovoucher-call/{id?}', 'Accounts\Plbs_settings\PlbsSettingsController@autoVoucherCall')->name('autovoucher-call');



//Cheque book Manager
Route::get('cheque-leaf-entry/{id?}', 'Accounts\ChequeBookManager\LeafEntryController@leafEntry');
Route::get('cheque-leaf-list', 'Accounts\ChequeBookManager\LeafEntryController@chequeBookLeafList');
Route::get('cheque-leaf-details/{id}', 'Accounts\ChequeBookManager\LeafEntryController@chequeLeafDetails');
Route::post('cheque-leaf-delete', 'Accounts\ChequeBookManager\LeafEntryController@chequeLeafDelete');
Route::post('cheque-leaf-entry-submit', 'Accounts\ChequeBookManager\LeafEntryController@chequeLeafEntrySubmit');
Route::post('get-company-book-number', 'Accounts\ChequeBookManager\LeafEntryController@getCompanyBookNumber');


//Cheque Management
Route::get('out-bound-cheque-entry/{id?}', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@outBoundChequeEntry');
Route::post('get-cheque-info-for-outgoing-cheque', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@getChequeInfoForOutgoingCheque');
Route::post('get-reftypes_info', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@getReftypesInfo');
Route::post('add-to-outbound-cheque-cart', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@addToOutboundChequeCart');
Route::post('remove-from-outbound-cheque-cart', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@removeFromOutboundChequeCart');
Route::post('outgoing-cheque-submit', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@outgoingChequeSubmit');
Route::post('delete-outgoing-cheque', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@deleteOutgoingCheque');
Route::post('get-outbound-cheque-tags', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@getOutboundChequeTags');
Route::get('out-bound-cheque-list', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@outBoundChequeList');
Route::get('outbound-cheque-details/{id}', 'Accounts\ChequeManagement\OutboundCheque\OutboundChequeController@outboundChequeDetails');



/*======================== END ACCOUNTS =============================*/

// dynamic drop down

Route::get('dropdown-index', 'Dropdown_grid\Dropdown_grid@dropdown_index')->name('dropdown_index');
Route::post('dropdown-grid-show', 'Dropdown_grid\Dropdown_grid@dropdown_grid_view')->name('dropdown-grid-show');
Route::post('get-dropdown-grid-data', 'Dropdown_grid\Dropdown_grid@get_dropdown_grid_ajax_data')->name('get-dropdown-grid-data');

/*======================== HR =============================*/
Route::get('emp-category-entry/{id?}', 'HrEmpCategory\HrEmpCategoryController@create')->name('emp-category-entry');
Route::any('emp-category-store/{id?}/', 'HrEmpCategory\HrEmpCategoryController@store')->name('emp-category-store');

/*=========== Shift manager ===========*/
Route::get('shift-change/{shift_id?}', 'HR\ShiftManager@shiftChange')->name('shift-change');
Route::post('save-shift-info', 'HR\ShiftManager@saveShiftInfo')->name('save-shift-info');
Route::post('shift-submit', 'HR\ShiftManager@shiftApprovalSubmit')->name('shift-submit');
Route::get('shift-approval', 'HR\ShiftManager@shiftApproval')->name('shift-approval');
/*=========== //Shift manager ===========*/
/*=========== leave manager ===========*/
Route::get('leave-entry/{data?}', 'HR\LeaveManager@leaveForm')->name('leave-entry');
Route::get('get-emp-info/{user_code?}/{user_id?}', 'HR\LeaveManager@getEmployeeInfo')->name('get-emp-info');
Route::get('get-emp-leave-info/{user_id?}', 'HR\LeaveManager@getEmployeeLeaveInfo')->name('get-emp-leave-info');
Route::get('get-emp-leave-history/{user_id?}', 'HR\LeaveManager@getEmployeeLeaveHistory')->name('get-emp-leave-history');

Route::any('emp-leave-list', 'HR\LeaveManager@employeeLeaveList')->name('emp-leave-list');
Route::any('save-leave-info', 'HR\LeaveManager@saveLeaveInfo')->name('save-leave-info');
Route::post('hr-leave-record-delete', 'HR\LeaveManager@deleteLeaveRecord')->name('hr-leave-record-delete');
Route::get('hr-leave-approve-list', 'HR\LeaveManager@leaveApprovalList')->name('hr-leave-approve-list');
Route::post('hr-leave-bulk-approved', 'HR\LeaveManager@HRLeaveBulkApproved')->name('hr-leave-bulk-approved');
Route::post('hr-leave-bulk-decline', 'HR\LeaveManager@HRLeaveBulkDecline')->name('hr-leave-bulk-decline');

/*=========== //leave manager ===========*/


/*=========== Employee manager ===========*/
Route::match(['get', 'post'],'employee', 'HR\EmployeeManager@List')->name('employee-list');
Route::get('employee/{id}', 'HR\EmployeeManager@show')->name('employee.show');
Route::get('employee-entry/{id?}', 'HR\EmployeeManager@basicForm')->name('employee-entry');

Route::post('get-district-name-bangla/{id}', 'HR\EmployeeManager@getDistrictNameBangla')->name('get-district-name-bangla');

Route::post('get-edu-form', 'HR\EmployeeManager@getEduForm')->name('get-edu-form');
Route::post('get-degree-list', 'HR\EmployeeManager@getDegreeList')->name('get-degree-list');

Route::post('submit-education-info/{id?}', 'HR\EmployeeManager@storeEducationInfo')->name('submit-education-info');
Route::post('delete-education-info', 'HR\EmployeeManager@destroyEducationInfo')->name('delete-education-info');

Route::post('get-acc-form', 'HR\EmployeeManager@getAccForm')->name('get-acc-form');
Route::post('submit-account-info/{id?}', 'HR\EmployeeManager@storeAccountInfo')->name('submit-account-info');
Route::post('delete-acc-info', 'HR\EmployeeManager@destroyAccInfo')->name('delete-acc-info');

Route::post('get-emp-form', 'HR\EmployeeManager@getEmpForm')->name('get-emp-form');


Route::post('get-branch-list/{id}', 'HR\EmployeeManager@getBranchList')->name('get-branch-list');
Route::post('submit-employment-info/{id?}', 'HR\EmployeeManager@storeEmploymentInfo')->name('submit-employment-info');
Route::post('delete-emp-info', 'HR\EmployeeManager@destroyEmpInfo')->name('delete-emp-info');


Route::post('get-provision-period', 'HR\EmployeeManager@getProvisionPeriod')->name('get-provision-period');
Route::post('store-personal-info', 'HR\EmployeeManager@storePersonalInfo')->name('store-personal-info');

Route::post('get-grade-info', 'HR\EmployeeManager@getGradeInfo')->name('get-grade-info');
Route::post('store-official-info', 'HR\EmployeeManager@storeOfficialInfo')->name('store-official-info');
Route::post('store-salary-info', 'HR\EmployeeManager@storeSalaryInfo')->name('store-salary-info');
Route::post('hr-working-shift-time', 'HR\EmployeeManager@workingShiftTime')->name('hr-working-shift-time');
Route::post('get-emp-inc-pro-form', 'HR\EmployeeManager@incrementPromotionForm')->name('get-emp-inc-pro-form');
Route::post('hr-store-inc-pro', 'HR\EmployeeManager@incrementPromotionStore')->name('hr-store-inc-pro');
Route::post('hr-inc-pro-history', 'HR\EmployeeManager@incrementPromotionHistory')->name('hr-inc-pro-history');

Route::post('hr-transfer-history', 'HR\EmployeeManager@transferHistory')->name('hr-transfer-history');
Route::post('get-emp-transfer-form', 'HR\EmployeeManager@transferForm')->name('get-emp-transfer-form');
Route::post('hr-store-transfer', 'HR\EmployeeManager@transferStore')->name('hr-store-transfer');

Route::post('get-emp-leave-form', 'HR\EmployeeManager@leaveForm')->name('get-emp-leave-form');
/*=========== //Employee manager ===========*/


/*======================== //HR =============================*/

/********HR Increment & Promotion********/

Route::match(['get', 'post'],'hr-increment', 'HrIncrementPromotion\HrIncrementController@increment')->name('hr_increment');
Route::match(['get', 'post'],'hr-new-increment', 'HrIncrementPromotion\HrIncrementController@incrementForm')->name('hr-new-increment');
Route::get('get-hr-increment-letter/{id}', 'HrIncrementPromotion\HrIncrementController@incrementLetter')->name('get-hr-increment-letter');
Route::post('hr-get-selected-increment-emp', 'HrIncrementPromotion\HrIncrementController@incrementEmployees')->name('hr-get-selected-increment-emp');
Route::post('hr-store-selected-emp-increment', 'HrIncrementPromotion\HrIncrementController@storeIncrement')->name('hr-store-selected-emp-increment');
Route::post('hr-record-delete', 'HrIncrementPromotion\HrIncrementController@deleteHrRecord')->name('hr-record-delete');
Route::post('hr-get-selected-increment-emp-edit', 'HrIncrementPromotion\HrIncrementController@editIncrementRecord')->name('hr-get-selected-increment-emp-edit');
Route::post('hr-update-selected-emp-increment', 'HrIncrementPromotion\HrIncrementController@updateIncrement')->name('hr-update-selected-emp-increment');

Route::match(['get', 'post'],'hr-promotion', 'HrIncrementPromotion\HrPromotionController@promotion')->name('hr-promotion');
Route::match(['get', 'post'],'hr-new-promotion', 'HrIncrementPromotion\HrPromotionController@promotionForm')->name('hr-new-promotion');
Route::get('get-hr-promotion-letter/{id}', 'HrIncrementPromotion\HrPromotionController@promotionLetter')->name('get-hr-promotion-letter');
Route::post('hr-get-selected-promotion-emp', 'HrIncrementPromotion\HrPromotionController@promotionEmployees')->name('hr-get-selected-promotion-emp');
Route::post('hr-store-selected-emp-promotion', 'HrIncrementPromotion\HrPromotionController@storePromotion')->name('hr-store-selected-emp-promotion');
Route::get('get-hr-grade-wise-salary/{id}', 'HrIncrementPromotion\HrPromotionController@gradeSalary')->name('get-hr-grade-wise-salary');
Route::post('hr-get-selected-promotion-emp-edit', 'HrIncrementPromotion\HrPromotionController@editPromotionRecord')->name('hr-get-selected-promotion-emp-edit');
Route::post('hr-update-selected-emp-promotion', 'HrIncrementPromotion\HrPromotionController@updatePromotion')->name('hr-update-selected-emp-promotion');

Route::get('hr-increment-promotion-log', 'HrIncrementPromotion\HrIncrementController@incrementPromotionLog')->name('hr-increment-promotion-log');
Route::get('get-hr-emp-history/{id}', 'HrIncrementPromotion\HrIncrementController@incrementPromotionLogView')->name('get-hr-emp-history');

Route::get('hr-salary-grade-config', 'HrIncrementPromotion\HrPromotionController@salaryGradeConfigForm')->name('hr-salary-grade-config');
Route::post('hr-salary-config-save', 'HrIncrementPromotion\HrPromotionController@salaryGradeConfigSave')->name('hr-salary-config-save');
Route::get('hr-salary-approve-list', 'HrIncrementPromotion\HrIncrementController@salaryApprovalList')->name('hr-salary-approve-list');

/***************HR Transfer***************/
Route::match(['get', 'post'],'hr-transfer', 'HrTransfer\HrTransferController@transfer')->name('hr-transfer');
Route::match(['get', 'post'],'hr-new-transfer', 'HrTransfer\HrTransferController@transferForm')->name('hr-new-transfer');
Route::post('hr-get-selected-transfer-emp', 'HrTransfer\HrTransferController@transferEmployees')->name('hr-get-selected-transfer-emp');
Route::post('hr-store-selected-emp-transfer', 'HrTransfer\HrTransferController@storeTransfer')->name('hr-store-selected-emp-transfer');
Route::post('hr-get-selected-transfer-emp-edit', 'HrTransfer\HrTransferController@editTransferRecord')->name('hr-get-selected-transfer-emp-edit');
Route::post('hr-update-selected-emp-transfer', 'HrTransfer\HrTransferController@updateTransfer')->name('hr-update-selected-emp-transfer');
Route::get('get-hr-transfer-letter/{id}', 'HrTransfer\HrTransferController@transferLetter')->name('get-hr-transfer-letter');
Route::get('hr-transfer-approve-list', 'HrTransfer\HrTransferController@transferApprovalList')->name('hr-transfer-approve-list');
Route::post('hr-transfer-bulk-approved', 'HrTransfer\HrTransferController@HRTransferBulkApproved')->name('hr-transfer-bulk-approved');


/*=========== Hr Payroll ===========*/
Route::get('salary-month-config/{id?}', 'HrPayroll\HrPayrollController@salaryMonthConfig')->name('salary-month-config');
Route::post('hr-employee-salary-month-info-save', 'HrPayroll\HrPayrollController@hrEmployeeSalaryMonthInfoSave')->name('hr-employee-salary-month-info-save');
Route::get('hr-employee-salary-month-config-list/{id?}', 'HrPayroll\HrPayrollController@hrEmployeeSalaryMonthConfigurationList')->name('hr-employee-salary-month-config-list');
Route::get('delete-hr-employee-salary-month-details-info/{id?}', 'HrPayroll\HrPayrollController@deleteHrEmployeeSalaryMonthDetailsInfo')->name('delete-hr-employee-salary-month-details-info');
Route::get('show-hr-employee-salary-month-details-info/{id?}', 'HrPayroll\HrPayrollController@ShowHrEmployeeSalaryMonthDetailsInfo')->name('show-hr-employee-salary-month-details-info');
Route::post('hr-employee-montly-holiday-check', 'HrPayroll\HrPayrollController@hrEmployeeMontlyHolidayCheck')->name('hr-employee-montly-holiday-check');
Route::get('attendance-bulk-upload', 'HrPayroll\HrPayrollController@attendanceBulkUpload')->name('attendance-bulk-upload');
Route::match(['get', 'post'],'attendance-final-process', 'HrPayroll\HrPayrollController@attendanceFinalProcess')->name('attendance-final-process');
Route::post('preview-attendance-history', 'HrPayroll\HrPayrollController@previewAttendanceHistory')->name('preview-attendance-history');
Route::post('delete-hr-emp-attendance', 'HrPayroll\HrPayrollController@deleteHrEmployeeAttendanceInfo')->name('delete-hr-emp-attendance');
Route::post('attendance-process', 'HrPayroll\HrPayrollController@HrEmployeeAttendanceProcess')->name('attendance-process');
Route::post('check-previous-attendance', 'HrPayroll\HrPayrollController@checkPreviousEmployeeAttendance')->name('check-previous-attendance');
Route::get('approved-attendance-list', 'HrPayroll\HrPayrollController@approvedAttendanceHistory')->name('approved-attendance-list');
Route::get('show-hr-employee-attendance-details-info/{code?}', 'HrPayroll\HrPayrollController@showHrEmployeeAttendanceDetails')->name('show-hr-employee-attendance-details-info');





/*******HR Payroll Salary & wages*********/
Route::match(['get', 'post'],'hr-salary-wages-emp-list', 'HrPayroll\HrSalaryWagesController@monthlyEmployeeList')->name('hr-salary-wages-emp-list');
Route::post('get-hr-emp-salary-info', 'HrPayroll\HrSalaryWagesController@monthlyEmployeeSalaryInfo')->name('get-hr-emp-salary-info');
Route::post('hr-emp-salary-update', 'HrPayroll\HrSalaryWagesController@monthlyEmployeeSalaryUpdate')->name('hr-emp-salary-update');

