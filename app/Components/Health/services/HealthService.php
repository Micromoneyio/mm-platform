<?php
/**
 * Created by PhpStorm.
 * User: bazil
 * Date: 09.02.18
 * Time: 20:24
 */

namespace App\Components\Health\services;


use App\Models\HealthLog;
use App\Services\Repository\Main;
use App\Services\Repository\Security;
use App\Services\Repository\SettingsRepository;

class HealthService {

	/**
	 * @var Security
	 */
	private $_securityRepository;

	/**
	 * @var SettingsRepository
	 */
	private $_settingsRepository;

	/**
	 * HealthService constructor.
	 * @param Security $security
	 * @param Main $main
	 */
	public function __construct(
		Security $security,
		Main $main
	) {
		$this->_securityRepository = $security;
		$this->_settingsRepository = resolve('\App\Services\Repository\SettingsRepository');
	}

	/**
	 * @param string $chanelName
	 * @param string $chanelStatus
	 * @return bool
	 */
	public function updateChanelStatus($chanelName, $chanelStatus) {
		/** @var HealthLog $lastChanelLog */
		$lastChanelLog = $this->_settingsRepository->loadLastHealthLog($chanelName);
		if ($lastChanelLog === null || $lastChanelLog->status !== $chanelStatus) {
			$lastChanelLog = new HealthLog();
			$lastChanelLog->status = $chanelStatus;
			$lastChanelLog->chanel = $chanelName;
			$this->_settingsRepository->storeHealthLog($lastChanelLog);
		}

		return true;
	}
}