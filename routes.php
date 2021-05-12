<?php
Route::prefix('api/v1')
    ->namespace('PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api')
    ->middleware(['throttle:120,1', 'bindings'])
    ->group(
        function () {
            $arRoutes = ['cart','orders'];
            foreach ($arRoutes as $sPublicRoute) {
                Route::group([], plugins_path('/planetadeleste/apiordersshopaholic/routes/'.$sPublicRoute.'.php'));
            }
        }
    );
