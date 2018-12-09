<?php
namespace App\Components\CPA;

/**
 * Class Facade
 * @package App\Components\CPA
 */
class Facade {

	/**
	 * @param User $user
	 */
	public static function sendRegistration(User $user) {
		$provider = $user->cpa_provider;
		switch ($provider) {
			case 'achiva':
				CSL::achivaService()->sendRegistration($user);
				break;
			case 'runcpa':
			default:
				CSL::runCpaService()->sendRegistration($user);
				break;
		}
	}

	/**
	 * @param User $user
	 * @param Purchase $purchase
	 */
	public static function sendPurchase(User $user, Purchase $purchase) {
		$provider = $user->cpa_provider;
		switch ($provider) {
			case 'achiva':
				CSL::achivaService()->sendPurchase($purchase, $user);
				break;
			case 'runcpa':
			default:
				CSL::runCpaService()->sendPurchase($purchase, $user);
				break;
		}
	}
}