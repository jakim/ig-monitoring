<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 11.06.2018
 */

namespace app\components\instagram\contracts;


use app\components\instagram\models\Post;

interface MediaScraperInterface
{
    public function fetchOne(string $shortcode): ?Post;
}