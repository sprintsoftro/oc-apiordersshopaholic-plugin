<?php
if (has_jwtauth_plugin()) {
    Route::middleware(['jwt.auth'])
        ->group(
            function () {
                Route::prefix('profile')
                    ->name('profile.')
                    ->group(
                        function () {
                            Route::get('address', 'UserAddresses@address')->name('address');
                            Route::post('address/add', 'UserAddresses@addAddress')->name('address.add');
                            Route::post('address/update', 'UserAddresses@updateAddress')->name('address.update');
                            Route::post('address/remove', 'UserAddresses@removeAddress')->name('address.remove');
                        }
                    );
            }
        );
}
