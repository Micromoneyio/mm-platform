<?php
namespace App\Components\HashProcessor;

class Facade {

	/**
	 * @param string $txId
	 * @param string $network
	 * @return models\TxModel
	 * @throws ObjectDoesNotExist
	 * @throws exceptions\InvalidHashFormat
	 * @throws exceptions\ResponseDoesNotExist
	 * @throws exceptions\TxDoesNotExistException
	 */
	public static function loadTxById($txId, $network = 'BTC') {
		switch (strtoupper($network)) {
			case 'ETH':
				return CSL::etherScanProcessor()->getBlockInfo($txId, false);
				break;
			case 'ETHTEST':
				return CSL::etherScanProcessor()->getBlockInfo($txId, true);
				break;
			case 'BCH':
				return CSL::blockdozerProcessor()->getTxInputs($txId, 'BCH');
			default:
				return CSL::chainSoProcessor()->getTxInputs($txId, $network);
				break;
		}
	}


	/**
	 * @param string $address
	 * @param string $currency
	 * @return bool
	 */
	public static function isWalletAddressValid($address, $currency) {
		return CSL::engineService()->isAddressValid($address, $currency);
	}

	/**
	 * @param User $user
	 * @param array $addresses
	 * @return array
	 */
	public static function getUserExistingWalletAddress(User $user, array $addresses) {
		return CSL::engineService()->getUserExistingAddress($user, $addresses);
	}

	/**
	 * @param string $address
	 * @param string $network
	 * @return services\EtherScanProcessor|array
	 * @throws exceptions\InvalidHashFormat
	 * @throws exceptions\ResponseDoesNotExist
	 * @throws exceptions\TxDoesNotExistException
	 */
	public static function loadTransactionsFromAddress($address, $network = 'BTC') {
		switch (strtoupper($network)) {
			case 'ETH':
				return CSL::etherScanProcessor()->getTransactionsFromAddress($address);
				break;
			case 'ETHTEST':
				return CSL::etherScanProcessor()->getTransactionsFromAddress($address);
				break;
			case 'BCH':
				return CSL::blockdozerProcessor()->getTransactionsFromAddress($address);
				break;
			default:
				return CSL::chainSoProcessor()->getTransactionsFromAddress($address, $network);
				break;
		}
	}
}