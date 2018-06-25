<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.06.2018
 */

namespace app\components\instagram\contracts;


use app\components\instagram\models\Account;

interface AccountScraperInterface
{
    /**
     * @param string $ident username or id
     * @return \app\components\instagram\models\Account|null
     */
    public function fetchOne(string $ident): ?Account;

    /**
     * @param string $username
     * @return \app\components\instagram\models\Post[]
     */
    public function fetchLastPosts(string $username): array;
}