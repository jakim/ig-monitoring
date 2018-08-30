<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 20.08.2018
 */

namespace app\commands;


use app\dictionaries\AccountInvalidationType;
use app\dictionaries\TagInvalidationType;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\StringHelper;

class AdminController extends Controller
{
    public function actionDictionaries()
    {
        $dictionaries = [
            AccountInvalidationType::class,
            TagInvalidationType::class,
        ];

        /** @var \app\dictionaries\Dictionary $dictionary */
        foreach ($dictionaries as $dictionary) {
            $this->stdout("$dictionary\n");

            $modelClass = '\\app\\models\\' . StringHelper::basename($dictionary);
            if (!class_exists($modelClass)) {
                $this->stderr("ERR: '$modelClass'\n");

                return ExitCode::UNSPECIFIED_ERROR;
            }

            foreach ($dictionary::labels() as $id => $name) {
                /** @var \yii\db\ActiveRecord $modelClass */
                $model = $modelClass::findOne($id);
                if ($model === null) {
                    $model = new $modelClass();
                    $model->id = $id;
                }
                $model->name = $name;
                /** @var \yii\db\ActiveRecord $model */
                if (!$model->save()) {
                    echo Console::errorSummary($model);

                    return ExitCode::DATAERR;
                }
                $this->stdout("$id => $name\n");
            }
        }

    }
}