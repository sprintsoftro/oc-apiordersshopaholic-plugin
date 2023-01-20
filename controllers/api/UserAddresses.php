<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api;

use Exception;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Classes\Collection\UserAddressCollection;
use Lovata\OrdersShopaholic\Components\UserAddress;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\UserAddress\IndexCollection;
use PlanetaDelEste\ApiToolbox\Classes\Api\Base;

class UserAddresses extends Base
{
    /**
     * Get current user addresses
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function address()
    {
        try {
            $this->currentUser();
            
            $allAddresses = [];

            if($this->user->address) {
                $obAddresses = UserAddressCollection::make();
                $obAddressesCollection = collect($obAddresses->user($this->user->id));
                $allAddresses['companies'] = IndexCollection::make($obAddressesCollection->where('is_company', '==', true));
                $allAddresses['persons'] = IndexCollection::make($obAddressesCollection->where('is_company', '!=', true));
            }
            return Result::setData($allAddresses)->get();
        } catch (Exception $e) {
            return static::exceptionResult($e);
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function addAddress(): array
    {
        return $this->userAddressComponent()->onAdd();
    }

    /**
     * @return array
     * @throws \SystemException
     */
    public function updateAddress(): array
    {
        return $this->userAddressComponent()->onUpdate();
    }

    /**
     * @return array
     * @throws \SystemException
     * @throws \Exception
     */
    public function removeAddress(): array
    {

        return $this->userAddressComponent()->onRemove();
    }

    public function getModelClass(): string
    {
        return User::class;
    }

    /**
     * @return \Cms\Classes\ComponentBase|UserAddress
     * @throws \SystemException
     */
    protected function userAddressComponent()
    {
        return $this->component(UserAddress::class);
    }
}
