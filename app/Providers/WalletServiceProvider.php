<?php
/**
 * Created by PhpStorm.
 * User: bazil
 * Date: 04.02.18
 * Time: 15:28
 */

namespace App\Providers;

use App\Services\Analytics\AnalyticsService;
use App\Services\BonusService;
use App\Services\RateService;
use App\Services\Repository\Main;
use App\Services\Repository\PurchaseRepository;
use App\Services\Repository\Security;
use App\Services\Repository\UserRepository;
use App\Services\Repository\WalletRepository;
use App\Services\WalletService;
use Illuminate\Support\ServiceProvider;

/**
 * Class WalletServiceProvider
 * @package App\Providers
 * @codeCoverageIgnore
 */
class WalletServiceProvider extends ServiceProvider {

    /**
     * Provide User Service
     */
    public function register() {
        $this->app->singleton('\App\Services\WalletService', function(
            Main $repository,
            RateService $rateService,
            Security $security,
            BonusService $bonusService,
            AnalyticsService $analyticsService,
            UserRepository $userRepository,
            PurchaseRepository $purchaseRepository,
            WalletRepository $walletRepository
        ) {
            return new WalletService(
                $repository,
                $rateService,
                $security,
                $bonusService,
                $analyticsService,
                $userRepository,
                $purchaseRepository,
                $walletRepository
            );
        });
    }

}