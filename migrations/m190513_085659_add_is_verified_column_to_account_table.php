<?php

use yii\db\Migration;

/**
 * Handles adding is_verified to table `{{%account}}`.
 */
class m190513_085659_add_is_verified_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%account}}', 'is_verified', $this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%account}}', 'is_verified');
    }
}
