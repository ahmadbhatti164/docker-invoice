<?php

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

Auth::routes();

Route::namespace('Auth')->group(function () {


});

Route::middleware(['auth', 'permission'])->group(function() {


	Route::namespace('Admin')->group(function () {
		Route::namespace('User')->group(function () {
			Route::prefix('user')->group(function () {
				Route::get('list', 'UserController@list')->name('userList');
				Route::get('detail/{userId}', 'UserController@detail')->name('userDetail');
				Route::post('list-data', 'UserController@index')->name('userListTable');
				Route::get('add', 'UserController@add')->name('addUser');
				Route::post('create', 'UserController@insert')->name('userForm');
				Route::get('edit/{userId}', 'UserController@edit')->name('editUser');
				Route::post('update/{userId}', 'UserController@update')->name('updateUser');

				Route::get('profile', 'UserController@editProfile')->name('editProfile');
				Route::post('update-profile', 'UserController@updateProfile')->name('updateProfile');

                Route::delete('remove/{userId}', 'UserController@delete')->name('removeUser');

			});
		});

		Route::namespace('Vendor')->group(function () {
			Route::prefix('vendor')->group(function () {
				Route::get('list', 'VendorController@list')->name('vendorList');
				Route::get('detail/{vendorId}', 'VendorController@detail')->name('vendorDetail');
				Route::post('list-data', 'VendorController@index')->name('vendorListTable');
				Route::get('add', 'VendorController@add')->name('addVendor');
				Route::post('create', 'VendorController@insert')->name('vendorForm');
				Route::get('parse', 'VendorController@parseFile')->name('parseFile');
				Route::post('parse', 'VendorController@parseFileToHtml')->name('parseFileForm');
				Route::get('edit/{vendorId}', 'VendorController@edit')->name('editVendor');
				Route::post('update/{vendorId}', 'VendorController@update')->name('updateVendor');
                Route::delete('remove/{vendorId}', 'VendorController@delete')->name('removeVendor');
			});
		});

		Route::namespace('Invoice')->group(function () {
			Route::prefix('invoice')->group(function () {
				Route::get('list', 'InvoiceController@list')->name('invoiceList');
				Route::get('detail/{invoiceId}', 'InvoiceController@detail')->name('invoiceDetail');
				Route::post('list-data', 'InvoiceController@index')->name('invoiceListTable');
				Route::get('add', 'InvoiceController@add')->name('addInvoice');
				Route::post('create', 'InvoiceController@insert')->name('invoiceForm');
				Route::get('edit/{invoiceId}', 'InvoiceController@edit')->name('editInvoice');
				Route::post('update/{invoiceId}', 'InvoiceController@update')->name('updateInvoice');

				Route::post('user-invoices', 'InvoiceController@userInvoiceList')->name('userInvoiceList');
			});
		});

		Route::namespace('Product')->group(function () {
			Route::prefix('product')->group(function () {
				Route::get('list', 'ProductController@list')->name('productList');
				Route::get('detail/{productId}', 'ProductController@detail')->name('productDetail');
				Route::post('list-data', 'ProductController@index')->name('productListTable');
				Route::get('add', 'ProductController@add')->name('addProduct');
				Route::post('create', 'ProductController@insert')->name('productForm');
				Route::get('edit/{productId}', 'ProductController@edit')->name('editProduct');
				Route::post('update/{productId}', 'ProductController@update')->name('updateProduct');
                Route::delete('remove/{productId}', 'ProductController@delete')->name('removeProduct');
			});
		});

		Route::namespace('OCR')->group(function () {
			Route::prefix('ocr')->group(function () {
				//Route::get('list', 'ProductController@list')->name('productList');
			});
			Route::resource('ocr','OCRController');
		});

        Route::namespace('Category')->group(function () {
            Route::prefix('category')->group(function () {
                Route::get('list', 'CategoryController@list')->name('categoryList');
                Route::post('list-data', 'CategoryController@index')->name('categoryListTable');
                Route::get('add', 'CategoryController@add')->name('addCategory');
                Route::post('create', 'CategoryController@store')->name('categoryForm');
                Route::get('edit/{categoryId}', 'CategoryController@edit')->name('editCategory');
                Route::post('update/{categoryId}', 'CategoryController@update')->name('updateCategory');
                Route::delete('remove/{categoryId}', 'CategoryController@delete')->name('removeCategory');
            });
        });

        Route::post('search', 'SearchController@globalSearch')->name('search');
        Route::get('searchDetail/{value}', 'SearchController@searchDetail')->name('searchDetail');

	});
});

Route::namespace('Admin')->group(function () {
    Route::namespace('Company')->group(function () {
        Route::prefix('company')->group(function () {
            Route::get('list', 'CompanyController@list')->name('companyList');
            Route::post('list-data', 'CompanyController@index')->name('companyListTable');
            Route::get('detail/{companyId}', 'CompanyController@detail')->name('companyDetail');
            Route::get('add', 'CompanyController@add')->name('addCompany');
            Route::post('create', 'CompanyController@store')->name('companyForm');
            Route::get('edit/{companyId}', 'CompanyController@edit')->name('editCompany');
            Route::post('update/{companyId}', 'CompanyController@update')->name('updateCompany');
            Route::delete('remove/{companyId}', 'CompanyController@delete')->name('removeCompany');
        });
    });
});

Route::get('/', 'HomeController@index')->name('home');

Route::get('/dashboard', 'HomeController@index')->name('dashboard');
Route::get('/weeklyExpenses', 'HomeController@weeklyExpenses')->name('weeklyExpenses');
