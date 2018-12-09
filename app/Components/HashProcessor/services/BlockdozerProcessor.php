<?php
namespace App\Components\HashProcessor\services;


/**
 * Class BlockdozerProcessor
 * @package App\Components\HashProcessor\services
 */
class BlockdozerProcessor {

	const BLOCKDOZER_ADDRESS_URL = 'https://bch.blockdozer.com/insight-api/txs/?address=';

	const BLOCKDOZER_TX_URL = 'https://www.blockdozer.com/insight-api/tx/';

	/**
	 * @param string $address
	 * @param string $network
	 * @return array of TxModel
	 * @throws ResponseDoesNotExist
	 */
	public function getTransactionsFromAddress($address, $network = 'BCH') {
		try {
			$response = Curl::to(self::BLOCKDOZER_ADDRESS_URL . $address)
				->withTimeout(2)->asJsonResponse()->get();

			$result = [];
			foreach ($response->txs as $tx) {
				$outAddresses = [];
				foreach ($tx->vout as $vout) {
					$outAddresses = array_merge($outAddresses, $vout->scriptPubKey->addresses);
				}

				if (!in_array($address, $outAddresses)) {
					continue;
				}

				$vinAmount = 0;
				foreach ($tx->vin as $vin) {
					$vinAmount += $vin->value;
				}

				$txModel = new TxModel(
					$tx->txid,
					$tx->blockhash,
					$tx->confirmations,
					$tx->time,
					$tx->size,
					$address,
					$vinAmount,
					[],
					$network
				);

				$result[] = $txModel;
			}

			return $result;
		} catch (\Exception $exception) {
			throw new ResponseDoesNotExist($exception->getMessage());
		}
	}

	/**
	 * @param string $txId
	 * @param string $network
	 * @return TxModel
	 * @throws ResponseDoesNotExist
	 * @throws TxDoesNotExistException
	 */
	public function getTxInputs($txId, $network = 'BCH') {
		try {
			$response = Curl::to(self::BLOCKDOZER_TX_URL . $txId)
				->withTimeout(5)->asJsonResponse()->get();
		} catch (\Exception $exception) {
			throw new ResponseDoesNotExist('Tx block information not accessible');
		}

		$address = false;
		$value = false;
		foreach ($response->vout as $output) {
			foreach ($output->scriptPubKey->addresses as $outAddress){
				if (CSL::engineService()->isAddressValid($outAddress, $network)) {
					$address = $outAddress;
					$value = floatval($output->value);
				}
			}
		}

		if ($address === false || $value === false) {
			throw new TxDoesNotExistException('Tx with hash ' . $txId . ' does not exist');
		}

		$inputAddresses = [];
		foreach ($response->vin as $input) {
			$inputAddresses[] = strtoupper($input->addr);
		}

		return new TxModel(
			$response->txid,
			$response->blockhash,
			$response->confirmations,
			$response->time,
			$response->size,
			$address,
			$value,
			$inputAddresses,
			$network
		);
	}
}