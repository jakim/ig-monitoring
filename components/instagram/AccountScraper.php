<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\components\instagram;


use app\components\http\Client;
use app\components\http\CacheStorage;
use app\models\Account;
use app\models\Proxy;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use Jakim\Query\AccountQuery;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class AccountScraper extends Component
{
    /**
     * @var Proxy
     */
    public $proxy;

    /**
     * @var bool
     */
    public $cache = true;

    public function fetchDetails(Account $account): \Jakim\Model\Account
    {
        $query = $this->queryFactory($account);

        $ident = $account->username;
        $attempt = 0;

        while ($attempt < 2) {
            try {
                $attempt++;

                return $query->findOne($ident);

            } catch (ClientException $exception) {
                $ident = $account->instagram_id;
                if ($ident) {
                    continue;
                }
                break;
            }
        };

        $httpCode = isset($exception) ? $exception->getResponse()->getStatusCode() : null;
        if ($httpCode == 404) {
            throw new NotFoundHttpException('Account not found.');
        }

        throw $exception ?? new ServerErrorHttpException('Something is wrong!');
    }

    /**
     * @param \app\models\Account $account
     * @return \Generator|\Jakim\Model\Post[]
     * @throws \yii\base\InvalidConfigException
     */
    public function fetchMedia(Account $account)
    {
        $query = $this->queryFactory($account);

        return $query->findPosts($account->username, 10);
    }

    /**
     * @param \app\models\Account $account
     * @param string $url
     * @return null|string image data
     * @throws \yii\base\InvalidConfigException
     */
    public function fetchProfilePic(Account $account, string $url): ?string
    {
        $proxy = $this->getProxy($account);
        $client = Client::factory($proxy);

        return $client->get($url)->getBody()->getContents();
    }

    protected function getProxy(Account $account): Proxy
    {
        $proxy = $this->proxy ?: $account->proxy;
        if (!$proxy || !$proxy->active) {
            throw new InvalidConfigException('Account proxy must be set and be active.');
        }

        return $proxy;
    }

    /**
     * @param \app\models\Account $account
     * @return AccountQuery
     * @throws \yii\base\InvalidConfigException
     */
    private function queryFactory(Account $account): AccountQuery
    {
        $proxy = $this->getProxy($account);

        if ($this->cache) {
            $stack = HandlerStack::create();
            $stack->push(new CacheMiddleware(
                new GreedyCacheStrategy(
                    new CacheStorage(), 3600)
            ), 'cache');
            $client = Client::factory($proxy, ['handler' => $stack]);
        } else {
            $client = Client::factory($proxy);
        }

        $query = new AccountQuery($client);

        return $query;
    }
}