<?php
namespace App\Components\Analytics;


class CSL {

	/**
	 * @var array
	 */
	static $_container = [];

	/**
	 * @return \App\Components\Analytics\services\GoogleAnalyticsService
	 */
	public static function googleAnalyticsService() {
		return self::_factory('\App\Components\Analytics\services\GoogleAnalyticsService');
	}

	/**
	 * @param string $serviceName
	 * @return mixed
	 */
	private static function _factory($serviceName) {
		if (!array_key_exists($serviceName, self::$_container)) {
			self::$_container[$serviceName] = new $serviceName();
		}

		return self::$_container[$serviceName];
	}
}