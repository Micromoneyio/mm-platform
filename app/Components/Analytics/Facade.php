<?php
namespace App\Components\Analytics;


class Facade {

	/**
	 * @param string      $startDate
	 * @param string      $endDate
	 * @param string|null $dimension
	 * @return array
	 * @throws exceptions\GoogleAPIConnectionException
	 */
	public static function getUserStatistics($startDate, $endDate, $dimension = 'ga:date') {
		return CSL::googleAnalyticsService()->getUserStatistics($startDate, $endDate, $dimension);
	}

}