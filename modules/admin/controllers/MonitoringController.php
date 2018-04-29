<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 19.01.2018
 */

namespace app\modules\admin\controllers;


use app\components\AccountManager;
use app\models\Favorite;
use app\modules\admin\models\Account;
use app\modules\admin\models\AccountMonitoringForm;
use app\modules\admin\models\AccountSearch;
use app\modules\admin\models\Tag;
use app\modules\admin\models\TagMonitoringForm;
use app\modules\admin\models\TagSearch;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\web\Controller;

class MonitoringController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-account' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreateTag()
    {
        $form = new TagMonitoringForm();

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $names = StringHelper::explode($form->names, ',', true, true);
            foreach ($names as $name) {
                $tag = Tag::findOne(['slug' => Inflector::slug($name)]);
                if ($tag === null) {
                    $tag = new Tag(['name' => $name]);
                }
                $tag->proxy_id = $form->proxy_id ?: null;
                $tag->proxy_tag_id = $form->proxy_tag_id ?: null;
                $tag->monitoring = 1;
                if ($tag->save()) {
                    \Yii::$app->session->setFlash('success', 'OK!');
                } else {
                    \Yii::error('Validation error: ' . json_encode($tag->errors), __METHOD__);
                    \Yii::$app->session->setFlash('error', 'ERR!');
                }

            }
        }

        return $this->redirect(['monitoring/tags', 'sort' => '-created_at']);
    }

    public function actionCreateAccount()
    {
        $form = new AccountMonitoringForm();

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $usernames = StringHelper::explode($form->names, ',', true, true);
            foreach ($usernames as $username) {
                $account = Account::findOne(['username' => $username]);
                if ($account === null) {
                    $account = new Account(['username' => $username]);
                }
                $account->proxy_id = $form->proxy_id ?: null;
                $account->proxy_tag_id = $form->proxy_tag_id ?: null;
                $account->monitoring = 1;
                if ($account->save()) {
                    \Yii::$app->session->setFlash('success', 'OK!');
                    $accountManager = \Yii::createObject(AccountManager::class);
                    $accountManager->updateTags($account, (array) $form->tags);
                } else {
                    \Yii::error('Validation error: ' . json_encode($account->errors), __METHOD__);
                    \Yii::$app->session->setFlash('error', 'ERR!');
                }

            }
        }

        return $this->redirect(['monitoring/accounts', 'sort' => '-created_at']);
    }

    public function actionTags()
    {
        $searchModel = new TagSearch(['monitoring' => 1]);
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);

        return $this->render('tags', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAccounts()
    {

        $label = \Yii::$app->request->post('label');
        $url = \Yii::$app->request->post('url');
        if ($label && $url) {
            (new Favorite([
                'url' => $url,
                'label' => "<span class='fa fa-search'></span> $label",
            ]))->insert(false);
        }

        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['account.monitoring' => 1]);

        return $this->render('accounts', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}