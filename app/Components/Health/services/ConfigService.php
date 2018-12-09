<?php
/**
 * Created by PhpStorm.
 * User: bazil
 * Date: 09.02.18
 * Time: 18:37
 */

namespace App\Components\Health\services;


use App\Components\Health\exceptions\IncorrectConfigSignException;
use App\Components\Health\exceptions\UnknownConfigException;
use App\Services\Repository\Main;
use App\Services\Repository\Security;
use App\Services\Repository\SettingsRepository;

class ConfigService {

	/**
	 * @var Security
	 */
	private $_securityRepository;

	/**
	 * @var SettingsRepository
	 */
	private $_settingsRepository;

	/**
	 * ConfigService constructor.
	 * @param Security $securityRepository
	 * @param Main $main
	 */
	public function __construct(
		Security $securityRepository,
		Main $main
	) {
		$this->_securityRepository = $securityRepository;
		$this->_settingsRepository = resolve('\App\Services\Repository\SettingsRepository');
	}

	/**
	 * @throws IncorrectConfigSignException
	 * @throws UnknownConfigException
	 */
	public function precessList() {
		$dbList = $this->_settingsRepository->loadConfigList();
		$currentConfigs = $this->_loadLocalConfigs();

		$dbArray = [];
		foreach ($dbList as $dbItem) {
			$dbArray[$dbItem->config] = $dbItem;
		}

		foreach ($currentConfigs as $currentConfig) {
			if (!array_key_exists($currentConfig, $dbArray)) {
				throw new UnknownConfigException('Config ' . $currentConfig . ' unknown');
			}

			if ($dbArray[$currentConfig]->token !== $this->_getHashFromFile($currentConfig)) {
				throw new IncorrectConfigSignException('Incorrect sign for ' . $currentConfig);
			}
		}
	}

	/**
	 * @param string $fileName
	 * @return string
	 */
	private function _getHashFromFile($fileName) {
		return md5_file(config_path() . DIRECTORY_SEPARATOR . $fileName);
	}

	/**
	 * @return array
	 */
	private function _loadLocalConfigs() {
		$configPath = config_path();
		$configs = preg_grep('/^([^.])/', scandir($configPath));
		return $configs;
	}
}