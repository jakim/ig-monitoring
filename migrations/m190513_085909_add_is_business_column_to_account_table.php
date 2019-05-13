<?php

use yii\db\Migration;

/**
 * Handles adding is_business to table `{{%account}}`.
 */
class m190513_085909_add_is_business_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%account}}', 'is_business', $this->boolean()->defaultValue(0));
        $this->addColumn('{{%account}}', 'business_category', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%account}}', 'is_business');
        $this->dropColumn('{{%account}}', 'business_category');
    }
}
