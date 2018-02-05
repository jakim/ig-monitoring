<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tag`.
 */
class m180111_103924_create_tag_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tag', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->unique(),
            'slug' => $this->string(),
            'main_tag_id' => $this->integer(),
            'updated_at' => $this->dateTime(),
            'created_at' => $this->dateTime(),
            'monitoring' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('fk_tag_tag', 'tag', 'main_tag_id', 'tag', 'id', 'SET NULL');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_tag_tag', 'tag');
        $this->dropTable('tag');
    }
}
