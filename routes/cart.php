<?php
Route::middleware(['web'])
    ->group(
        function () {
            Route::prefix('cart')
                ->name('cart.')
                ->group(
                    function () {
                        Route::get('data/{payment_method_id?}', 'Cart@getData')->name('data');
                        Route::get('get/{shipping_type_id?}/{payment_method_id?}', 'Cart@get')->name('get');
                        Route::post('add', 'Cart@add')->name('add');
                        Route::post('update', 'Cart@update')->name('update');
                        Route::post('remove', 'Cart@remove')->name('remove');
                        Route::get('payment_method_list', 'PaymentMethodList@get')->name('payment_method_list');
                        Route::get('shipping_type_list', 'ShippingTypeList@get')->name('shipping_type_list');
                    }
                );
        }
    );
