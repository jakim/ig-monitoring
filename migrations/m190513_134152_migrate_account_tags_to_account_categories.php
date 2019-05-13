<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m190513_134152_migrate_account_tags_to_account_categories
 */
class m190513_134152_migrate_account_tags_to_account_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $rows = (new Query())
            ->select([
                'account_tag.account_id',
                'tag.name',
                'account_tag.user_id',
            ])
            ->from('account_tag')
            ->innerJoin('tag', 'account_tag.tag_id=tag.id')
            ->all();

        foreach ($rows as $row) {
            $category = (new Query())
                ->from('category')
                ->where(['name' => $row['name']])
                ->one();
            if (!$category) {
                $this->insert('category', [
                    'name' => $row['name'],
                ]);
                $categoryId = \Yii::$app->db->getLastInsertID();
            } else {
                $categoryId = $category['id'];
            }
            $this->insert('account_category', [
                'account_id' => $row['account_id'],
                'category_id' => $categoryId,
                'user_id' => $row['user_id'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

}
