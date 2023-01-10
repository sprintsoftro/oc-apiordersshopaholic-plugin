<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Order;

use PlanetaDelEste\ApiToolbox\Classes\Resource\Base as BaseResource;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\PaymentMethod\ItemResource as ItemResourcePaymentMethod;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Status\ItemResource as ItemResourceStatus;
use PlanetaDelEste\ApiOrdersShopaholic\Plugin;
use October\Rain\Network\Http;

/**
 * Class ItemResource
 *
 * @mixin \Lovata\OrdersShopaholic\Classes\Item\OrderItem
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Order
 */
class ItemResource extends BaseResource
{
    public function getData(): array
    {
        return [
            'shipping_price_value'                => (float)$this->shipping_price_value,
            'old_shipping_price_value'            => (float)$this->old_shipping_price_value,
            'discount_shipping_price_value'       => (float)$this->discount_shipping_price_value,
            'total_price_value'                   => (float)$this->total_price_value,
            'old_total_price_value'               => (float)$this->old_total_price_value,
            'discount_total_price_value'          => (float)$this->discount_total_price_value,
            'position_total_price_value'          => (float)$this->position_total_price_value,
            'old_position_total_price_value'      => (float)$this->old_position_total_price_value,
            'discount_position_total_price_value' => (float)$this->discount_position_total_price_value,
            'status'                              => ItemResourceStatus::make($this->status),
            'payment_method'                      => $this->payment_method
                ? ItemResourcePaymentMethod::make($this->payment_method)
                : null,
            'octav_data' => $this->getOctavData($this),
        ];
    }

    protected function getOctavData($obItemResource) 
    {
        if(!isset($obItemResource->property['octav_order_number'])) {
            return [];
        }
        
        $url = config('api.base_url'). '/api/v1/show-order/'.$obItemResource->property['octav_order_number'];

        $response = Http::get($url, function($http) {
            $http->header(config('api.key_name'), config('api.key'));
            $http->timeout(3600);
        });

        $responseAr = json_decode($response->body, true);

        if(!$responseAr) {
            return;
        }
        
        return [
            'octav_status' => $responseAr['status'],
            'octav_status_id' => $responseAr['status_id'],
            'octav_awb' => $responseAr['awb']
        ];
    }

    public function getDataKeys(): array
    {
        return ['id', 'order_number', 'currency_symbol', 'total_price'];
    }

    protected function getEvent(): ?string
    {
        return Plugin::EVENT_ITEMRESOURCE_DATA.'.order';
    }
}
