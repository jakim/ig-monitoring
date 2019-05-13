<?php

use yii\db\Migration;

/**
 * Class m190513_121436_rm_proxy_tag_ig_column
 */
class m190513_121436_rm_proxy_tag_ig_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_account_proxy_tag', 'account');
        $this->dropColumn('account', 'proxy_tag_id');

        $this->dropForeignKey('fk_tag_proxy_tag', 'tag');
        $this->dropColumn('tag', 'proxy_tag_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('account', 'proxy_tag_id', $this->integer());
        $this->addForeignKey('fk_account_proxy_tag', 'account', 'proxy_tag_id', 'tag', 'id');

        $this->addColumn('tag', 'proxy_tag_id', $this->integer());
        $this->addForeignKey('fk_tag_proxy_tag', 'tag', 'proxy_tag_id', 'tag', 'id');
    }
}
