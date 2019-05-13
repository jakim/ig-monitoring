<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%account_category}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%account}}`
 * - `{{%category}}`
 */
class m190513_133832_create_junction_table_for_account_and_category_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('account_category', [
            'account_id' => $this->integer(),
            'category_id' => $this->integer(),
            'user_id' => $this->integer(),
            'PRIMARY KEY(account_id, category_id, user_id)',
        ]);

        // creates index for column `account_id`
        $this->createIndex(
            'idx-account_category-account_id',
            'account_category',
            'account_id'
        );

        // add foreign key for table `account`
        $this->addForeignKey(
            'fk-account_category-account_id',
            'account_category',
            'account_id',
            'account',
            'id',
            'CASCADE'
        );

        // creates index for column `category_id`
        $this->createIndex(
            'idx-account_category-category_id',
            'account_category',
            'category_id'
        );

        // add foreign key for table `category`
        $this->addForeignKey(
            'fk-account_category-category_id',
            'account_category',
            'category_id',
            'category',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-account_category-user_id',
            'account_category',
            'user_id'
        );

        $this->addForeignKey(
            'fk-account_category-user_id',
            'account_category',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `account`
        $this->dropForeignKey(
            'fk-account_category-account_id',
            'account_category'
        );

        // drops index for column `account_id`
        $this->dropIndex(
            'idx-account_category-account_id',
            'account_category'
        );

        // drops foreign key for table `category`
        $this->dropForeignKey(
            'fk-account_category-category_id',
            'account_category'
        );

        // drops index for column `category_id`
        $this->dropIndex(
            'idx-account_category-category_id',
            'account_category'
        );

        $this->dropForeignKey(
            'fk-account_category-user_id',
            'account_category'
        );

        $this->dropIndex(
            'idx-account_category-user_id',
            'account_category'
        );

        $this->dropTable('account_category');
    }
}
