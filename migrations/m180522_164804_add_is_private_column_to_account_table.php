<?php

use yii\db\Migration;

/**
 * Handles adding is_private to table `account`.
 */
class m180522_164804_add_is_private_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'is_private', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'is_private');
    }
}
