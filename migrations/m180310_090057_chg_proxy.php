<?php

use yii\db\Migration;

/**
 * Class m180310_090057_chg_proxy
 */
class m180310_090057_chg_proxy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('proxy', 'ip', $this->string()->notNull());
        $this->alterColumn('proxy', 'port', $this->integer()->notNull());
        $this->alterColumn('proxy', 'active', $this->boolean()->notNull()->defaultValue(1));

        $this->addColumn('proxy', 'default_for_accounts', $this->boolean()->notNull()->defaultValue('0'));
        $this->addColumn('proxy', 'default_for_tags', $this->boolean()->notNull()->defaultValue('0'));

        $this->dropColumn('proxy', 'type');

        $this->addColumn('account', 'proxy_tag_id', $this->integer()->after('proxy_id'));
        $this->addForeignKey('fk_account_proxy_tag', 'account', 'proxy_tag_id', 'tag', 'id', 'SET NULL');

        $this->addColumn('tag', 'proxy_tag_id', $this->integer()->after('proxy_id'));
        $this->addForeignKey('fk_tag_proxy_tag', 'tag', 'proxy_tag_id', 'tag', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_account_proxy_tag', 'account');
        $this->dropColumn('account', 'proxy_tag_id');

        $this->dropForeignKey('fk_tag_proxy_tag', 'tag');
        $this->dropColumn('tag', 'proxy_tag_id');

        $this->addColumn('proxy', 'type', $this->string());

        $this->dropColumn('proxy', 'default_for_accounts');
        $this->dropColumn('proxy', 'default_for_tags');
    }
}
