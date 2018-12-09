<?php
namespace App\Components\HashProcessor\models;

/**
 * Class TxModel
 * @package App\Components\HashProcessor\models
 */
class TxModel {
	/**
	 * @var string
	 */
	public $txId;

	/**
	 * @var string
	 */
	public $blockHash;

	/**
	 * @var int
	 */
	public $confirmations;

	/**
	 * @var int
	 */
	public $time;

	/**
	 * @var int
	 */
	public $size;

	/**
	 * @var string
	 */
	public $addressTo;

	/**
	 * @var float
	 */
	public $value;

	/**
	 * @var array
	 */
	public $addressesFrom;

	/**
	 * @var string
	 */
	public $currency;

	/**
	 * TxModel constructor.
	 * @param string        $txId
	 * @param string        $blockHash
	 * @param int           $confirmations
	 * @param int           $time
	 * @param int           $size
	 * @param string        $addressTo
	 * @param string        $value
	 * @param array         $addressesFrom
	 * @param string        $currency
	 */
	public function __construct(
		$txId,
		$blockHash,
		$confirmations,
		$time,
		$size,
		$addressTo,
		$value,
		array $addressesFrom = [],
		$currency = null
	) {
		$this->txId            = $txId;
		$this->blockHash       = $blockHash;
		$this->confirmations   = $confirmations;
		$this->time            = $time;
		$this->size            = $size;
		$this->addressTo       = $addressTo;
		$this->value           = $value;
		$this->addressesFrom   = $addressesFrom;
		$this->currency        = $currency;
	}
}