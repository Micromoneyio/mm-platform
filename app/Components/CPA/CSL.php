<?php
namespace App\Components\CPA;


class CSL {

	/**
	 * @var array
	 */
	static $_container = [];

	/**
	 * @return \App\Components\CPA\Services\RunCpaService
	 */
	public static function runCpaService() {
		return self::_factory('\App\Components\CPA\Services\RunCpaService');
	}

	/**
	 * @return \App\Components\CPA\Services\AchivaService
	 */
	public static function achivaService() {
		return self::_factory('\App\Components\CPA\Services\AchivaService');
	}

	/**
	 * @param $serviceName
	 * @return mixed
	 */
	private static function _factory($serviceName) {
		if (!array_key_exists($serviceName, self::$_container)) {
			self::$_container[$serviceName] = new $serviceName();
		}

		return self::$_container[$serviceName];
	}

}