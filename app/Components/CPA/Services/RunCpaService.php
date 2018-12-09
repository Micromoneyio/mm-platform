<?php
	namespace App\Components\CPA\Services;

/**
 * Class RunCpaService
 * @package App\Components\CPA\Services
 */
class RunCpaService implements CPAServiceInterface {

	const RUNCPA_CALLBACK_S2S_URL = 'http://runcpa.com/callbacks/event/s2s-partner/';
	const RUNCPA_CALLBACL_REVENUE_URL = 'http://runcpa.com/callbacks/events/revenue-partner/';

	/**
	 * Send Event to Analytics
	 *
	 * @param User $user
	 */
	public function sendRegistration(User $user) {
		$result = Curl::to(self::RUNCPA_CALLBACK_S2S_URL . config('services.runcpa.secret')
			.'/'.config('services.runcpa.registration_event_id').'/' . $user->cpa_id)->withTimeout(5)->get();

		Log::debug('RunCPA: Registration with ' . $user->cpa_id . ' ---> ' . $result);
	}

	/**
	 * Transaction to Analytics
	 *
	 * @param Purchase $purchase
	 * @param User $user
	 */
	public function sendPurchase(Purchase $purchase, User $user) {
		$result = Curl::to(self::RUNCPA_CALLBACL_REVENUE_URL . config('services.runcpa.secret')
			.'/'.config('services.runcpa.registration_purchase_id').'/' . $user->cpa_id . '/'
			. ($purchase->amount_received * $purchase->rate) . '?currency=usd')->withTimeout(5)->get();

		Log::debug('RunCPA: Purchase with ' . $user->cpa_id . '/'
			. ($purchase->amount_received * $purchase->rate) . ' ---> ' . $result);
	}
}