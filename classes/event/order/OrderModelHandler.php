<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\Order;

use Lovata\OrdersShopaholic\Classes\Collection\OrderCollection;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\Toolbox\Classes\Event\ModelHandler;
use Lovata\OrdersShopaholic\Models\Order;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Store\OrderListStore;

/**
 * Class OrderModelHandler
 *
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Event\Order
 */
class OrderModelHandler extends ModelHandler
{
    /** @var Order */
    protected $obElement;

    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        OrderCollection::extend(
            function ($obCollection) {
                $this->extendCollection($obCollection);
            }
        );
    }

    protected function extendCollection(OrderCollection $obCollection)
    {
        $obCollection->addDynamicMethod(
            'sort',
            function ($sSort = OrderListStore::SORT_CREATED_AT_DESC) use ($obCollection) {
                $arResultIDList = OrderListStore::instance()->sorting->get($sSort);
                return $obCollection->applySorting($arResultIDList);
            }
        );
    }

    /**
     * Get model class name
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Order::class;
    }

    /**
     * Get item class name
     *
     * @return string
     */
    protected function getItemClass()
    {
        return OrderItem::class;
    }

    /**
     * After create event handler
     */
    protected function afterCreate()
    {
        parent::afterCreate();

        $this->clearBySortingPublished();
    }

    /**
     * Clear cache by created_at
     */
    protected function clearBySortingPublished()
    {
        OrderListStore::instance()->sorting->clear(OrderListStore::SORT_CREATED_AT_ASC);
        OrderListStore::instance()->sorting->clear(OrderListStore::SORT_CREATED_AT_DESC);
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        $this->clearBySortingPublished();
    }
}
