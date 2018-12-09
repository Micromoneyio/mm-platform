<?php
/**
 * Created by PhpStorm.
 * User: bazil
 * Date: 22.03.18
 * Time: 19:55
 */

namespace App\Providers;


use App\Services\StatisticService;
use Barryvdh\Debugbar\ServiceProvider;

/**
 * Class StatisticServiceProvider
 * @package App\Providers
 */
class StatisticServiceProvider extends ServiceProvider {

    /**
     * Register statistic service
     */
    public function register() {
        $this->app->singleton('\App\Services\SettingsService', function (){
            return new StatisticService();
        });
    }

}