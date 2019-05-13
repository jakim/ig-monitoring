<?php

use yii\db\Migration;

/**
 * Handles adding stats to table `{{%tag}}`.
 */
class m190513_111455_add_stats_columns_to_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tag', 'media', $this->integer());
        $this->addColumn('tag', 'likes', $this->integer());
        $this->addColumn('tag', 'min_likes', $this->integer());
        $this->addColumn('tag', 'max_likes', $this->integer());
        $this->addColumn('tag', 'comments', $this->integer());
        $this->addColumn('tag', 'min_comments', $this->integer());
        $this->addColumn('tag', 'max_comments', $this->integer());
        $this->addColumn('tag', 'stats_updated_at', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tag', 'media');
        $this->dropColumn('tag', 'likes');
        $this->dropColumn('tag', 'min_likes');
        $this->dropColumn('tag', 'max_likes');
        $this->dropColumn('tag', 'comments');
        $this->dropColumn('tag', 'min_comments');
        $this->dropColumn('tag', 'max_comments');
        $this->dropColumn('tag', 'stats_updated_at');
    }
}
