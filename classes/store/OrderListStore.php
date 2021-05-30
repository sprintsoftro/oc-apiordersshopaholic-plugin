<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Store\Order\SortingListStore;

/**
 * Class OrderListStore
 *
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Store
 * @property SortingListStore $sorting
 */
class OrderListStore extends AbstractListStore
{
    const SORT_CREATED_AT_ASC = 'created_at|asc';
    const SORT_CREATED_AT_DESC = 'created_at|desc';

    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('sorting', SortingListStore::class);
    }
}
