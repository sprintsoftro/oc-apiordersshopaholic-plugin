<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Status;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class IndexCollection
 *
 * @package PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\Status
 */
class IndexCollection extends ResourceCollection
{
    public $collects = ShowResource::class;

    public function toArray($request)
    {
        return $this->collection;
    }
}
