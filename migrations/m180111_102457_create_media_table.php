<?php

use yii\db\Migration;

/**
 * Handles the creation of table `media`.
 */
class m180111_102457_create_media_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('media', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'shortcode' => $this->string()->notNull()->unique(),
            'is_video' => $this->boolean(),
            'caption' => $this->text(),
            'instagram_id' => $this->string(),
            'taken_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'created_at' => $this->dateTime(),
            'monitoring' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('fk_media_account', 'media', 'account_id', 'account', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_media_account', 'media');
        $this->dropTable('media');
    }
}
