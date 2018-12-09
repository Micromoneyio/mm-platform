<?php
/**
 * @author  Vasiliy Shibanov <sv@profgalery.ru>
 * @company ProfGallery
 */

namespace App\Providers;

use App\Services\Repository\Main;
use App\Services\Repository\PurchaseRepository;
use App\Services\Repository\Security;
use App\Services\Repository\UserRepository;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

/**
 * Class UserServiceProvider
 * @package App\Providers
 * @codeCoverageIgnore
 */
class UserServiceProvider extends ServiceProvider {

	/**
	 * Provide User Service
	 */
	public function register() {
		$this->app->singleton('\App\Services\UserService', function(
		    Main $repository,
            Security $security,
            UserRepository $userRepository,
            PurchaseRepository $purchaseRepository
        ) {
			return new UserService($repository, $security, $userRepository, $purchaseRepository);
		});
	}
}