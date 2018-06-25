<?php

use yii\db\Migration;

/**
 * Class m180621_100101_chg_account_add_indexes
 */
class m180621_100101_chg_account_add_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_account_monitoring', 'account', 'monitoring');
        $this->createIndex('idx_account_disabled', 'account', 'disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_account_monitoring', 'account');
        $this->dropIndex('idx_account_disabled', 'account');
    }
}
