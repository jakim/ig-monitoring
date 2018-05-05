<?php

use yii\db\Migration;

/**
 * Handles adding disabled to table `account`.
 */
class m180505_134122_add_disabled_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'disabled', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'disabled');
    }
}
