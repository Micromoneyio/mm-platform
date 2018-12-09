<?php
namespace App\Components\CPA\Services;

/**
 * Interface CPAServiceInterface
 * @package App\Components\CPA\Services
 */
interface CPAServiceInterface {

	/**
	 * @param User $user
	 */
	public function sendRegistration(User $user);

	/**
	 * @param Purchase $purchase
	 * @param User $user
	 */
	public function sendPurchase(Purchase $purchase, User $user);
}