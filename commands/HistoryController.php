<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 09.02.2018
 */

namespace app\commands;


use app\components\ArrayHelper;
use app\components\http\Client;
use app\components\MediaManager;
use app\models\Account;
use app\models\Media;
use jakim\ig\Endpoint;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\Json;

class HistoryController extends Controller
{
    public function actionAccount($username)
    {
        $account = Account::findOne(['username' => $username]);
        if ($account === null) {
            $this->stdout("Account '{$username}' not found\n", Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $url = (new Endpoint())->accountMedia($account->instagram_id, 200);

        $client = Client::factory($account->proxy);
        $res = $client->get($url);
        $content = Json::decode($res->getBody()->getContents());

        $items = ArrayHelper::getValue($content, 'data.user.edge_owner_to_timeline_media.edges', []);

        $manager = \Yii::createObject([
            'class' => MediaManager::class,
            'account' => $account,
            'propertyMap' => MediaManager::PROPERTY_MAP_ACCOUNT_MEDIA,
        ]);

        foreach ($items as $item) {
            $id = ArrayHelper::getValue($item, 'node.id');
            echo "{$id}\n";
            $media = Media::findOne(['instagram_id' => $id]);
            if ($media === null) {
                $media = new Media(['account_id' => $account->id]);
            }
            $manager->update($media, $item);
        }

    }
}