<?php

use yii\db\Migration;

/**
 * Handles the creation of table `proxy`.
 */
class m180111_105940_create_proxy_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('proxy', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(),
            'port' => $this->integer(),
            'username' => $this->string(),
            'password' => $this->string(),
            'active' => $this->boolean(),
            'type' => $this->string(),
            'updated_at' => $this->dateTime(),
            'created_at' => $this->dateTime(),
        ]);

        $this->addColumn('account', 'proxy_id', $this->integer());
        $this->addForeignKey('fk_account_proxy', 'account', 'proxy_id', 'proxy', 'id', 'SET NULL');

        $this->addColumn('media', 'proxy_id', $this->integer());
        $this->addForeignKey('fk_media_proxy', 'media', 'proxy_id', 'proxy', 'id', 'SET NULL');

        $this->addColumn('tag', 'proxy_id', $this->integer());
        $this->addForeignKey('fk_tag_proxy', 'tag', 'proxy_id', 'proxy', 'id', 'SET NULL');

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_account_proxy', 'account');
        $this->dropForeignKey('fk_media_proxy', 'media');
        $this->dropForeignKey('fk_tag_proxy', 'tag');
        $this->dropTable('proxy');
    }
}
