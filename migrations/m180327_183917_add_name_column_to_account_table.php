<?php

use yii\db\Migration;

/**
 * Handles adding name to table `account`.
 */
class m180327_183917_add_name_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'name', $this->string()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'name');
    }
}
