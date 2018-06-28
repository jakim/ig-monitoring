<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.01.2018
 */

namespace app\components\instagram;


use app\components\instagram\base\Scraper;
use app\components\instagram\contracts\AccountScraperInterface;
use app\components\instagram\models\Account as IgAccount;
use Jakim\Query\AccountQuery;

class AccountScraper extends Scraper implements AccountScraperInterface
{
    /**
     * @param string $ident username or id
     * @return \app\components\instagram\models\Account|null
     */
    public function fetchOne(string $ident): ?IgAccount
    {
        $query = new AccountQuery($this->httpClient);
        $account = $query->findOne($ident);
        $profilePic = $this->fetchProfilePicIfNeeded($account->username, $account->profilePicUrl);

        $model = new IgAccount();
        $model->id = $account->id;
        $model->username = $account->username;
        $model->profilePicUrl = $profilePic;
        $model->fullName = $account->fullName;
        $model->biography = $account->biography;
        $model->externalUrl = $account->externalUrl;
        $model->followedBy = $account->followedBy;
        $model->follows = $account->follows;
        $model->media = $account->media;
        $model->isPrivate = $account->isPrivate;

        return $model;
    }

    /**
     * @param string $username
     * @return \app\components\instagram\models\Post[]
     */
    public function fetchLastPosts(string $username): array
    {
        $query = new AccountQuery($this->httpClient);
        $posts = $query->findLastPosts($username, 10);
        $posts = $this->preparePosts($posts);

        return $posts;
    }

    private function fetchProfilePicIfNeeded($username, $profilePicUrl)
    {
        $username = strtolower($username);
        $filename = sprintf('%s_%s', $username, basename($profilePicUrl));
        $path = sprintf('/uploads/%s', substr($username, 0, 2));

        $fullPath = \Yii::getAlias("@app/web/{$path}");
        @mkdir($fullPath);
        @chmod($fullPath, 0777);

        $file = "{$fullPath}/{$filename}";
        if (!file_exists($file)) {
            $content = $this->httpClient->get($profilePicUrl)->getBody()->getContents();
            file_put_contents($file, $content);
        }

        return "{$path}/{$filename}";
    }
}