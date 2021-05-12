<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\PaymentMethod;

use PlanetaDelEste\ApiToolbox\Classes\Resource\Base as BaseResource;

/**
 * Class ItemResource
 *
 * @mixin \Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\PaymentMethod
 */
class ItemResource extends BaseResource
{

    /**
     * @inheritDoc
     */
    protected function getEvent(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return [];
    }

    public function getDataKeys(): array
    {
        return [
            'id',
            'name',
            'code',
            'preview_text',
            'restriction',
        ];
    }
}
