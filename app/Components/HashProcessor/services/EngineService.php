<?php
namespace App\Components\HashProcessor\services;

class EngineService {

	/**
	 * @var WalletService
	 */
	private $_walletService;

	public function __construct(
		WalletService $walletService
	) {
		$this->_walletService = $walletService;
	}

	/**
	 * @param string $address
	 * @param string $currency
	 * @return bool
	 */
	public function isAddressValid($address, $currency) {
		$existingAddress = $this->_walletService->getStaticWalletAddresses($currency, $address);
		return $existingAddress->count() !== 0;
	}

	/**
	 * @param User $user
	 * @param array $addresses
	 * @return array
	 */
	public function getUserExistingAddress(User $user, array $addresses) {
		$userExistingAddresses = $this->_walletService->getUserWalletAddresses($user);

		return array_intersect($userExistingAddresses, $addresses);
	}
}