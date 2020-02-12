<?php

/*
 * 天气接口 API https://lbs.amap.com/api/webservice/guide/api/weatherinfo/
 * */
namespace Cpken\Weather;

use GuzzleHttp\Client;
use Cpken\Weather\Exceptions\HttpException;
use Cpken\Weather\Exceptions\InvalidArgumentException;

class Weather
{
    // 天气 API key
    protected $key;
    // guzzle 实例的参数
    protected $guzzleOptions = [
        'verify' => false, //禁用 https 警告
    ];
    // 接口请求地址
    protected $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    // 获取 http client 实例
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }//getHttpClient() end

    // 自定义 guzzle 实例参数
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }//setGuzzleOptions() end

    /**
     * @desc  获取天气
     * @param $city          城市名
     * @param string $type   返回内容类型
     * @param string $format 输出的数据格式
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $format = \strtolower($format);
        $type   = \strtolower($type);

        // 1. 对 `$format` 与 `$type` 参数进行检查，不在范围内的抛出异常
        if (! \in_array($format, ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }
        if (! \in_array($type, ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): '.$type);
        }

        // 2. 封装 query 参数，并对空值进行过滤
        $query = array_filter([
            'key'        => $this->key,
            'city'       => $city,
            'output'     => $format,
            'extensions' => $type,
        ]);

        try {
            // 3. 调用 getHttpClient 获取实例，并调用该实例的 `get` 方法，
            // 传递参数为两个：$url、['query' => $query]
            $response = $this->getHttpClient()->get($this->url, [
                'query' => $query,
            ])->getBody()->getContents();

            // 4. 返回值根据 $format 返回不同的格式，
            // 当 $format 为 json 时，返回数组格式，否则为 xml
            return 'json' === $format ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            // 5. 当调用出现异常时捕获并抛出，消息为捕获到的异常消息，
            // 并将调用异常作为 $previousException 传入
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }//getWeather() end
}