<?php

use yii\db\Migration;

/**
 * Handles adding is_valid to table `account`.
 */
class m180820_114348_add_is_valid_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'is_valid', $this->boolean()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'is_valid');
    }
}
