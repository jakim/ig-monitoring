<?php
/**
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.10.2017
 */

namespace jakim\ig;


class Endpoint
{
    private $baseUrl = 'https://www.instagram.com';

    public function accountDetails($username, array $params = []): string
    {
        $params['username'] = $username;
        $params['__a'] = 1;

        return $this->createUrl('/{username}/', $params);
    }

    public function accountMedia($accountId, int $first = 12, array $params = []): string
    {
        $params['query_id'] = '17888483320059182';
        $params['variables']['id'] = $accountId;
        $params['variables']['first'] = $first;

        return $this->createUrl('/graphql/query/', $params);
    }

    public function mediaDetails($code, array $params = []): string
    {
        $params['code'] = $code;
        $params['__a'] = 1;

        return $this->createUrl('/p/{code}/', $params);
    }

    public function exploreLocations($id, array $params = []): string
    {
        $params['id'] = $id;
        $params['__a'] = 1;

        return $this->createUrl('/explore/locations/{id}/', $params);
    }

    public function exploreTags($tag, array $params = []): string
    {
        $params['tag'] = $tag;
        $params['__a'] = 1;

        return $this->createUrl('/explore/tags/{tag}/', $params);
    }

    public function createUrl($endpoint, array $params = []): string
    {
        preg_match_all('/{(.+?)}/', $endpoint, $matches);
        $placeholders = array_combine($matches['0'], $matches['1']);

        array_walk($params, function ($value, $key) use (&$params) {
            $params[$key] = is_array($value) ? json_encode($value) : $value;
        });

        foreach ($placeholders as $placeholder => $key) {
            if (array_key_exists($key, $params)) {
                $value = $params[$key];
                unset($params[$key]);
                $endpoint = str_replace($placeholder, $value, $endpoint);
            }
        }

        $query = http_build_query($params);

        return "{$this->baseUrl}{$endpoint}" . ($query ? "?{$query}" : '');
    }
}