<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.05.2018
 */

namespace app\modules\api\v1\controllers;


use app\models\Tag;
use app\modules\api\v1\components\ActiveController;
use app\modules\api\v1\models\Account;
use app\modules\api\v1\models\AccountSearchForm;
use yii\data\ActiveDataFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\rest\IndexAction;
use yii\web\ServerErrorHttpException;

class AccountController extends ActiveController
{
    public $modelClass = Account::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);

        $actions['index'] = [
            'class' => IndexAction::class,
            'modelClass' => $this->modelClass,
            'dataFilter' => [
                'class' => ActiveDataFilter::class,
                'searchModel' => AccountSearchForm::class,
            ],
        ];

        return $actions;
    }

    public function actionCreate()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams();

        $username = ArrayHelper::getValue($bodyParams, 'username');
        $tags = ArrayHelper::remove($bodyParams, 'tags', '');

        $account = $this->findOrCreateModel($username);
        $isNewRecord = $account->isNewRecord;

        $account->load($bodyParams, '');
        if ($account->save()) {
            $this->linkTags($tags, $account);

            $response = \Yii::$app->getResponse();
            $response->setStatusCode($isNewRecord ? 201 : 200);

            // TODO uncomment after create "view" endpoint
//            $id = implode(',', array_values($model->getPrimaryKey(true)));
//            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));

        } elseif (!$account->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $account;
    }

    protected function linkTags(string $tags, Account $account): void
    {
        $tags = StringHelper::explode($tags, ',', true, true);
        $tags = array_unique($tags);

        foreach ($tags as $name) {
            try {
                $tag = Tag::findOne(['slug' => Inflector::slug($name)]);
                if ($tag === null) {
                    $tag = new Tag();
                    $tag->name = $name;
                    $tag->insert();
                }
                $account->link('tags', $tag);
            } catch (\Throwable $exception) {
                \Yii::error(sprintf('API: account tag error: %s', $exception->getMessage()), __METHOD__);
                continue;
            }
        }
    }

    /**
     * @param $username
     * @return \app\modules\api\v1\models\Account|null
     */
    protected function findOrCreateModel($username)
    {
        $account = Account::findOne(['username' => $username]);

        if ($account === null) {
            $account = new Account();
            $account->setScenario($this->createScenario);
        }

        return $account;
    }
}