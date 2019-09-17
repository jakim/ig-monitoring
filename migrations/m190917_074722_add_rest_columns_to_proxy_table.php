<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%proxy}}`.
 */
class m190917_074722_add_rest_columns_to_proxy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proxy', 'rests', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('proxy', 'rest_until', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('proxy', 'rests');
        $this->dropColumn('proxy', 'rest_until');
    }
}
