<?php

use yii\db\Migration;

/**
 * Handles dropping is_private from table `account`.
 */
class m180820_175219_drop_is_private_column_from_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('account', 'is_private');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('account', 'is_private', $this->boolean());
    }
}
