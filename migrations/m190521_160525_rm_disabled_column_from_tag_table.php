<?php

use yii\db\Migration;

/**
 * Class m190521_160525_rm_disabled_column_from_tag_table
 */
class m190521_160525_rm_disabled_column_from_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('tag', 'disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('tag', 'disabled', $this->boolean());
    }
}
