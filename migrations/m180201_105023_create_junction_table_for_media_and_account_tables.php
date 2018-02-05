<?php

use yii\db\Migration;

/**
 * Handles the creation of table `media_account`.
 * Has foreign keys to the tables:
 *
 * - `media`
 * - `account`
 */
class m180201_105023_create_junction_table_for_media_and_account_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('media_account', [
            'media_id' => $this->integer(),
            'account_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'PRIMARY KEY(media_id, account_id)',
        ]);

        // creates index for column `media_id`
        $this->createIndex(
            'idx-media_account-media_id',
            'media_account',
            'media_id'
        );

        // add foreign key for table `media`
        $this->addForeignKey(
            'fk-media_account-media_id',
            'media_account',
            'media_id',
            'media',
            'id',
            'CASCADE'
        );

        // creates index for column `account_id`
        $this->createIndex(
            'idx-media_account-account_id',
            'media_account',
            'account_id'
        );

        // add foreign key for table `account`
        $this->addForeignKey(
            'fk-media_account-account_id',
            'media_account',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `media`
        $this->dropForeignKey(
            'fk-media_account-media_id',
            'media_account'
        );

        // drops index for column `media_id`
        $this->dropIndex(
            'idx-media_account-media_id',
            'media_account'
        );

        // drops foreign key for table `account`
        $this->dropForeignKey(
            'fk-media_account-account_id',
            'media_account'
        );

        // drops index for column `account_id`
        $this->dropIndex(
            'idx-media_account-account_id',
            'media_account'
        );

        $this->dropTable('media_account');
    }
}
