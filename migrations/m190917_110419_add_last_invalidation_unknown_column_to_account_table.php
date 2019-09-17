<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%account}}`.
 */
class m190917_110419_add_last_invalidation_unknown_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('account', 'last_invalidation_unknown', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('account', 'notes');
    }
}
