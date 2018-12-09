<?php
/**
 * Created by PhpStorm.
 * User: bazil
 * Date: 09.02.18
 * Time: 20:02
 */

namespace App\Components\Health;


class CSL {

	/**
	 * @var array
	 */
	static $_container = [];

	/**
	 * @return \App\Components\Health\services\ConfigService
	 */
	public static function configService() {
		return self::_factory('\App\Components\Health\services\ConfigService');
	}

	/**
	 * @return \App\Components\Health\services\HealthService
	 */
	public static function healthService() {
		return self::_factory('\App\Components\Health\services\HealthService');
	}

	/**
	 * @return \App\Components\Health\services\WalletService
	 */
	public static function walletService() {
		return self::_factory('\App\Components\Health\services\WalletService');
	}

	/**
	 * @param $serviceName
	 * @return mixed
	 */
	private static function _factory($serviceName) {
		if (!array_key_exists($serviceName, self::$_container)) {
			$securityRepository = resolve('App\Services\Repository\Security');
			$mainRepository     = resolve('App\Services\Repository\Main');
			self::$_container[$serviceName] = new $serviceName($securityRepository, $mainRepository);
		}

		return self::$_container[$serviceName];
	}
}