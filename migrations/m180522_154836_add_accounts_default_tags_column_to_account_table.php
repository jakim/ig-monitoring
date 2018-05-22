<?php

use yii\db\Migration;

/**
 * Handles adding accounts_default_tags to table `account`.
 */
class m180522_154836_add_accounts_default_tags_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'accounts_default_tags', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'accounts_default_tags');
    }
}
