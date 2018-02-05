<?php

use yii\db\Migration;

/**
 * Handles the creation of table `account`.
 */
class m180110_204953_create_account_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('account', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'profile_pic_url' => $this->string(),
            'full_name' => $this->string(),
            'biography' => $this->string(),
            'external_url' => $this->string(),
            'instagram_id' => $this->string(),
            'updated_at' => $this->dateTime(),
            'created_at' => $this->dateTime(),
            'monitoring' => $this->boolean()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('account');
    }
}
