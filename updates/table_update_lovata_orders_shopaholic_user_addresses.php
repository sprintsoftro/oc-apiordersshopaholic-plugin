<?php namespace PlanetaDelEste\ApiOrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateLovataOrdersShopaholicUserAddresses
 * @package PlanetaDelEste\ApiOrdersShopaholic\Updates
 */
class TableUpdateLovataOrdersShopaholicUserAddresses extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_user_addresses';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'reg_number')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->boolean('is_company')->default(0)->after('type');
            $obTable->string('company')->nullable()->after('type');
            $obTable->string('cui')->nullable()->after('type');
            $obTable->string('reg_number')->nullable()->after('type');
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'reg_number')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['is_company', 'company', 'cui', 'reg_number']);
        });
    }
}