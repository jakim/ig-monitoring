<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tag_invalidation_type`.
 */
class m180830_063332_create_tag_invalidation_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tag_invalidation_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->insert('tag_invalidation_type', [
            'id' => 1,
            'name' => 'Not found',
        ]);

        $this->addColumn('tag', 'is_valid', $this->boolean()->notNull()->defaultValue(1));

        $this->addColumn('tag', 'invalidation_type_id', $this->integer());
        $this->addForeignKey('fk_tag_invalidation_type', 'tag', 'invalidation_type_id', 'tag_invalidation_type', 'id', 'SET NULL');

        $this->addColumn('tag', 'invalidation_count', $this->integer());
        $this->addColumn('tag', 'update_stats_after', $this->dateTime());
        $this->addColumn('tag', 'disabled', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tag', 'is_valid');

        $this->dropForeignKey('fk_tag_invalidation_type', 'tag');
        $this->dropColumn('tag', 'invalidation_type_id');
        $this->dropTable('tag_invalidation_type');

        $this->dropColumn('tag', 'invalidation_count');
        $this->dropColumn('tag', 'update_stats_after');
        $this->dropColumn('tag', 'disabled');
    }
}
