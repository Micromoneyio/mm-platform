<?php
namespace App\Components\Api\services;


class WalletService {

	/**
	 * @var Main
	 */
	private $_mainRepository;

	/**
	 * @var Security
	 */
	private $_securityRepository;

	/**
	 * @var UserRepository
	 */
	private $_userRepository;

	/**
	 * @var PurchaseRepository
	 */
	private $_purchaseRepository;

	/**
	 * @var PurchaseService
	 */
	private $_purchaseService;

	/**
	 * UserService constructor.
	 * @param Main $main
	 * @param Security $security
	 */
	public function __construct(
		Main $main,
		Security $security
	) {
		$this->_mainRepository      = $main;
		$this->_securityRepository  = $security;
		$this->_userRepository      = resolve('\App\Services\Repository\UserRepository');
		$this->_purchaseRepository  = resolve('\App\Services\Repository\PurchaseRepository');
		$this->_purchaseService     = resolve('\App\Services\PurchaseService');
	}

	/**
	 * @param int|null $customerId
	 * @param bool $withUser
	 * @param int|null $limit
	 * @return array
	 * @throws ObjectDoesNotExist
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public function getPurchases(
		$customerId = null,
		$withUser = false,
		$limit = null
	) {
		if ($customerId === null) {
			$purchases = $this->_purchaseRepository->loadPurchasesByParams([
				'accepted' => Purchase::PURCHASE_STATUS_ACCEPTED
			], $limit);
		} else {
			/** @var User $user */
			$user = $this->_userRepository->loadUserById((int)$customerId);
			if ($user === null) {
				throw new ObjectDoesNotExist('User does not exists');
			}

			$purchases = $this->_purchaseRepository->loadPurchasesByUser(
				$user,
				false,
				Purchase::PURCHASE_STATUS_ACCEPTED
			);
		}

		$result = [];
		foreach ($purchases as $purchase) {
			$result[] = $this->convertPurchaseEntityToModel($purchase, $withUser);
		}

		return $result;
	}

	/**
	 * @param int|null $customerId
	 * @return int
	 * @throws ObjectDoesNotExist
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public function getPurchasesCount($customerId = null) {
		if ($customerId === null) {
			$purchases = $this->_purchaseRepository->loadPurchasesByParams([
				'accepted' => Purchase::PURCHASE_STATUS_ACCEPTED
			], null);
		} else {
			/** @var User $user */
			$user = $this->_userRepository->loadUserById((int)$customerId);
			if ($user === null) {
				throw new ObjectDoesNotExist('User does not exists');
			}

			$purchases = $this->_purchaseRepository->loadPurchasesByUser(
				$user,
				false,
				Purchase::PURCHASE_STATUS_ACCEPTED
			);
		}

		return $purchases->count();
	}

	/**
	 * @param Purchase $purchase
	 * @param bool $withUser
	 * @return PurchaseModel
	 */
	public function convertPurchaseEntityToModel(Purchase $purchase, $withUser = false) {
		$model = new PurchaseModel();
		$model->userId          = (int)$purchase->user_id;
		$model->amountSent      = $purchase->amount_sent;
		$model->amountReceived  = $purchase->amount_received;
		$model->rate            = $purchase->rate;

		$transaction = $purchase->transaction->first();
		if ($transaction !== null) {
			$model->transactionTxnId        = $transaction->txn_id;
			$model->transactionAddressFrom  = $transaction->address_from;
			$model->transactionAddressTo    = $transaction->address_to;
		}

		if ($withUser) {
			$model->relations['user'] = CSL::userService()->convertUserEntityToModel($purchase->user);
		}

		return $model;
	}

	/**
	 * @param string $address
	 * @return float
	 * @throws ObjectDoesNotExist
	 */
	public function getAddressBalance($address) {
		/** @var User $user */
		$user = $this->_userRepository->loadUserByParameters([
			['output_wallet', 'LIKE', $address]
		]);

		if ($user === null) {
			throw new ObjectDoesNotExist('User with output wallet ' . $address . ' does not exist');
		}

		return $user->totalBalance()->amount;
	}

	/**
	 * @param array $ids
	 */
	public function checkPaymentAddressLogsNow($ids) {
		$addressLogs = $this->_purchaseRepository->loadPaymentsAddressLogsByParams([
			'id' => $ids
		]);

		$this->_purchaseRepository->checkPaymentAddressLogsNow($addressLogs);
	}

	/**
	 * @param array $ids
	 * @param bool  $isCheckable
	 */
	public function setPaymentAddressLogsIsCheckable($ids, $isCheckable) {
		$this->_purchaseService->setPaymentAddressLogsIsCheckable($ids, $isCheckable);
	}
}