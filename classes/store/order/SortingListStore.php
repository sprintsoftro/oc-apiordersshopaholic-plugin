<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Store\Order;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;
use Lovata\OrdersShopaholic\Models\Order;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Store\OrderListStore;

/**
 * Class SortingListStore
 *
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Store\Order
 */
class SortingListStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     *
     * @return array
     */
    protected function getIDListFromDB(): array
    {
        switch ($this->sValue) {
            case OrderListStore::SORT_CREATED_AT_ASC:
                $arElementIDList = $this->getByPublishASC();
                break;
            case OrderListStore::SORT_CREATED_AT_DESC:
                $arElementIDList = $this->getByPublishDESC();
                break;
            default:
                $arElementIDList = $this->getDefaultList();
                break;
        }

        return $arElementIDList;
    }

    /**
     * Get sorting ID list by published (ASC)
     *
     * @return array
     */
    protected function getByPublishASC(): array
    {
        $arElementIDList = (array)Order::orderBy('created_at', 'asc')->lists('id');

        return $arElementIDList;
    }

    /**
     * Get sorting ID list by published (DESC)
     *
     * @return array
     */
    protected function getByPublishDESC(): array
    {
        $arElementIDList = (array)Order::orderBy('created_at', 'desc')->lists('id');

        return $arElementIDList;
    }

    /**
     * Get default list
     *
     * @return array
     */
    protected function getDefaultList(): array
    {
        $arElementIDList = (array)Order::lists('id');

        return $arElementIDList;
    }
}
