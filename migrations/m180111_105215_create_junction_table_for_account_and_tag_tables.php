<?php

use yii\db\Migration;

/**
 * Handles the creation of table `account_tag`.
 * Has foreign keys to the tables:
 *
 * - `account`
 * - `tag`
 */
class m180111_105215_create_junction_table_for_account_and_tag_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('account_tag', [
            'account_id' => $this->integer(),
            'tag_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'PRIMARY KEY(account_id, tag_id)',
        ]);

        // creates index for column `account_id`
        $this->createIndex(
            'idx-account_tag-account_id',
            'account_tag',
            'account_id'
        );

        // add foreign key for table `account`
        $this->addForeignKey(
            'fk-account_tag-account_id',
            'account_tag',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        // creates index for column `tag_id`
        $this->createIndex(
            'idx-account_tag-tag_id',
            'account_tag',
            'tag_id'
        );

        // add foreign key for table `tag`
        $this->addForeignKey(
            'fk-account_tag-tag_id',
            'account_tag',
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
        // drops foreign key for table `account`
        $this->dropForeignKey(
            'fk-account_tag-account_id',
            'account_tag'
        );

        // drops index for column `account_id`
        $this->dropIndex(
            'idx-account_tag-account_id',
            'account_tag'
        );

        // drops foreign key for table `tag`
        $this->dropForeignKey(
            'fk-account_tag-tag_id',
            'account_tag'
        );

        // drops index for column `tag_id`
        $this->dropIndex(
            'idx-account_tag-tag_id',
            'account_tag'
        );

        $this->dropTable('account_tag');
    }
}
