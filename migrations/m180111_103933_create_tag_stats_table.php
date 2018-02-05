<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tag_stats`.
 */
class m180111_103933_create_tag_stats_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tag_stats', [
            'id' => $this->primaryKey(),
            'tag_id' => $this->integer(),
            'media' => $this->integer(),
            'likes' => $this->integer(),
            'comments' => $this->integer(),
            'min_likes' => $this->integer(),
            'max_likes' => $this->integer(),
            'min_comments' => $this->integer(),
            'max_comments' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk_tag_stats_tag', 'tag_stats', 'tag_id', 'tag', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_tag_stats_tag', 'tag_stats');
        $this->dropTable('tag_stats');
    }
}
