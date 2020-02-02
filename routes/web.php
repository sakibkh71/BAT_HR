<?php
Route::fallback(function(){
    return response()->view('errors.404');
});
/*==========================================Testing routes for nirjhar mandal ======================================================*/
/*==========================================Testing routes for nirjhar======================================================*/
Route::get('ddd', 'ModuleController@getModuleList');
/*==========================================Testing routes for nirjhar======================================================*/
/*==========================================Testing routes for nirjhar======================================================*/
Route::get('no-permission', 'HomeController@noPermission')->name('no-permission');
Route::get('documentation', 'Documentation@index')->name('documentation');
Route::get('my-dashboard', 'DashboardController@getMyDashboard');


//====notifications
Route::get('redirect_to_notification_route','HomeController@redirectToNotificationRoute')->name('redirect_to_notification_route');
Route::get('see-all-notifications','HomeController@seeAllNotifications')->name('see-all-notifications');
Route::get('redirect-to-single-notification','HomeController@redirectToSingleNotification')->name('redirect-to-single-notification');

/*================================================================================================*/
Route::get('home', 'DashboardController@index')->name('home');
Route::get('dashboard', 'DashboardController@getDashboard');
Route::get('my-dashboard', 'DashboardController@getMyDashboard');
Route::get('new-join', 'DashboardController@getNewJoin');
Route::get('pfp-target', 'DashboardController@pfpTarget');
Route::get('/kpi-achievement-current-month', 'DashboardController@kpiAchievementCurrentMonth');
Route::get('/new-insurance','DashboardController@getNewInsurance');
Route::get('dashboard-last-month-salary','DashboardController@getLastMonthSalary');
Route::get('dashboard-last-month-pf','DashboardController@getLastMonthPF');

Route::any('dashboard-setposition/{userid?}/{moduleid?}', 'DashboardController@DashboardSetPosition')->name('dashboard-setposition');
Route::any('dashboard-setwidget/{userid?}/{moduleid?}', 'DashboardController@DashboardSetMyWidgets')->name('dashboard-setposition');
Route::post('dashboard-fetch-list', 'DashboardController@fetchList')->name('dashboard-fetch-list');
Route::post('dashboard-fetch-summary', 'DashboardController@fetchSummary')->name('dashboard-fetch-summary');
Route::post('dashboard-fetch-pie', 'DashboardController@fetchPie')->name('dashboard-fetch-pie');
Route::post('dashboard-fetch-c3', 'DashboardController@fetchC3')->name('dashboard-fetch-c3');


Route::post('dashboard-fetch-designations', 'DashboardController@fetchDesignations')->name('dashboard-fetch-designations');
Route::get('dashboard2', 'DashboardController@dashboard2')->name('dashboard2');
Route::post('dashboard-company-organogram', 'DashboardController@companyOrganogram')->name('dashboard-company-organogram');
Route::post('dashboard-company-pf', 'DashboardController@fetchCompanyPF')->name('dashboard-company-pf');
Route::post('ddashboard-salary-disburge', 'DashboardController@salaryDisburge')->name('dashboard-salary-disburge');

//============Daily Kpi Achievement====
Route::match(['get','post'],'daily-kpi-achievement/{date?}','Kpi\KpiReportController@dailyAchievement')->name('daily-kpi-achievement');

/*================================= SYSTEM ACCESS ==========================================*/
Route::get('/moduleChanger/{id}', 'ModuleController@moduleChanger')->name('moduleChanger');
/*================================= USER AUTHENTICATION ==========================================*/
Route::get('/', 'Auth\LoginController@showLoginForm')->name('login');
Auth::routes();
Route::post('multi-login-action', 'Auth\LoginController@multiLoginAction');
Route::post('notify-dismiss', 'User\UserController@notifyDismiss');

/*=================================== USER MANAGEMENT =========================================*/
Route::resource('users', 'User\UserController');
Route::post('getUserRaw', 'User\UserController@getUserRaw')->name('getUserRaw');
Route::post('profile', 'User\UserController@getUserProfile')->name('get-user-profile');
Route::match(['get', 'post'], 'user-list', 'User\UserController@List')->name('user-list');
Route::get('user-entry/{id?}', 'User\UserController@entryForm')->name('user-entry');
Route::match(['get', 'post'], 'store-user-info', 'User\UserController@storeUser')->name('store-user-info');
Route::post('user-info','User\UserController@getUserInfo')->name('user-info');

Route::match(['get', 'post'], 'user-employee-list', 'User\UserController@userEmployeeList')->name('user-employee-list');
Route::get('create-user-form-employee/{id?}', 'User\UserController@createUserEmployee')->name('create-user-form-employee');
Route::post('store-employee-data', 'User\UserController@storeUserEmployee')->name('store-employee-data');

/*================================ MENU MANAGER ========================================*/
Route::get('menu_list', 'MenuManagement@menu_list')->name('menu_list');
Route::get('menulist', 'MenuManagement@menuList')->name('menu_list');
Route::post('menu_entry', 'MenuManagement@menu_entry')->name('menu_entry');
Route::post('getMenuRaw', 'MenuManagement@getMenuRaw')->name('getMenuRaw');
Route::post('saveMenuOrder', 'MenuManagement@saveMenuOrder')->name('saveMenuOrder');
Route::post('menuDelete', 'MenuManagement@menuDelete')->name('menuDelete');
Route::get('get_menu_for_level', 'MenuManagement@get_menu_for_level')->name('get_menu_for_level');

/*================================ MENU USER MANUAL ========================================*/
Route::post('get-existing-manual-info', 'MenuUserManual@getExistingManualInfo')->name('get-existing-manual-info');
Route::post('update-manual-info', 'MenuUserManual@updateManualInfo')->name('update-manual-info');

/*============================ Customer Search ===================================*/
Route::get('implement', 'CustomSearch\CustomSearch@implement')->name('implement');
Route::post('implement-post', 'CustomSearch\CustomSearch@implementPost')->name('implement');
Route::post('session-search-filter', 'CustomSearch\CustomSearch@sessionSearchFilter')->name('implement');
Route::get('custom-search-autocomplete-query/{mode?}/{master_det_id?}/{id?}', 'CustomSearch\CustomSearch@getAutocompleteQuery');
/*=================================== Dynamic drop down =====================================*/
Route::get('dropdown-index', 'Dropdown_grid\Dropdown_grid@dropdown_index')->name('dropdown_index');
Route::post('dropdown-grid-show', 'Dropdown_grid\Dropdown_grid@dropdown_grid_view')->name('dropdown-grid-show');
Route::post('get-dropdown-grid-data', 'Dropdown_grid\Dropdown_grid@get_dropdown_grid_ajax_data')->name('get-dropdown-grid-data');

/*===============================User Profile========================*/
Route::get('profile', 'User\UserController@getUserProfiles')->name('getUserProfile');
Route::post('update-user-profile', 'User\UserController@updateUserProfile')->name('update-user-profile');
Route::get('reset_password', 'User\UserController@resetUserPassword')->name('resetPassword');
Route::post('reset-password-submit', 'User\UserController@resetPasswordSubmit');
Route::post('update-company-logo', 'User\UserController@updateCompanyLogo')->name('update-company-logo');


/*=================Delegation Process=================*/
Route::get('get-approval-modules', 'Delegation\MyApprovalList@index')->name('approvalModules');
Route::post('seen-approval-notification', 'Delegation\MyApprovalList@seenApprovalNotification')->name('seen-approval-notification');
Route::get('get-delegation-list', 'Delegation\MyApprovalList@getDelegationList')->name('get-delegation-list');
Route::match(['get', 'post'], 'get-delegation-list', 'Delegation\MyApprovalList@getDelegationList')->name('get-delegation-list');




/*=============================================MASTER ENTRY====================================================*/
Route::get('grid/{gridname}', 'Master\MasterGridController@getGrid')->name('grid');
Route::match(['get','post'],'get-grid-data/{type?}', 'Master\MasterGridController@getGridData')->name('get-grid-data');
Route::post('delete-record', 'Master\MasterGridController@deleteRecord');
Route::get('form/{formname}', 'Master\MasterFormController@buildMasterForm')->name('form');
Route::get('entryform/{formname}/{table_name?}/{primary_key_field?}/{id?}', 'Master\MasterFormController@buildFormForEntry')->name('entryform');
Route::post('masterFormDataStore', 'Master\MasterFormController@masterFormDataStore')->name('masterFormDataStore');
Route::get('get-autocomplete-query/{mode?}/{master_det_id?}/{id?}', 'Master\MasterFormController@getAutocompleteQuery');
/*======================================================================================================================*/

/*============================== Delegation Process ===============================*/
Route::get('delegation-link-view-test/{code?}', 'Delegation\DelegationProcess@delegationLinkViewTest');
Route::any('delegation-initialize/{path?}', 'Delegation\DelegationProcess@delegationInitialize');
Route::post('delegation-approval-process', 'Delegation\DelegationProcess@delegationApprove');
Route::post('delegation-decline-process', 'Delegation\DelegationProcess@delegationDeclineProcess');
Route::get('waiting-for-approval/{slug}', 'Delegation\DelegationProcess@waitingForApproval');

Route::get('emp-category-entry/{id?}', 'HrEmpCategory\HrEmpCategoryController@create')->name('emp-category-entry');
Route::any('emp-category-store/{id?}/', 'HrEmpCategory\HrEmpCategoryController@store')->name('emp-category-store');

/*======================================= Working Shift manager =======================================*/
Route::match(['get','post'],'worker-shift-configure', 'HR\ShiftManager@shiftChange')->name('worker-shift-configure');
Route::post('worker-calendar-set-event', 'HR\ShiftManager@setCalendarShift')->name('worker-calendar-set-event');
Route::post('save-shift-info', 'HR\ShiftManager@saveShiftInfo')->name('save-shift-info');
Route::post('shift-submit', 'HR\ShiftManager@shiftApprovalSubmit')->name('shift-submit');
Route::get('shift-approval', 'HR\ShiftManager@shiftApproval')->name('shift-approval');
Route::match('get','shift-change-calendar/{month?}/{sys_users_id?}', 'HR\ShiftManager@calendarShiftChange')->name('shift-change-calendar');
Route::get('employee-calendar-get-event/{id?}', 'HR\ShiftManager@calendarEmployeeShiftData')->name('employee-calendar-get-event');

Route::match(['get','post'],'daily-duty-report/{type?}', 'HR\ShiftReport@dailyDutyReport')->name('daily-duty-report');
Route::match(['get','post'],'employee-wise-duty/{type?}', 'HR\ShiftReport@employeeWiseDuty')->name('employee-wise-duty');
Route::match(['get','post'],'shift-wise-duty/{type?}', 'HR\ShiftReport@shiftWiseDuty')->name('shift-wise-duty');

/*======================================= leave manager =======================================*/
Route::get('leave-entry/{data?}', 'HR\LeaveManager@leaveForm')->name('leave-entry');
Route::get('get-emp-info/{user_code?}/{user_id?}', 'HR\LeaveManager@getEmployeeInfo')->name('get-emp-info');
Route::get('get-emp-leave-info/{user_id?}', 'HR\LeaveManager@getEmployeeLeaveInfo')->name('get-emp-leave-info');
Route::get('get-emp-leave-type/{user_id?}', 'HR\LeaveManager@getEmployeeLeaveType')->name('get-emp-leave-type');
Route::post('get-emp-leave-total', 'HR\LeaveManager@getEmployeeLeaveTotal')->name('get-emp-leave-total');
Route::match(['get','post'],'get-emp-leave-history/{type?}', 'HR\LeaveManager@getEmployeeLeaveHistory')->name('get-emp-leave-history');
Route::post('check-pending-leave-exist', 'HR\LeaveManager@checkPendingLeaveExist')->name('check-pending-leave-exist');
Route::any('emp-leave-list', 'HR\LeaveManager@employeeLeaveList')->name('emp-leave-list');
Route::any('save-leave-info', 'HR\LeaveManager@saveLeaveInfo')->name('save-leave-info');
Route::post('hr-leave-record-delete', 'HR\LeaveManager@deleteLeaveRecord')->name('hr-leave-record-delete');
Route::post('hr-leave-record-cancel', 'HR\LeaveManager@cancelLeaveRecord')->name('hr-leave-record-cancel');
Route::get('hr-leave-approve-list', 'HR\LeaveManager@leaveApprovalList')->name('hr-leave-approve-list');
Route::post('hr-leave-bulk-approved', 'HR\LeaveManager@HRLeaveBulkApproved')->name('hr-leave-bulk-approved');
Route::post('hr-leave-bulk-decline', 'HR\LeaveManager@HRLeaveBulkDecline')->name('hr-leave-bulk-decline');

Route::get('leave-report', 'HR\LeaveReport@employeeLeaveReport')->name('leave-report');
Route::match(['get', 'post'],'get-all-employee-leave-report/{type?}', 'HR\LeaveReport@allEmployeeLeaveReport')->name('get-all-employee-leave-report');

Route::post('get-emp-yearly-earn-leave', 'HR\LeaveReport@earlyEarnLeaveReport')->name('get-emp-yearly-earn-leave');
Route::post('get-emp-monthly-leave-report', 'HR\LeaveReport@monthlyLeaveReport')->name('get-emp-monthly-leave-report');
Route::get('emp-monthly-leave-report-print/{user_id}/{month}', 'HR\LeaveReport@monthlyLeaveReportPrint')->name('emp-monthly-leave-report-print');
Route::get('emp-earn-leave-report-print/{user_id}', 'HR\LeaveReport@earnLeaveReportPrint')->name('emp-earn-leave-report-print');

Route::get('emp_leave_report_print/{user_id}','HR\LeaveReport@leaveReportPrint')->name('emp_leave_report_print');

Route::post('get-emp-leave-report','HR\LeaveReport@getLeaveReport')->name('get-emp-leave-report');

Route::match(['get','post'],'leave-encashment-create/{encashments_id?}', 'HR\LeaveManager@leaveEncashmentCreate')->name('leave-encashment-create');
Route::post('leave-encashment-store/{encashments_id?}', 'HR\LeaveManager@leaveEncashmentStore')->name('leave-encashment-store');
Route::match(['get','post'],'leave-encashment-history', 'HR\LeaveManager@leaveEncashmentHistory')->name('leave-encashment-history');
Route::match(['get','post'],'leave-policy-apply-for-new-year', 'HR\LeaveManager@leavePolicyApplyNewYear')->name('leave-policy-apply-for-new-year');
Route::match(['post'],'apply-leave-policy', 'HR\LeaveManager@applyLeavePolicy')->name('apply-leave-policy');

Route::match(['get','post'],'hr-emp-leave-report/{type?}', 'HR\LeaveManager@hrEmpLeaveReport')->name('hr-emp-leave-report');


/*================================ Employee INfo ===================================*/
Route::get('emargency-contract-list', 'HR\EmployeeInfo@emargencyContractList')->name('emargency-contract-list');
Route::get('emp-emargency-contract-list/{id}', 'HR\EmployeeInfo@empEmargencyContractList')->name('emp-emargency-contract-list');
Route::get('employee-probation-list', 'HR\EmployeeInfo@probationList')->name('employee-probation-list');
Route::get('emp-extend-probation-date/{id}', 'HR\EmployeeInfo@extendProbationDate')->name('emp-extend-probation-date');
Route::post('extend-probation-update', 'HR\EmployeeInfo@extendProbationUpdate')->name('extend-probation-update');
Route::post('employee-probation-to-active', 'HR\EmployeeInfo@probationToActive')->name('employee-probation-to-active');

/*================================ Employee manager ===================================*/
Route::get('find-route-for-ss-sr/{dp_id?}/{designation_id?}', 'HR\EmployeeManager@findRouteForSsSr')->name('find-route-for-ss-sr');
Route::match(['get', 'post'], 'employee', 'HR\EmployeeManager@List')->name('employee-list');
Route::post('employee/{id}', 'HR\EmployeeManager@show')->name('employee.show');
Route::post('employee-delete/{id}/{status?}', 'HR\EmployeeManager@deleteEmp')->name('employee.delete');
Route::get('employee-pdf/{id}', 'HR\EmployeeManager@employeePDF')->name('employee.pdf');
Route::match(['get', 'post'], 'employee-entry/{id?}/{tab?}', 'HR\EmployeeManager@basicForm')->name('employee-entry');
Route::match(['get', 'post'], 'employee/{id?}/{tab}/{mode}', 'HR\EmployeeManager@basicForm')->name('employee-view');
Route::post('get-district-name-bangla/{id}', 'HR\EmployeeManager@getDistrictNameBangla')->name('get-district-name-bangla');
Route::post('check-user-code', 'HR\EmployeeManager@checkUserCode')->name('check-user-code');
Route::post('check-email-exist', 'HR\EmployeeManager@checkEmailExist')->name('check-email-exist');
Route::post('get-insurance-form', 'HR\EmployeeManager@getInsuranceForm')->name('get-insurance-form');
Route::post('delete-insurance-info', 'HR\EmployeeManager@deleteInsuranceInfo')->name('delete-insurance-info');
Route::post('delete-emargency-contract-info', 'HR\EmployeeManager@deleteEmergencyContractInfo')->name('delete-emargency-contract-info');
Route::post('get-emargency-contract-form', 'HR\EmployeeManager@getEmargencyContractForm')->name('get-emargency-contract-form');
Route::post('get-edu-form', 'HR\EmployeeManager@getEduForm')->name('get-edu-form');
Route::post('get-degree-list', 'HR\EmployeeManager@getDegreeList')->name('get-degree-list');
Route::post('submit-education-info/{id?}', 'HR\EmployeeManager@storeEducationInfo')->name('submit-education-info');
Route::post('submit-insurance-info/{id?}', 'HR\EmployeeManager@storeInsuranceInfo')->name('submit-insurance-info');
Route::post('submit-emargency-contract-info/{id?}', 'HR\EmployeeManager@storeEmargencyContractInfo')->name('submit-emargency-contract-info');
Route::post('delete-education-info', 'HR\EmployeeManager@destroyEducationInfo')->name('delete-education-info');
Route::post('get-acc-form', 'HR\EmployeeManager@getAccForm')->name('get-acc-form');
Route::post('submit-account-info/{id?}', 'HR\EmployeeManager@storeAccountInfo')->name('submit-account-info');
Route::post('check-account-type', 'HR\EmployeeManager@checkAccountTypes')->name('check-account-type');
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
Route::post('get-emp-yearly-leave', 'HR\LeaveReport@yearlyLeaveReport')->name('get-emp-yearly-leave');
Route::get('hr-emp-yearly-leave-report/{id}/{year}', 'HR\LeaveReport@yearlyLeaveReportPrint')->name('hr-emp-yearly-leave-report');
Route::post('get-emp-monthly-atten', 'HR\EmployeeManager@monthlyAttendanceReport')->name('get-emp-monthly-atten');
Route::post('hr-get-nominee-form', 'HR\EmployeeManager@getNomineeForm')->name('hr-get-nominee-form');
Route::post('hr-submit-nominee-info', 'HR\EmployeeManager@storeNomineeInfo')->name('hr-submit-nominee-info');
Route::post('hr-delete-nominee-info', 'HR\EmployeeManager@destroyNomineeInfo')->name('hr-delete-nominee-info');
Route::match(['get', 'post'],'company-calendar-config', 'HR\CompanyCalender@calendarConfigure')->name('company-calendar-config');
Route::post('company-calendar-set-event', 'HR\CompanyCalender@calendarConfigureSetEvent')->name('company-calendar-set-event');
Route::get('company-calendar-get-event/{id?}/{month?}', 'HR\CompanyCalender@calendarConfigureGetEvent')->name('company-calendar-get-event');
Route::match(['get','post'],'emp-shift-calendar', 'HR\EmployeeManager@empShiftCalender')->name('emp-shift-calendar');

Route::match(['get', 'post'],'company-calendar-show', 'HR\CompanyCalender@calendarShow')->name('company-calendar-show');



//employee info excel

Route::get('employee-info-excel','HR\EmployeeManager@getEmployeeInfoExcel')->name('employee-info-excel');
Route::get('emargency-contact-excel','HR\EmployeeManager@emargencyContactExcel')->name('emargency-contact-excel');


//====bonus Policy
Route::match(['get','post'],'new_bonus_policy/{id?}','HrPayroll\HrEidBonusController@addBonusPolicy')->name('new_bonus_policy');
Route::match(['get','post'],'store-bonus-policy','HrPayroll\HrEidBonusController@storeBonusPolicy')->name('store-bonus-policy');
Route::match(['get','post'],'check-company-for-bonus-policy-eligibility','HrPayroll\HrEidBonusController@checkBonusEligibility')->name('check-company-for-bonus-policy-eligibility');

Route::get('delete-bonus-policy','HrPayroll\HrEidBonusController@deleteBonusPolicy')->name('delete-bonus-policy');
//==== PF Report====
Route::get('pf-policy-list','HR\BatCompany@pfPolicyList')->name('pf-policy-list');
Route::match(['get', 'post'], 'company-pf-policy-edit/{id?}', 'HR\BatCompany@editPfPolicy')->name('company-pf-policy-edit');
Route::get('update-company-pf-policy','HR\BatCompany@updateCompanyPFPolicy')->name('update-company-pf-policy');

//=====Company Organogram===
Route::get('company-organogram-list','HR\BatCompany@companyOrganogramList')->name('company-organogram-list');
Route::match(['get','post'],'company-organogram-edit/{id?}','HR\BatCompany@companyOrganogramEdit')->name('company-organogram-edit');
Route::get('update-company-organogram','HR\BatCOmpany@updateCompanyOrganogram')->name('update-company-organogram');


Route::post('working-shifts-by-roaster-type', 'HR\EmployeeManager@getWorkingShifts')->name('working-shifts-by-roaster-type');
Route::get('dp-by-dh/{id?}','HR\EmployeeManager@getDPbySH')->name('dp-by-dh');
Route::post('set-emp-variable-salary-form','HR\EmployeeManager@variableSalaryForm')->name('set-emp-variable-salary-form');
Route::post('store-variable-salary','HR\EmployeeManager@variableSalaryStore')->name('store-variable-salary');
Route::post('hr-vsalary-record-delete', 'HR\EmployeeManager@deleteVsalaryRecord')->name('hr-vsalary-record-delete');

Route::post('hr-separation-causes-store', 'HR\EmployeeManager@separationCausesStore')->name('hr-separation-causes-store');
Route::get('hr-upload-employee', 'HR\EmployeeManager@employeeUploadForm')->name('hr-upload-employee');
Route::post('employee-bulk-upload-store', 'HR\EmployeeManager@employeeUploadStore')->name('employee-bulk-upload-store');

Route::post('hr_submit_mfs_account','HR\EmployeeManager@addMfsAccount')->name('hr_submit_mfs_account');

Route::post('check-basic-employee-uniqueness','HR\EmployeeManager@checkBasicEmployeeUniqueness')->name('check-basic-employee-uniqueness');
/**employee separation**/

Route::get('separation-form-show','HR\EmployeeSeparation@separationFormShow')->name('separation-form-show');
Route::get('emp-leaver-list','HR\EmployeeSeparation@getSeparationList')->name('emp-leaver-list');
Route::get('separation-settlement-pdf/{separation_id?}','HR\EmployeeSeparation@getSeparationSettlement')->name('separation-settlement-pdf');
//Route::get('get-separation-form/{id?}/{date?}/{separation_id?}','HR\EmployeeSeparation@getSeparationForm')->name('get-separation-form');
Route::match(['get','post'],'get-separation-form/{id?}/{date?}/{separation_id?}','HR\EmployeeSeparation@getSeparationForm')->name('get-separation-form');
Route::post('emp-separation-confirm','HR\EmployeeSeparation@separationConfirm')->name('emp-separation-confirm');
Route::post('separated-salary-submit','HR\EmployeeSeparation@separationSubmit')->name('separated-salary-submit');
Route::get('separation-undo/{separation_id?}/{emp_id?}','HR\EmployeeSeparation@separationUndo')->name('separation-undo');

Route::get('see-company-staff-vacancy','HR\EmployeeSeparation@seeNotifications')->name('see-company-staff-vacancy');
Route::post('check-separated','HR\EmployeeSeparation@checkSeparated')->name('check-separated');
Route::post('separation-delegation-process','HR\EmployeeSeparation@separationDelegationProcess')->name('separation-delegation-process');
Route::get('hr-separation-approve-list','HR\EmployeeSeparation@separationApproveList')->name('hr-separation-approve-list');
Route::post('hr-separation-bulk-approved','HR\EmployeeSeparation@separationBulkApproved')->name('hr-separation-bulk-approved');




//============== user Notifications
Route::match(['get','post'],'get-user-all-notifications','HomeController@getUserNotifications')->name('get-user-all-notifications');

/*==============================HR Increment ===================================*/
Route::match(['get', 'post'], 'hr-increment', 'HrIncrementPromotion\HrIncrementController@increment')->name('hr_increment');
Route::match(['get', 'post'], 'hr-new-increment', 'HrIncrementPromotion\HrIncrementController@incrementForm')->name('hr-new-increment');
Route::get('get-hr-increment-letter/{id}', 'HrIncrementPromotion\HrIncrementController@incrementLetter')->name('get-hr-increment-letter');
Route::post('hr-get-selected-increment-emp', 'HrIncrementPromotion\HrIncrementController@incrementEmployees')->name('hr-get-selected-increment-emp');
Route::post('hr-store-selected-emp-increment', 'HrIncrementPromotion\HrIncrementController@storeIncrement')->name('hr-store-selected-emp-increment');
Route::post('hr-record-delete', 'HrIncrementPromotion\HrIncrementController@deleteHrRecord')->name('hr-record-delete');
Route::post('hr-get-selected-increment-emp-edit', 'HrIncrementPromotion\HrIncrementController@editIncrementRecord')->name('hr-get-selected-increment-emp-edit');
Route::post('hr-update-selected-emp-increment', 'HrIncrementPromotion\HrIncrementController@updateIncrement')->name('hr-update-selected-emp-increment');
Route::match(['get', 'post'],'hr-increment-promotion-report/{type?}', 'HrIncrementPromotion\HrIncrementController@incrementPromotionReport')->name('hr-increment-promotion-report');
Route::get('hr-salary-approve-list', 'HrIncrementPromotion\HrIncrementController@salaryApprovalList')->name('hr-salary-approve-list');
Route::post('hr-increment-list-export', 'HrIncrementPromotion\HrIncrementController@incrementListExport')->name('hr-increment-list-export');
Route::post('hr-increment-list-pdf', 'HrIncrementPromotion\HrIncrementController@incrementListPDF')->name('hr-increment-list-pdf');
Route::get('hr-increment-promotion-log', 'HrIncrementPromotion\HrIncrementController@incrementPromotionLog')->name('hr-increment-promotion-log');
Route::get('get-hr-emp-history/{id}', 'HrIncrementPromotion\HrIncrementController@incrementPromotionLogView')->name('get-hr-emp-history');

/*==============================HR  Promotion===================================*/
Route::match(['get', 'post'], 'hr-promotion', 'HrIncrementPromotion\HrPromotionController@promotion')->name('hr-promotion');
Route::match(['get', 'post'], 'hr-new-promotion', 'HrIncrementPromotion\HrPromotionController@promotionForm')->name('hr-new-promotion');
Route::post('hr-get-selected-promotion-emp', 'HrIncrementPromotion\HrPromotionController@promotionEmployees')->name('hr-get-selected-promotion-emp');
Route::post('hr-get-gross-by-grade', 'HrIncrementPromotion\HrPromotionController@grossbyGrade')->name('hr-get-gross-by-grade');
Route::post('hr-store-selected-emp-promotion', 'HrIncrementPromotion\HrPromotionController@storePromotion')->name('hr-store-selected-emp-promotion');
Route::post('hr-promotion-delete', 'HrIncrementPromotion\HrPromotionController@deletePromotionRecord')->name('hr-promotion-delete');

Route::get('get-hr-promotion-letter/{id}', 'HrIncrementPromotion\HrPromotionController@promotionLetter')->name('get-hr-promotion-letter');


Route::post('hr-get-selected-promotion-emp-edit', 'HrIncrementPromotion\HrPromotionController@editPromotionRecord')->name('hr-get-selected-promotion-emp-edit');
Route::post('hr-update-selected-emp-promotion', 'HrIncrementPromotion\HrPromotionController@updatePromotion')->name('hr-update-selected-emp-promotion');

Route::get('get-hr-grade-wise-salary/{id}', 'HrIncrementPromotion\HrPromotionController@gradeWiseSalary')->name('get-hr-grade-wise-salary');
Route::get('hr-salary-grade-config', 'HrIncrementPromotion\HrPromotionController@salaryGradeConfigForm')->name('hr-salary-grade-config');
Route::post('hr-salary-config-save', 'HrIncrementPromotion\HrPromotionController@salaryGradeConfigSave')->name('hr-salary-config-save');
Route::post('hr-promotion-list-pdf', 'HrIncrementPromotion\HrPromotionController@promotionListPDF')->name('hr-promotion-list-pdf');




/*====================================HR Transfer===========================*/
Route::match(['get', 'post'], 'hr-transfer', 'HrTransfer\HrTransferController@transfer')->name('hr-transfer');
Route::match(['get', 'post'], 'hr-new-transfer', 'HrTransfer\HrTransferController@transferForm')->name('hr-new-transfer');
Route::post('hr-get-selected-transfer-emp', 'HrTransfer\HrTransferController@transferEmployees')->name('hr-get-selected-transfer-emp');
Route::post('hr-store-selected-emp-transfer', 'HrTransfer\HrTransferController@storeTransfer')->name('hr-store-selected-emp-transfer');
Route::post('hr-get-selected-transfer-emp-edit', 'HrTransfer\HrTransferController@editTransferRecord')->name('hr-get-selected-transfer-emp-edit');
Route::post('hr-update-selected-emp-transfer', 'HrTransfer\HrTransferController@updateTransfer')->name('hr-update-selected-emp-transfer');
Route::get('get-hr-transfer-letter/{id}', 'HrTransfer\HrTransferController@transferLetter')->name('get-hr-transfer-letter');
Route::get('hr-transfer-approve-list', 'HrTransfer\HrTransferController@transferApprovalList')->name('hr-transfer-approve-list');
Route::post('hr-transfer-bulk-approved', 'HrTransfer\HrTransferController@HRTransferBulkApproved')->name('hr-transfer-bulk-approved');
Route::post('hr-transfer-list-pdf', 'HrTransfer\HrTransferController@HRTransferListPdf')->name('hr-transfer-list-pdf');

/*=========================== Hr Payroll ================================*/
Route::get('salary-month-config/{id?}', 'HrPayroll\HrPayrollController@salaryMonthConfig')->name('salary-month-config');
Route::post('days-company-calender', 'HrPayroll\HrPayrollController@getDays')->name('days-company-calender');
Route::post('hr-employee-salary-month-info-save', 'HrPayroll\HrPayrollController@hrEmployeeSalaryMonthInfoSave')->name('hr-employee-salary-month-info-save');
Route::get('hr-employee-salary-month-config-list/{id?}', 'HrPayroll\HrPayrollController@hrEmployeeSalaryMonthConfigurationList')->name('hr-employee-salary-month-config-list');
Route::get('delete-hr-employee-salary-month-details-info/{id?}', 'HrPayroll\HrPayrollController@deleteHrEmployeeSalaryMonthDetailsInfo')->name('delete-hr-employee-salary-month-details-info');
Route::get('show-hr-employee-salary-month-details-info/{id?}', 'HrPayroll\HrPayrollController@ShowHrEmployeeSalaryMonthDetailsInfo')->name('show-hr-employee-salary-month-details-info');
Route::post('hr-employee-montly-holiday-check', 'HrPayroll\HrPayrollController@hrEmployeeMontlyHolidayCheck')->name('hr-employee-montly-holiday-check');
Route::post('hr-monthly-working-days', 'HR\CompanyCalender@saveMonthlyWorkingDays')->name('hr-monthly-working-days');

/* ========================= HR FF Route Transfer =========================== */
Route::match(['get', 'post'],'route-list-with-ff/{dpid?}/{designation_id?}/{date?}', 'HR\RouteTransfer@routeListWithFF')->name('route-list-with-ff');
Route::post('empty-route', 'HR\RouteTransfer@emptyRoute')->name('empty-route');
Route::post('assign-route', 'HR\RouteTransfer@assignRoute')->name('assign-route');
Route::get('route-log', 'HR\RouteTransfer@routeLog')->name('route-log');

/*=========================== Hr Attendance ================================*/
//Route::match(['get', 'post'],'attendance-entry/{id?}', 'HrAttendance\HrAttendanceController@attendanceEntry')->name('attendance-entry');
Route::match(['get', 'post'],'attendance-entry', 'HrAttendance\HrAttendanceController@attendanceEntryFromList')->name('attendance-entry');
Route::post('assign-route-for-esr', 'HrAttendance\HrAttendanceController@assignRouteForEsr')->name('assign-route-for-esr');
Route::post('get-extra-sr-list', 'HrAttendance\HrAttendanceController@getExtraSrList')->name('get-extra-sr-list');
Route::get('emp-change-attendance/{id?}/{id1?}/{id2?}', 'HrAttendance\HrAttendanceController@empChangeAttendance')->name('emp-change-attendance');
Route::match(['get', 'post'], 'attendance-final-process', 'HrAttendance\HrAttendanceController@attendanceFinalProcess')->name('attendance-final-process');
Route::post('preview-attendance-history', 'HrAttendance\HrAttendanceController@previewAttendanceHistory')->name('preview-attendance-history');
Route::post('delete-hr-emp-attendance', 'HrAttendance\HrAttendanceController@deleteHrEmployeeAttendanceInfo')->name('delete-hr-emp-attendance');
Route::post('attendance-process', 'HrAttendance\HrAttendanceController@HrEmployeeAttendanceProcess')->name('attendance-process');
Route::post('attendance-manual-entry/{id?}', 'HrAttendance\HrAttendanceController@HrEmployeeAttendanceManualEntry')->name('attendance-manual-entry');
Route::post('check-previous-attendance', 'HrAttendance\HrAttendanceController@checkPreviousEmployeeAttendance')->name('check-previous-attendance');
Route::post('check-employee-attendance', 'HrAttendance\HrAttendanceController@checkEmployeeAttendance')->name('check-employee-attendance');
Route::post('get-employee-attendance-shift', 'HrAttendance\HrAttendanceController@getEmpShift')->name('get-employee-attendance-shift');
Route::match(['get', 'post'], 'approved-attendance-list', 'HrAttendance\HrAttendanceController@approvedAttendanceHistory')->name('approved-attendance-list');
Route::match(['get', 'post'], 'monthly-attendance-details', 'HrAttendance\HrAttendanceController@monthlyAttendanceDetails')->name('monthly-attendance-details');
Route::get('show-hr-employee-attendance-details-info/{code?}', 'HrAttendance\HrAttendanceController@showHrEmployeeAttendanceDetails')->name('show-hr-employee-attendance-details-info');
Route::post('get-hr-emp-attendance-details', 'HrAttendance\HrAttendanceController@getHrEmpAttendanceDetails');
Route::post('update-attendance-history', 'HrAttendance\HrAttendanceController@updateAttendanceHistory')->name('update-attendance-history');
Route::post('locked-selected-attendance-history', 'HrAttendance\HrAttendanceController@lockedSelectedAttendanceHistory');

Route::match(['get', 'post'], 'daily-attendance-sheet/{type?}', 'HrAttendance\HrAttendanceReportController@dailyAttendanceSheet')->name('daily-attendance-sheet');
Route::match(['get', 'post'], 'attendance-in-out-missing/{type?}', 'HrAttendance\HrAttendanceReportController@punchInOutReport')->name('attendance-in-out-missing');
Route::get('job-card', 'HrAttendance\HrAttendanceReportController@jobCard')->name('job-card');
Route::post('job-card-data/{type?}', 'HrAttendance\HrAttendanceReportController@jobCardData')->name('job-card-data');
Route::match(['get', 'post'],'monthly-attendance-sheet/{pdf?}', 'HrAttendance\HrAttendanceReportController@monthlyAttendanceSheet')->name('monthly-attendance-sheet');
Route::match(['get', 'post'],'punch-missing-report/{pdf?}', 'HrAttendance\HrAttendanceReportController@punchMissingReport')->name('punch-missing-report');

Route::match(['get', 'post'],'attendance-bulk-upload/{id?}', 'HrAttendance\HrAttendanceUploadController@bulkEntry')->name('attendance-bulk-upload');
Route::post('attendance-bulk-upload-store', 'HrAttendance\HrAttendanceUploadController@storeBulkAttendanceData')->name('attendance-bulk-upload-store');

Route::match(['get', 'post'],'attendance-manual-upload/{id?}', 'HrAttendance\HrAttendanceUploadController@manualEntry')->name('attendance-manual-upload');
Route::post('attendance-manual-upload-store', 'HrAttendance\HrAttendanceUploadController@storeManualAttendanceData')->name('attendance-manual-upload-store');
Route::post('get-temp-manual-attendance', 'HrAttendance\HrAttendanceUploadController@getTempManualAttendance')->name('get-temp-manual-attendance');
Route::post('sync-manual-attendance', 'HrAttendance\HrAttendanceUploadController@syncManualAttendance')->name('sync-manual-attendance');


/*================= HR Payroll Salary & wages =====================*/
Route::match(['get', 'post'],'hr-create-new-salary-sheet/{id?}','HrPayroll\HrSalaryWagesController@monthlySalarySheetCreate')->name('hr-create-new-salary-sheet');
Route::match(['get', 'post'], 'hr-salary-sheet', 'HrPayroll\HrSalaryWagesController@monthlySalarySheet')->name('hr-salary-sheet');

Route::match(['get', 'post'], 'hr-salary-wages-emp-list/{id?}/{type?}', 'HrPayroll\HrSalaryWagesController@monthlyEmployeeList')->name('hr-salary-wages-emp-list');
Route::match(['get', 'post'], 'hr-monthly-attendance-report', 'HrPayroll\HrSalaryWagesController@monthlyAttendanceReport')->name('hr-monthly-attendance-report');
Route::post('get-hr-emp-salary-info', 'HrPayroll\HrSalaryWagesController@monthlyEmployeeSalaryInfo')->name('get-hr-emp-salary-info');
Route::post('hr-emp-salary-update', 'HrPayroll\HrSalaryWagesController@monthlyEmployeeSalaryUpdate')->name('hr-emp-salary-update');
Route::match(['get', 'post'],'hr-emp-bonus-generate', 'HrPayroll\HrEidBonusController@generateBonusSheet')->name('hr-emp-bonus-generate');

Route::post('hr-bonus-submit', 'HrPayroll\HrEidBonusController@submitBonusSheet')->name('hr-bonus-submit');
Route::post('hr-emp-bonus-edit', 'HrPayroll\HrEidBonusController@editBonusSheet')->name('hr-emp-bonus-edit');
Route::post('hr-bonus-delete', 'HrPayroll\HrEidBonusController@deleteBonusData')->name('hr-bonus-delete');
Route::post('hr-bonus-sheet-update', 'HrPayroll\HrEidBonusController@updateBonusSheet')->name('hr-bonus-sheet-update');
Route::match(['get', 'post'],'hr-emp-bonus-sheet', 'HrPayroll\HrEidBonusController@BonusSheet')->name('hr-emp-bonus-sheet');
Route::match(['get', 'post'],'hr-emp-bonus-sheet-data/{id}', 'HrPayroll\HrEidBonusController@BonusSheetData')->name('hr-emp-bonus-sheet-data');
Route::match(['get', 'post'],'hr-emp-bonus-report/{id?}/{type?}', 'HrPayroll\HrEidBonusController@BonusSheetReport')->name('hr-emp-bonus-report');
Route::get('hr-bonus-policy', 'HrPayroll\HrEidBonusController@hrBonuspolicy')->name('hr-bonus-policy');

Route::match(['get', 'post'],'hr-create-new-sheet/{id?}', 'HrPayroll\HrEidBonusController@hrBonusSheetCreate')->name('hr-create-new-sheet');



Route::post('hr-bonus-sheet-create-save', 'HrPayroll\HrEidBonusController@hrBonusSheetCreateSave')->name('hr-bonus-sheet-create-save');

//bonus approval
Route::post('bonus-delegation-process', 'HrPayroll\HrEidBonusController@bonusDelegationProcess')->name('bonus-delegation-process');
Route::get('hr-bonus-approve-list', 'HrPayroll\HrEidBonusController@bonusApproveList')->name('hr-bonus-approve-list');
Route::post('hr-bonus-bulk-approved', 'HrPayroll\HrEidBonusController@bonusBulkApproved')->name('hr-bonus-bulk-approved');


Route::post('hr-salary-sheet-create-save', 'HrPayroll\HrSalaryWagesController@salarySheetCreateSave')->name('hr-salary-sheet-create-save');
Route::get('add-employee-manually-for-bonus','HrPayroll\HrEidBonusController@addEmployeeManuallyForBonus')->name('add-employee-manually-for-bonus');

Route::post('hr-emp-salary-sheet-generate', 'HrPayroll\HrSalaryWagesController@salarySheetGenerate')->name('hr-emp-salary-sheet-generate');

Route::get('hr-emp-salary-sheet-bank-advice/{sheet_code}/{type?}', 'HrPayroll\HrSalaryWagesController@salarySheetBankAdviceGenerate')->name('hr-emp-salary-sheet-bank-advice');
Route::post('hr-salary-sheet-bank-advice-save', 'HrPayroll\HrSalaryWagesController@salarySheetBankAdviceSave')->name('hr-salary-sheet-bank-advice-save');
Route::post('hr-salary-sheet-emp-delete', 'HrPayroll\HrSalaryWagesController@salarySheetDeleteEmployee')->name('hr-salary-sheet-emp-delete');

Route::get('hr-emp-salary-sheet-bank-advice-pdf/{code}', 'HrPayroll\HrSalaryWagesController@salarySheetBankAdvicePDF')->name('hr-emp-salary-sheet-bank-advice-pdf');


Route::get('api/{debug?}', 'Auth\SessionFilterController@testAPI')->name('api');
Route::get('session-filter/{debug?}', 'Auth\SessionFilterController@setUserFilterArray')->name('session-filter');
Route::get('session-permission-filter/{debug?}', 'Auth\SessionFilterController@setPermissionFilterArray')->name('session-permission-filter');

Route::post('salary-sheet-delegation-process', 'HrPayroll\HrSalaryWagesController@salarySheetDelegationProcess')->name('salary-sheet-delegation-process');
Route::get('hr-salary-sheet-approve-list', 'HrPayroll\HrSalaryWagesController@salarySheetApproveList')->name('hr-salary-sheet-approve-list');
Route::post('hr-salary-sheet-bulk-approved', 'HrPayroll\HrSalaryWagesController@salarySheetBulkApproved')->name('hr-salary-sheet-bulk-approved');

/*=================HR Employee Gardes=====================*/
Route::get('hr-emp-grade', 'HrEmpGrade\HrEmpGradeController@gradeList')->name('hr-emp-grade');
Route::get('hr-emp-grade-entry/{grade_id?}', 'HrEmpGrade\HrEmpGradeController@gradeEntry')->name('hr-emp-grade-entry');
Route::post('hr-emp-grade-store/{grade_id?}', 'HrEmpGrade\HrEmpGradeController@gradeStpre')->name('hr-emp-grade-store');
Route::post('hr-emp-grade-destroy', 'HrEmpGrade\HrEmpGradeController@destroy')->name('hr-emp-grade-destroy');

/* ================ KPI ==================== */
Route::get('kpi-settings', 'Kpi\KpiSettingsController@index')->name('kpi-settings');
Route::get('kpi-create/{code?}', 'Kpi\KpiSettingsController@create')->name('kpi-create');
Route::post('kpi-store/{code?}', 'Kpi\KpiSettingsController@store')->name('kpi-store');
Route::delete('kpi-destroy/{code?}', 'Kpi\KpiSettingsController@destroy')->name('kpi-destroy');



Route::get('kpi-config', 'Kpi\KpiConfigController@index')->name('kpi-config');
Route::get('kpi-config-view/{id}', 'Kpi\KpiConfigController@view')->name('kpi-config-view');
Route::get('kpi-config-edit-validation/{id}', 'Kpi\KpiConfigController@editValidation')->name('kpi-config-edit-validation');
Route::get('kpi-config-create-form/{id?}', 'Kpi\KpiConfigController@create_form')->name('kpi-config-create-form');
Route::post('kpi-config-store', 'Kpi\KpiConfigController@store')->name('kpi-config-create-store');
Route::post('kpi-config-update', 'Kpi\KpiConfigController@update')->name('kpi-config-create-update');
Route::post('kpi-config-delete', 'Kpi\KpiConfigController@delete')->name('kpi-config-create-delete');

Route::match(['get', 'post'],'assigned-kpi-list/{type?}', 'Kpi\KpiAssignedController@index')->name('assigned-kpi-list');
Route::get('kpi-assign-form', 'Kpi\KpiAssignedController@assignKpi')->name('kpi-assign-form');
Route::match(['get','post'],'insert-monthly-variable-salary','Kpi\KpiAssignedController@insertMonthlyVariableSalary')->name('insert-monthly-variable-salary');
Route::get('kpi-assign-form-xl', 'Kpi\KpiAssignedController@assignKpiXl')->name('kpi-assign-form-xl');
Route::get('get-kpi-config-details/{id}/{ff?}', 'Kpi\KpiAssignedController@configDetails')->name('get-kpi-config-details');
Route::get('get-kpi-assign-view/{id}/{id2}', 'Kpi\KpiAssignedController@kpiAssignView')->name('get-kpi-assign-view');
// Route::get('get-kpi-config-details/{id}', 'Kpi\KpiAssignedController@configDetails')->name('get-kpi-config-details');
Route::post('kpi-assign-form-store', 'Kpi\KpiAssignedController@store')->name('kpi-assign-form-store');

// Route::post('kpi-assign-xl-download', 'Kpi\KpiAssignedController@xlDownloadApi')->name('kpi-assign-xl-download');
Route::post('kpi-assign-xl-download', 'Kpi\KpiAssignedController@xlDownload')->name('kpi-assign-xl-download');

// Route::post('kpi-assign-xl-upload', 'Kpi\KpiAssignedController@xlUploadApi')->name('kpi-assign-xl-upload');
Route::post('kpi-assign-xl-upload', 'Kpi\KpiAssignedController@xlUpload')->name('kpi-assign-xl-upload');
Route::get('assigned-kpi-manually', 'Kpi\KpiAssignedController@assignedManually')->name('assigned-kpi-manually');
Route::get('assigned-kpi-excel', 'Kpi\KpiAssignedController@assignedExcel')->name('assigned-kpi-excel');

Route::get('kpi-achievement', 'Kpi\KpiAchievementController@index')->name('kpi-achievement');
Route::post('
', 'Kpi\KpiAchievementController@kpiAchievementGet')->name('kpi-achievement-get');

/*======================= Performance Evaluation ========================*/
Route::get('pe-head-title', 'PerformanceEvaluation\PerformanceEvaluationController@headTitle')->name('pe-head-title');
Route::post('pe-store-head-title', 'PerformanceEvaluation\PerformanceEvaluationController@storeHeadTitle')->name('pe-store-head-title');
Route::get('pe-get-head-data/{id}', 'PerformanceEvaluation\PerformanceEvaluationController@getHeadData')->name('pe-get-head-data');
Route::get('pe-kpi-question', 'PerformanceEvaluation\PerformanceEvaluationController@kpiQuestion')->name('pe-kpi-question');
Route::post('pe-store-kpi-question', 'PerformanceEvaluation\PerformanceEvaluationController@storeKpiQuestion')->name('pe-store-kpi-question');
Route::get('pe-get-kpi-question-data/{id}', 'PerformanceEvaluation\PerformanceEvaluationController@getQuestionData')->name('pe-get-kpi-question-data');
Route::get('pe-kpi-configuration', 'PerformanceEvaluation\PerformanceEvaluationController@configurationList')->name('pe-kpi-configuration');
Route::post('pe-store-config', 'PerformanceEvaluation\PerformanceEvaluationController@storeConfig')->name('pe-store-config');
Route::get('pe-create-config', 'PerformanceEvaluation\PerformanceEvaluationController@createConfig')->name('pe-create-config');
Route::get('pe-get-config-view/{id}', 'PerformanceEvaluation\PerformanceEvaluationController@getConfigView')->name('pe-get-config-view');
Route::get('pe-config-edit/{id}', 'PerformanceEvaluation\PerformanceEvaluationController@configEditForm')->name('pe-config-edit');
Route::post('pe-update-config', 'PerformanceEvaluation\PerformanceEvaluationController@updateConfig')->name('pe-update-config');
Route::get('pe-download-excel-config', 'PerformanceEvaluation\PerformanceEvaluationController@downloadConfigExcel')->name('pe-download-excel-config');

Route::get('pe-evaluate-emp', 'PerformanceEvaluation\EvaluateEmployeeController@evaluateEmployee')->name('pe-evaluate-emp');
Route::post('pe-evaluate-emp', 'PerformanceEvaluation\EvaluateEmployeeController@evaluateEmployee')->name('pe-evaluate-emp');
Route::post('pe-evaluate-emp-store', 'PerformanceEvaluation\EvaluateEmployeeController@evaluateEmployeeStore')->name('pe-evaluate-emp-store');
Route::get('pe-evaluation-list', 'PerformanceEvaluation\EvaluateEmployeeController@evaluationList')->name('pe-evaluation-list');
Route::get('pe-evaluation-list-details/{id}', 'PerformanceEvaluation\EvaluateEmployeeController@evaluationListDetails')->name('pe-evaluation-list-details');
Route::get('pe-get-user-by-designation/{id}/{u_id?}', 'PerformanceEvaluation\EvaluateEmployeeController@getUserByDesignation')->name('pe-get-user-by-designation');
Route::get('pe-get-designation-by-year/{year}/{id1?}/{id2?}', 'PerformanceEvaluation\EvaluateEmployeeController@getDesignationByYear')->name('pe-get-designation-by-year');
Route::match(['get', 'post'],'pe-emp-achievement-datewise', 'PerformanceEvaluation\EvaluateEmployeeController@EmpAchievementDatewise')->name('pe-emp-achievement-datewise');

/*======================= Training Module =============================*/
Route::match(['get', 'post'],'training-list', 'Training\TrainingController@trainingList')->name('training-list');
Route::get('training-list-edit/{id}', 'Training\TrainingController@trainingListEdit')->name('training-list-edit');

/*======================= payroll KPI Report ==========================*/
Route::match(['get', 'post'],'kpi-monthly-summery/{type?}', 'Kpi\KpiReportController@monthly_summery')->name('kpi-monthly-summery');
Route::match(['get', 'post'],'slotwise-kpi-report', 'Kpi\KpiReportController@slotwiseKpiReport')->name('slotwise-kpi-report');
Route::match(['get', 'post'],'monthly-kpi-wise-achievement/{type?}', 'Kpi\KpiReportController@kpiWiseReport')->name('monthly-kpi-wise-achievement');
Route::post('/kpi-monthly-summary-xl', 'Kpi\KpiReportController@kpiMonthlySummaryExcel');

/*================= Location Dropdown ===========================*/
Route::get('/view_location', 'LocationConfigurationController@view');
Route::get('/test', 'LocationConfigurationController@test');
Route::post('/return_location', 'LocationConfigurationController@return_location');


Route::get('/getAllPlaces','LocationAccess@getAllPlaces')->name('getAllPlaces');


Route::get('/get-tree-places','LocationTree@getTreePlaces')->name('get-tree-places');


//=============Attachment
Route::post('support-document-upload', 'Attachment\AttachmentController@supportDocumentUpload')->name('support-document-upload');
Route::post('get-supporting-files', 'Attachment\AttachmentController@getSupportingFiles')->name('get-supporting-files');
Route::post('delete-attachments-item', 'Attachment\AttachmentController@deleteAttachmentsItem')->name('delete-attachments-item');

//======================Delegation process
Route::post('go-to-hr-delegation-process', 'HrIncrementPromotion\HrIncrementController@goToHRDelegationProcess')->name('go-to-hr-delegation-process');
Route::post('hr-salary-bulk-approved', 'HrIncrementPromotion\HrIncrementController@HRsalaryBulkApproved')->name('hr-salary-bulk-approved');
Route::post('go-to-leave-delegation-process', 'HR\LeaveManager@goToLeaveDelegationProcess')->name('go-to-leave-delegation-process');

/*=============== MODULE LANDING PAGE =======================*/
Route::get('module-landing-page', 'ModuleController@moduleList');

/*=============== Report ====================*/
Route::match(['get', 'post'], 'employee-list/{type?}', 'EmployeeReport\EmployeeReport@employeeList')->name('employee-list-report');
Route::match(['get', 'post'], 'employee-list-with-all-components/{type?}', 'EmployeeReport\EmployeeReport@employeeListAllComponents')->name('employee-list-with-all-components');
Route::match(['get', 'post'], 'employee-off-day-summary/{type?}', 'EmployeeReport\EmployeeReport@employeeOffDaySummary')->name('employee-off-day-summary');
Route::match(['get', 'post'], 'new-joining-status/{type?}', 'EmployeeReport\EmployeeReport@newJoiningStatus')->name('new-joining-status');

Route::get('appointment-letter/{employee}', 'EmployeeReport\EmployeeReport@AppointmentLetter')->name('appointment-letter');
Route::get('confirmation-letter/{employee}', 'EmployeeReport\EmployeeReport@ConfirmationLetter')->name('confirmation-letter');
Route::get('job-application/{employee}', 'EmployeeReport\EmployeeReport@JobApplication')->name('job-application');

/*====================== query to excel==========*/

Route::match(['get', 'post'],'query-to-excel','QueryToExcel@preExcel')->name('query-to-excel');

/*=============== Report Payroll ====================*/
Route::match(['get', 'post'], 'employee-salary-requisition/{type?}', 'HrPayroll\HrPayrollReportController@empSalaryRequisition')->name('employee-salary-requisition');
Route::match(['get', 'post'], 'get-hr-final-settlement', 'HrPayroll\HrPayrollReportController@finalSettlement')->name('get-hr-final-settlement');
Route::match(['get', 'post'], 'employee-insurance/{type?}', 'HrPayroll\HrPayrollReportController@empInsurance')->name('employee-insurance');
Route::match(['get', 'post'], 'earn-leave-payment-sheet/{type?}', 'HrPayroll\HrPayrollReportController@earnLeavePaymentSheet')->name('earn-leave-payment-sheet');
Route::match(['get', 'post'], 'employee-salary-sheet/{type?}', 'HrPayroll\HrPayrollReportController@monthlyEmployeeSalaryList')->name('employee-salary-sheet');
Route::match(['get', 'post'],'hr-pf-report/{type?}', 'HrPayroll\HrPayrollReportController@pfReportSheet')->name('hr-pf-report');
Route::match(['get', 'post'], 'get-hr-salary-pay-slip', 'HrPayroll\HrPayrollReportController@salaryPaySlip')->name('get-hr-salary-pay-slip');
Route::match(['get', 'post'], 'get-hr-pfp-pay-slip', 'HrPayroll\HrPayrollReportController@PFPSalaryPaySlip')->name('get-hr-pfp-pay-slip');
Route::get('emp-settlement-list', 'HrPayroll\HrPayrollReportController@getSeparationSettlementList')->name('emp-settlement-list');

//Shibly :Individual PF Report.
Route::match(['get', 'post'],'hr-pf-individual-report/{type?}', 'HrPayroll\HrPayrollReportController@individualPFReportSheet')->name('hr-pf-individual-report');
Route::get('image-cropper', 'imageCropperController@image_cropper')->name('image-cropper');

/* ================ System Settings In Admin Module ================= */
Route::get('system-settings/view', 'SystemSettings\SystemSettingsController@view');
Route::post('update-system-settings', 'SystemSettings\SystemSettingsController@update')->name('update-system-settings');

/*======== HR Employee Sections ===============*/
Route::get('hr-employee-section', 'HrEmpSection\HrEmpSectionController@sectionList')->name('hr-employee-section');
Route::get('create-employee-section/{id?}', 'HrEmpSection\HrEmpSectionController@createSection')->name('create-employee-section');
Route::post('store-employee-section/{id?}', 'HrEmpSection\HrEmpSectionController@storeSection')->name('store-employee-section-store');
Route::post('employee-section-delete/{id?}', 'HrEmpSection\HrEmpSectionController@destroySection')->name('employee-section-delete');

Route::post('section-by-department', 'HR\EmployeeManager@sectionByDepartment')->name('section-by-department');
Route::get('sendnotice', 'TestController@sendNotice')->name('sendnotice');
Route::get('getnotice', 'TestController@getNotice')->name('getnotice');
Route::post('delete-attachments-ajax', 'Attachment\AttachmentController@deleteAttachmentsAjax')->name('delete-attachments-ajax');

///*============Kpi List========*/
//Route::get('get-kpi-list/{group?}','Kpi\KpiListController@kpiList')->name('get-kpi-list');
//Route::get('location-wise-kpi/{id?}/{type?}','Kpi\KpiListController@locationWiseKpi')->name('location-wise-kpi');
//Route::match(['get','post'],'download-kpi-detail-excel','Kpi\KpiListController@downloadKpiDetail')->name('download-kpi-detail-excel');
//



/*========================================================By Saif============================================= */

/*=========== GRADE ENRTY ============= */
Route::match(['get', 'post'], 'employee-grade-entry/{id?}', 'HR\EmployeeGrade@basicFormGrade')->name('employee-grade-entry');
Route::post('store-grade-info/{grade_id?}', 'HR\EmployeeGrade@storeGradeInfo')->name('store-grade-info');
Route::match(['get', 'post'], 'emp-grade-list', 'HR\EmployeeGrade@empGradeList')->name('emp-grade-list');
Route::post('employee-grade-delete/{id}', 'HR\EmployeeGrade@deleteEmpGrade')->name('deleteEmpGrade');

/*=========== GRADE Component Entry ============= */
Route::post('grade-component-form', 'HR\EmployeeGrade@componentForm')->name('grade-component');
Route::match(['get', 'post'], 'emp-grade-component-list/{id?}', 'HR\EmployeeGrade@empGradeComponentList')->name('emp-grade-component-list');
Route::post('store-grade-component-info', 'HR\EmployeeGrade@storeGradeComponentInfo')->name('store-grade-component-info');
Route::post('employee-grade-component-delete', 'HR\EmployeeGrade@deleteEmpGradeComponent')->name('deleteEmpGradeComponent');

Route::match(['get', 'post'], 'employee-grade-componemt-entry/{id?}/{tab?}', 'HR\EmployeeGrade@basicFormGradeComponent')->name('employee-grade-component-entry');
Route::match(['get', 'post'], 'employee-grade-componemt/{id?}/{tab}/{mode}', 'HR\EmployeeGrade@basicFormGradeComponent')->name('employee-grade-view');

/************advance salary************/
Route::get('loan-list', 'HrPayroll\DisbursementController@disbursementList')->name('loan-list');
Route::get('new-loan-entry/{id?}', 'HrPayroll\DisbursementController@newLoanEntry')->name('new-loan-entry');
Route::post('hr-loan-entry-save', 'HrPayroll\DisbursementController@loanEntrySave')->name('hr-loan-entry-save');
Route::post('loan-delete', 'HrPayroll\DisbursementController@loanDelete')->name('loan-delete');
Route::post('loan-lock', 'HrPayroll\DisbursementController@loanLock')->name('loan-lock');
Route::get('emp-loan-info/{id?}', 'HrPayroll\DisbursementController@employeeLoanInfo')->name('emp-loan-info');
Route::get('loan-print/{id?}', 'HrPayroll\DisbursementController@employeeLoanPrint')->name('loan-print');
Route::post('store-paid-loan', 'HrPayroll\DisbursementController@storePaidLoan')->name('store-paid-loan');
Route::match(['get','post'],'paid-loan-list', 'HrPayroll\DisbursementController@paidLoanList')->name('paid-loan-list');

Route::get('hr-salary-disbursement','HrPayroll\DisbursementController@salaryDisbursement')->name('hr-salary-disbursement');

Route::get('emp-salary-statement/{id?}', 'HrPayroll\HrPayrollReportController@salaryStatement')->name('emp-salary-statement');

Route::get('make-salary-disburse','HrPayroll\DisbursementController@makeSalaryDisburse')->name('make-salary-disburse');
Route::get('make-bonus-disburse','HrPayroll\DisbursementController@makeBonusDisburse')->name('make-bonus-disburse');

Route::post('loan-delegation-process', 'HrPayroll\DisbursementController@loanDelegationProcess')->name('loan-delegation-process');
Route::get('hr-loan-approve-list', 'HrPayroll\DisbursementController@hrLoanApproveList')->name('hr-loan-approve-list');
Route::post('hr-loan-bulk-approved', 'HrPayroll\DisbursementController@hrLoanBulkApproved')->name('hr-loan-bulk-approved');


/************Salary deduction************/
Route::get('deduction-list', 'HrPayroll\DeductionController@deductionList')->name('deduction-list');
Route::get('new-deduction-entry/{id?}', 'HrPayroll\DeductionController@newDeductionEntry')->name('new-deduction-entry');
Route::get('emp-deduction-info/{id?}', 'HrPayroll\DeductionController@employeeDeductionInfo')->name('emp-deduction-info');
Route::post('hr-deduction-entry-save', 'HrPayroll\DeductionController@deductionEntrySave')->name('hr-deduction-entry-save');
Route::post('deduction-delete', 'HrPayroll\DeductionController@deductionDelete')->name('deduction-delete');
Route::post('deduction-delegation-process', 'HrPayroll\DeductionController@DeductionDelegationProcess')->name('deduction-delegation-process');
Route::get('hr-deduction-approve-list', 'HrPayroll\DeductionController@hrDeductionApproveList')->name('hr-deduction-approve-list');
Route::post('hr-deduction-bulk-approved', 'HrPayroll\DeductionController@hrDeductionBulkApproved')->name('hr-deduction-bulk-approved');


/*
 * Document Upload
 */
Route::get('document-upload/{id?}', 'Document\DocumentController@create')->name('document-upload');
Route::post('document-upload/{id?}', 'Document\DocumentController@store')->name('document-upload');
Route::post('document-delete', 'Document\DocumentController@destroy')->name('document-delete');
Route::get('document-list', 'Document\DocumentController@index')->name('document-list');



Route::post('hr-manual-attendance-sheet-create', 'HR\EmployeeManager@manualAttendanceSheetCreate')->name('hr-manual-attendance-sheet-create');
Route::get('get-kpi-daily-achievement/{date?}', 'Kpi\KpiAchievementController@get_achievement_prism')->name('get-kpi-daily-achievement');
Route::match(['get','post'],'get-manual-achievement','Kpi\KpiAchievementController@getManualAchievement')->name('get-manual-achievement');

// Audit log
Route::get('audit-log/{table}/{from?}/{to?}/{type?}','AuditLogController@index','audit-log');

Route::get('get-kpi-list/{group?}','Kpi\KpiListController@kpiList')->name('get-kpi-list');
Route::get('location-wise-kpi/{id?}/{type?}/{location_id?}','Kpi\KpiListController@locationWiseKpi')->name('location-wise-kpi');
Route::match(['get','post'],'download-kpi-detail-excel','Kpi\KpiListController@downloadKpiDetail')->name('download-kpi-detail-excel');
Route::match(['get','post'],'upload-kpi-target','Kpi\KpiListController@uploadKpiTarget')->name('upload-kpi-target');
Route::match(['get','post'],'delete-kpi-target','Kpi\KpiListController@deleteKpiTarget')->name('delete-kpi-target');

