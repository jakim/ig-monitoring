<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-02-12
 */

namespace app\components\traits;



trait BatchInsertCommandTrait
{
    /**
     * @param string $table
     * @param array $columns
     * @param array $rows
     * @return \yii\db\Command
     */
    public function batchInsertIgnoreCommand(string $table, array $columns, array $rows)
    {
        $sql = \Yii::$app->db->queryBuilder
            ->batchInsert($table, $columns, $rows);
        $sql = str_replace('INSERT INTO ', 'INSERT IGNORE INTO ', $sql);

        return \Yii::$app->db->createCommand($sql);
    }
}