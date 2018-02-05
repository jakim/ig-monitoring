<?php

use yii\db\Migration;

/**
 * Handles the creation of table `media_tag`.
 * Has foreign keys to the tables:
 *
 * - `media`
 * - `tag`
 */
class m180111_105152_create_junction_table_for_media_and_tag_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('media_tag', [
            'media_id' => $this->integer(),
            'tag_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'PRIMARY KEY(media_id, tag_id)',
        ]);

        // creates index for column `media_id`
        $this->createIndex(
            'idx-media_tag-media_id',
            'media_tag',
            'media_id'
        );

        // add foreign key for table `media`
        $this->addForeignKey(
            'fk-media_tag-media_id',
            'media_tag',
            'media_id',
            'media',
            'id',
            'CASCADE'
        );

        // creates index for column `tag_id`
        $this->createIndex(
            'idx-media_tag-tag_id',
            'media_tag',
            'tag_id'
        );

        // add foreign key for table `tag`
        $this->addForeignKey(
            'fk-media_tag-tag_id',
            'media_tag',
            'tag_id',
            'tag',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `media`
        $this->dropForeignKey(
            'fk-media_tag-media_id',
            'media_tag'
        );

        // drops index for column `media_id`
        $this->dropIndex(
            'idx-media_tag-media_id',
            'media_tag'
        );

        // drops foreign key for table `tag`
        $this->dropForeignKey(
            'fk-media_tag-tag_id',
            'media_tag'
        );

        // drops index for column `tag_id`
        $this->dropIndex(
            'idx-media_tag-tag_id',
            'media_tag'
        );

        $this->dropTable('media_tag');
    }
}
