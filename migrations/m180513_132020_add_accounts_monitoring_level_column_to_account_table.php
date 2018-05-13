<?php

use yii\db\Migration;

/**
 * Handles adding accounts_monitoring_level to table `account`.
 */
class m180513_132020_add_accounts_monitoring_level_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'accounts_monitoring_level', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'accounts_monitoring_level');
    }
}
