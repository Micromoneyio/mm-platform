<?php
namespace App\Components\CPA\Services;

/**
 * Class AchivaService
 * @package App\Components\CPA\Services
 */
class AchivaService implements CPAServiceInterface {

	const ACHIVA_POSTBACK_URL = 'https://admin.achivanetwork.com/postback';

	/**
	 * @param User $user
	 */
	public function sendRegistration(User $user) {
		$queryString = build_query([
			'goal'      => config('services.achiva.registration_goal_id'),
			'secure'    => config('services.achiva.secret'),
			'status'    => 1,
			'clickid'   => $user->cpa_id,
			'action_id' => $user->id
		]);

		$result = Curl::to(self::ACHIVA_POSTBACK_URL . '?' . $queryString)->withTimeout(5)->get();

		Log::debug('AchivaCPA: Registration with ' . $user->cpa_id . ' ---> ' . $result);
	}

	/**
	 * @param Purchase $purchase
	 * @param User $user
	 */
	public function sendPurchase(Purchase $purchase, User $user) {
		$queryString = build_query([
			'goal'      => config('services.achiva.purchase_goal_id'),
			'secure'    => config('services.achiva.secret'),
			'status'    => 2,
			'sum'       => $purchase->amount_received * $purchase->rate,
			'clickid'   => $user->cpa_id,
			'action_id' => $purchase->id
		]);

		$result = Curl::to(self::ACHIVA_POSTBACK_URL . '?' . $queryString)->withTimeout(5)->get();

		Log::debug('AchivaCPA: Purchase with ' . $purchase->id . '/' . $user->cpa_id . '/'
			. ($purchase->amount_received * $purchase->rate) . ' ---> ' . $result);
	}
}