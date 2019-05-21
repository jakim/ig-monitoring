<?php

use yii\db\Migration;

/**
 * Class m190521_155619_rm_disabled_column_from_account_table
 */
class m190521_155619_rm_disabled_column_from_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('account', 'disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('account', 'disabled', $this->boolean());
    }

}
