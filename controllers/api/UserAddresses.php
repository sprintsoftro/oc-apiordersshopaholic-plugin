<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Controllers\Api;

use Lang;
use Input;
use Exception;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Classes\Collection\UserAddressCollection;
use Lovata\OrdersShopaholic\Components\UserAddress;
use PlanetaDelEste\ApiOrdersShopaholic\Classes\Resource\UserAddress\IndexCollection;
use PlanetaDelEste\ApiToolbox\Classes\Api\Base;
use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\OrdersShopaholic\Models\UserAddress as UserAddressModel;

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
        $arAddressData = Input::all();

        $iUserID = UserHelper::instance()->getUserId();
        if (empty($arAddressData) || empty($iUserID)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        // Disable check for duplicates addresses
        // $obAddress = UserAddressModel::findAddressByData($arAddressData, $iUserID);
        // if (!empty($obAddress)) {
        //     $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.e_address_exists');
        //     return Result::setFalse()->setMessage($sMessage)->get();
        // }

        $arAddressData['user_id'] = $iUserID;
        $this->userAddressComponent()->createAddressObject($arAddressData);

        return Result::get();
    }

    /**
     * @return array
     * @throws \SystemException
     */
    public function updateAddress(): array
    {
        $arAddressData = Input::all();
        $iAddressID = array_get($arAddressData, 'id');

        $iUserID = UserHelper::instance()->getUserId();
        // $obAddress = UserAddressModel::findAddressByData($arAddressData, $iUserID);

        if (empty($arAddressData) || empty($iAddressID) || empty($iUserID)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        // if (!empty($obAddress) && $obAddress->id != $iAddressID) {
        //     $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.e_address_exists');
        //     return Result::setFalse()->setMessage($sMessage)->get();
        // }

        //Find address object by ID
        $obAddress = UserAddressModel::getByUser($iUserID)->find($arAddressData['id']);
        if (empty($obAddress)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        $this->userAddressComponent()->updateAddressObject($obAddress, $arAddressData);

        return Result::get();
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
