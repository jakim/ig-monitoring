<?php

use yii\db\Migration;

/**
 * Handles adding last_post_taken_at to table `{{%account}}`.
 */
class m190513_090649_add_last_post_taken_at_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%account}}', 'last_post_taken_at', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%account}}', 'last_post_taken_at');
    }
}
