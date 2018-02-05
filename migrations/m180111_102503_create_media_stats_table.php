<?php

use yii\db\Migration;

/**
 * Handles the creation of table `media_stats`.
 */
class m180111_102503_create_media_stats_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('media_stats', [
            'id' => $this->primaryKey(),
            'media_id' => $this->integer(),
            'likes' => $this->integer(),
            'comments' => $this->integer(),
            'account_followed_by' => $this->integer(),
            'account_follows' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk_media_stats_media', 'media_stats', 'media_id', 'media', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_media_stats_media', 'media_stats');
        $this->dropTable('media_stats');
    }
}
