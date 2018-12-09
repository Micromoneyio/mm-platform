<?php
/**
 * Created by PhpStorm.
 * User: bazil
 * Date: 09.02.18
 * Time: 21:40
 */

namespace App\Components\Health\services;


use App\Models\WalletStatic;
use App\Services\Repository\Main;
use App\Services\Repository\Security;
use App\Services\Repository\WalletRepository;

class WalletService {

	/**
	 * @var Security
	 */
	private $_securityRepository;

	/**
	 * @var Main
	 */
	private $_mainRepository;

	/**
	 * @var WalletRepository
	 */
	private $_walletRepository;

	public function __construct(
		Security $security,
		Main $main
	) {
		$this->_securityRepository = $security;
		$this->_mainRepository     = $main;

		$this->_walletRepository   = resolve('\App\Services\Repository\WalletRepository');
	}

	/**
	 * Sign static wallets for pending
	 * @return bool
	 */
	public function sign() {
		$wallets = $this->_walletRepository->loadStaticWallets();
		$sign = '';
		/** @var WalletStatic $wallet */
		foreach ($wallets as $wallet) {
			$sign .= $wallet->currency . ':' . md5($wallet->currency . $wallet->address) . '|';
		}

		$sign = trim($sign, '|');

		if (!$this->_checkFile($sign)) {
			$this->_storeToFile($sign);
			return false;
		}

		return true;
	}

	/**
	 * @param string $hash
	 */
	private function _storeToFile($hash) {
		$assetPath = $this->_getFilePath();

	file_put_contents($assetPath, base64_encode($hash));
	//file_put_contents($assetPath, $hash);
	
	//chmod($assetPath, 777);
	}

	/**
	 * @param string $hash
	 * @return bool
	 */
	private function _checkFile($hash) {
		$assetPath = $this->_getFilePath();

		if(!file_exists($assetPath)) {
			return false;
		}

	$contents = file_get_contents($assetPath);
		return base64_decode($contents) === $hash;
	}

	/**
	 * @return string
	 */
	private function _getFilePath() {
		$pubicPath = public_path();
		return $pubicPath . DIRECTORY_SEPARATOR . 'customer' . DIRECTORY_SEPARATOR
			. 'images' . DIRECTORY_SEPARATOR . 'empty.png';
	}
}
