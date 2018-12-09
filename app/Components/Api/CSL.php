<?php
namespace App\Components\Api;


/**
 * Class CSL
 * @package App\Components\Integration
 */
class CSL {

	/**
	 * @var array
	 */
	static $_container = [];

	/**
	 * @return \App\Components\Api\services\UserService
	 */
	public static function userService() {
		return self::_factory('\App\Components\Api\services\UserService');
	}

	/**
	 * @return \App\Components\Api\services\WalletService
	 */
	public static function walletService() {
		return self::_factory('\App\Components\Api\services\WalletService');
	}

	/**
	 * @return \App\Components\Api\services\SiteContentService
	 */
	public static function languageService() {
		return self::_factory('\App\Components\Api\services\SiteContentService');
	}

	/**
	 * @return \App\Components\Api\services\SettingsService
	 */
	public static function settingsService() {
		return self::_factory('\App\Components\Api\services\SettingsService');
	}

	/**
	 * @return \App\Components\Api\services\NewsLineService
	 */
	public static function newsLineService() {
		return self::_factory('\App\Components\Api\services\NewsLineService');
	}

	/**
	 * @param $serviceName
	 * @return mixed
	 */
	private static function _factory($serviceName) {
		if (!array_key_exists($serviceName, self::$_container)) {
			$securityRepository = resolve('App\Services\Repository\Security');
			$mainRepository     = resolve('App\Services\Repository\Main');
			self::$_container[$serviceName] = new $serviceName($mainRepository, $securityRepository);
		}

		return self::$_container[$serviceName];
	}
}