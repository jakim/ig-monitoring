<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%account}}`.
 */
class m190917_113758_drop_proxy_id_column_from_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_account_proxy', 'account');
        $this->dropColumn('account', 'proxy_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('account', 'proxy_id', $this->integer());
        $this->addForeignKey('fk_account_proxy', 'account', 'proxy_id', 'proxy', 'id');
    }
}
