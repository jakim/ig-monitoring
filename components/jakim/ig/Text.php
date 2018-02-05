<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 12.01.2018
 */

namespace jakim\ig;


class Text
{
    public static function getTags(string $text, $minLength = 2): array
    {
        return static::extract($text, '#', $minLength);
    }

    public static function getAccounts(string $text): array
    {
        return static::extract($text, '@', false);
    }

    public static function extract(string $text, $prefix = '#', $minLength = false): array
    {
        $text = str_replace($prefix, " $prefix", $text);
        $text = preg_replace('/[\n\r\t]/', ' ', $text);

        $parts = explode(' ', $text);
        $parts = array_filter($parts, function ($part) use ($prefix) {

            return strpos($part, $prefix) === 0;
        });
        $parts = array_map(function ($tag) use ($prefix, $minLength) {
            $tag = substr($tag, strlen($prefix));
            if (is_int($minLength) && mb_strlen($tag) >= $minLength) {
                return $tag;
            }

            return $tag;
        }, $parts);

        return array_unique(array_filter($parts));
    }
}