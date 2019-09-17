<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%tag}}`.
 */
class m190917_114155_drop_proxy_id_column_from_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_tag_proxy', 'tag');
        $this->dropColumn('tag', 'proxy_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('tag', 'proxy_id', $this->integer());
        $this->addForeignKey('fk_tag_proxy', 'tag', 'proxy_id', 'proxy', 'id');
    }
}
