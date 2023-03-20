<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateLovataOrdersShopaholicUserAddresses
 * @package PlanetaDelEste\ApiOrdersShopaholic\Updates
 */
class TableUpdateLovataOrdersShopaholicUserAddresses2 extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_user_addresses';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'email')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->string('email')->nullable()->after('type');
            $obTable->string('phone')->nullable()->after('type');
            $obTable->string('last_name')->nullable()->after('type');
            $obTable->string('first_name')->nullable()->after('type');
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'email')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['first_name', 'last_name', 'phone', 'email']);
        });
    }
}