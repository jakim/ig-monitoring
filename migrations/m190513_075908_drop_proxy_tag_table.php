<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%proxy_tag}}`.
 */
class m190513_075908_drop_proxy_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-proxy_tag-proxy_id', 'proxy_tag');
        $this->dropForeignKey('fk-proxy_tag-tag_id', 'proxy_tag');
        $this->dropTable('{{%proxy_tag}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->createTable('{{%proxy_tag}}', [
            'id' => $this->primaryKey(),
            'proxy_id' => $this->integer(),
            'tag_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk-proxy_tag-proxy_id', 'proxy_tag', 'proxy_id', 'proxy', 'id');
        $this->addForeignKey('fk-proxy_tag-tag_id', 'proxy_tag', 'tag_id', 'tag', 'id');
    }
}
