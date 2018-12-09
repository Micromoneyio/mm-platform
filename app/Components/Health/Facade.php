<?php
/**
 * Created by PhpStorm.
 * User: bazil
 * Date: 09.02.18
 * Time: 20:06
 */

namespace App\Components\Health;


class Facade {

	/**
	 * @throws exceptions\IncorrectConfigSignException
	 * @throws exceptions\UnknownConfigException
	 */
	public static function checkConfigs() {
		CSL::configService()->precessList();
	}

	/**
	 * @param string $healthChanel
	 * @param mixed $healthStatus
	 */
	public static function setHealthStatus($healthChanel, $healthStatus) {
		CSL::healthService()->updateChanelStatus($healthChanel, $healthStatus);
	}

	/**
	 * @return bool
	 */
	public static function signWallets() {
		return CSL::walletService()->sign();
	}
}