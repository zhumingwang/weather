<?php
namespace Zhuzi\Weather;

use GuzzleHttp\Client;
use Zhuzi\Weather\Exceptions\InvalidArgumentException;
use Zhuzi\Weather\Exceptions\HttpException;

class Weather
{

	protected $key = '';

	protected $uri = 'https://restapi.amap.com/v3/weather/weatherInfo?';

	protected $guzzleOptions = [];

	function __construct($key)
	{
		$this->key = $key;
	}

	public function getWeather($city, $type = 'base', $format = 'json')
	{
		if(!in_array(strtolower($type), ['base', 'all'])){
			throw new InvalidArgumentException("Invalid type value(base/all): ".$type);
		}

		if(!in_array(strtolower($format), ['xml', 'json'])){
			throw new InvalidArgumentException("Invalid return format(json/xml): ".$format);
		}

		$query = [
			'key' => $this->key,
			'city' => $city,
			'extensions' => $type,
			'output' => $format,
		];

		try {
			$response = $this->getClient()->get($this->uri, [
				'query' => $query,
			])->getBody()->getContents();

			return $format === 'json' ? json_decode($response, true) : $response;
		} catch (\Exception $e) {
			throw new HttpException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function getGuzzleOptions()
	{
		return $this->guzzleOptions;
	}

	public function setGuzzleOptions(array $options)
	{
		$this->guzzleOptions = $options;
	}

	public function getClient()
	{
		return new Client($this->guzzleOptions);
	}
}