<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.06.2018
 */

namespace app\components\instagram\contracts;


use app\components\instagram\models\Tag;

interface TagScraperInterface
{
    public function fetchOne(string $name): ?Tag;

    /**
     * @param string $name
     * @return \app\components\instagram\models\Post[]|array
     */
    public function fetchTopPosts(string $name): array;
}