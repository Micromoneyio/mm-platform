<?php
namespace App\Components\Api\model;

/**
 * Class PurchaseModel
 * @package App\Components\Api\model
 */
class PurchaseModel {

	/** @var int */
	public $userId;

	/** @var float */
	public $amountSent;

	/** @var float */
	public $amountReceived;

	/** @var string */
	public $currencyReceived;

	/** @var float */
	public $rate;

	/** @var string */
	public $transactionTxnId;

	/** @var string */
	public $transactionAddressFrom;

	/**
	 * @var string
	 */
	public $transactionAddressTo;

	/**
	 * @var array
	 */
	public $relations = [];
}