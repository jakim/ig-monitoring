<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `media_stats`.
 */
class m180513_085059_drop_media_stats_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('media', 'likes', $this->integer());
        $this->addColumn('media', 'comments', $this->integer());
        $this->addColumn('media', 'account_followed_by', $this->integer());
        $this->addColumn('media', 'account_follows', $this->integer());

        $sql = 'SELECT * FROM media_stats WHERE id IN (SELECT MAX(id) FROM media_stats GROUP BY media_id)';
        $rows = Yii::$app->db->createCommand($sql)
            ->queryAll();

        foreach ($rows as $row) {
            $this->update('media', [
                'likes' => $row['likes'],
                'comments' => $row['comments'],
                'account_followed_by' => $row['comments'],
                'account_follows' => $row['account_follows'],
            ], ['id' => $row['media_id']]);
        }

        $this->dropForeignKey('fk_media_stats_media', 'media_stats');
        $this->dropTable('media_stats');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->createTable('media_stats', [
            'id' => $this->primaryKey(),
            'likes' => $this->integer(),
            'comments' => $this->integer(),
            'account_followed_by' => $this->integer(),
            'account_follows' => $this->integer(),
            'media_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk_media_stats_media', 'media_stats', 'media_id', 'media', 'id', 'CASCADE');

        $rows = Yii::$app->db->createCommand('SELECT * FROM media')
            ->queryAll();

        foreach ($rows as $row) {
            $this->insert('media_stats', [
                'likes' => $row['likes'],
                'comments' => $row['comments'],
                'account_followed_by' => $row['account_followed_by'],
                'account_follows' => $row['account_follows'],
                'media_id' => $row['id'],
            ]);
        }

        $this->dropColumn('media', 'likes');
        $this->dropColumn('media', 'comments');
        $this->dropColumn('media', 'account_followed_by');
        $this->dropColumn('media', 'account_follows');
    }
}
