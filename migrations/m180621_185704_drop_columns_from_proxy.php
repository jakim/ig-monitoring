<?php

use yii\db\Migration;

/**
 * Class m180621_185704_drop_columns_from_proxy
 */
class m180621_185704_drop_columns_from_proxy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('proxy', 'default_for_accounts');
        $this->dropColumn('proxy', 'default_for_tags');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('proxy', 'default_for_accounts', $this->boolean());
        $this->addColumn('proxy', 'default_for_tags', $this->boolean());
    }
}
