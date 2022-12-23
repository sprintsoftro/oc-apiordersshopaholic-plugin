<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\UserAddress;

use PlanetaDelEste\ApiToolbox\Classes\Resource\Base as BaseResource;
use PlanetaDelEste\ApiOrdersShopaholic\Plugin;
use System\Classes\PluginManager;

/**
 * Class ItemResource
 *
 * @mixin \Lovata\OrdersShopaholic\Classes\Item\UserAddressItem
 * @package PlanetaDelEste\ApiShopaholic\Classes\Resource\UserAddress
 */
class ItemResource extends BaseResource
{
    /**
     * @return array|void
     */
    public function getData(): array
    {
        /** @var Country $obCountry */
        $obCountry = null;
        $obState = null;
        if(PluginManager::instance()->hasPlugin('RainLab.Location')) {
            
            $obCountry = is_numeric($this->country) ? \RainLab\Location\Models\Country::find($this->country) : null;
            if (!$obCountry) {
                $obCountry = Country::getDefault();
            }
            
            /** @var State $obState */
            $obState = is_numeric($this->state) ? \RainLab\Location\Models\State::find($this->state) : null;
        }

        /** @var Town $obCity */
        $obCity = null;
        if(PluginManager::instance()->hasPlugin('VojtaSvoboda.LocationTown')) {
            $obCity = is_numeric($this->city) ? \VojtaSvoboda\LocationTown\Models\Town::find($this->city) : null;
        }

        return [
            'country_text' => $obCountry ? $obCountry->name : $this->country,
            'state_text'   => $obState ? $obState->name : $this->state,
            'city_text'    => $obCity ? $obCity->name : $this->city,
            'country'      => is_numeric($this->country) ? intval($this->country) : $this->country,
            'state'        => is_numeric($this->state) ? intval($this->state) : $this->state,
            'city'         => is_numeric($this->city) ? intval($this->city) : $this->city,
        ];
    }

    public function getDataKeys(): array
    {
        return [
            'id',
            'user_id',
            'type',
            'country',
            'state',
            'city',
            'country_text',
            'state_text',
            'city_text',
            'street',
            'house',
            'building',
            'flat',
            'floor',
            'address1',
            'address2',
            'postcode',
            'created_at',
            'updated_at'
        ];
    }

    protected function getEvent(): ?string
    {
        return Plugin::EVENT_ITEMRESOURCE_DATA.'.userAddress';
    }
}
