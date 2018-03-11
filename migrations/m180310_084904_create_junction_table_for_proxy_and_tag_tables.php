<?php

use yii\db\Migration;

/**
 * Handles the creation of table `proxy_tag`.
 * Has foreign keys to the tables:
 *
 * - `proxy`
 * - `tag`
 */
class m180310_084904_create_junction_table_for_proxy_and_tag_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('proxy_tag', [
            'proxy_id' => $this->integer(),
            'tag_id' => $this->integer(),
            'PRIMARY KEY(proxy_id, tag_id)',
        ]);

        // creates index for column `proxy_id`
        $this->createIndex(
            'idx-proxy_tag-proxy_id',
            'proxy_tag',
            'proxy_id'
        );

        // add foreign key for table `proxy`
        $this->addForeignKey(
            'fk-proxy_tag-proxy_id',
            'proxy_tag',
            'proxy_id',
            'proxy',
            'id',
            'CASCADE'
        );

        // creates index for column `tag_id`
        $this->createIndex(
            'idx-proxy_tag-tag_id',
            'proxy_tag',
            'tag_id'
        );

        // add foreign key for table `tag`
        $this->addForeignKey(
            'fk-proxy_tag-tag_id',
            'proxy_tag',
            'tag_id',
            'tag',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `proxy`
        $this->dropForeignKey(
            'fk-proxy_tag-proxy_id',
            'proxy_tag'
        );

        // drops index for column `proxy_id`
        $this->dropIndex(
            'idx-proxy_tag-proxy_id',
            'proxy_tag'
        );

        // drops foreign key for table `tag`
        $this->dropForeignKey(
            'fk-proxy_tag-tag_id',
            'proxy_tag'
        );

        // drops index for column `tag_id`
        $this->dropIndex(
            'idx-proxy_tag-tag_id',
            'proxy_tag'
        );

        $this->dropTable('proxy_tag');
    }
}
