<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 26.01.2018
 */

namespace app\modules\admin\controllers;


use app\components\ArrayHelper;
use app\models\User;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\filters\VerbFilter;
use yii\web\Controller;

class AuthController extends Controller
{
    public $layout = 'main-auth';
    public $defaultAction = 'login';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        return $this->render('login');
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionInfo()
    {
        return $this->render('info');
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'authSuccess'],
            ],
        ];
    }

    public function authSuccess(ClientInterface $client)
    {
        $attributes = $client->getUserAttributes();
        $googleId = ArrayHelper::getValue($attributes, 'id');
        $email = ArrayHelper::getValue($attributes, 'email');
        $username = explode('@', $email)['0'];

        $user = User::find()
            ->andWhere([
                'google_user_id' => $googleId,
                'email' => $email,
            ])
            ->one();

        if ($user === null) {
            $user = new User([
                'google_user_id' => $googleId,
                'email' => $email,
                'username' => $username,
            ]);
            if ($user->save()) {
                $imageUrl = ArrayHelper::getValue($attributes, 'picture');
                $this->updateImage($imageUrl, $username, $user);
            }
        }

        if (!$user->active) {
            //TODO dodac info, ze admin aktywuje konto
            return $this->redirect(['auth/info']);
        }

        \Yii::$app->user->login($user);
    }

    private function updateImage(string $image, string $username, User $user): void
    {
        $content = @file_get_contents($image);
        if ($content) {
            $uid = sprintf("%s_%s", $username, md5($image));
            $ext = explode('?', pathinfo($image, PATHINFO_EXTENSION))['0'];
            $path = \Yii::getAlias("@webroot/uploads/{$uid}.{$ext}");
            if (file_put_contents($path, $content)) {
                $user->image = "/uploads/{$uid}.{$ext}";
                $user->update(false);
            }
        }
    }
}