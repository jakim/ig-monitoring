<?php

use yii\db\Migration;

/**
 * Handles adding er to table `account_stats`.
 */
class m180116_174154_add_er_column_to_account_stats_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('account_stats', 'er', $this->decimal(4, 2)->after('media'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('account_stats', 'er');
    }
}
