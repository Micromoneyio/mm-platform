<?php
namespace App\Components\Analytics\services;

class GoogleAnalyticsService {

	/**
     * @var \Google_Client
     */
    protected $client;

    /**
     * @var \Google_Service_AnalyticsReporting
     */
    protected $analytics;

    /**
     * GAService constructor.
     *
     * @throws exceptions\CredentialsConfigNotFoundException
     * @throws exceptions\GoogleAPIConnectionException
     */
    public function __construct() {
        $credentials = db_config('ga_api.credentials');

        if (empty($credentials)) {
            throw new exceptions\CredentialsConfigNotFoundException('Google credentials config not loaded in settings');
        }

        try {
            $this->client = new \Google_Client();
            $this->client->setAuthConfig((array) $credentials);
            $this->client->addScope(\Google_Service_Analytics::ANALYTICS_READONLY);
        } catch (\Google_Exception $e) {
            throw new exceptions\GoogleAPIConnectionException('Google API connection error');
        }
    }

    /**
     * @param string      $startDate
     * @param string      $endDate
     * @param string|null $dimension
     * @return array
     * @throws exceptions\GoogleAPIConnectionException
     */
    public function getUserStatistics($startDate, $endDate, $dimension = 'ga:date') {
        $viewId = db_config('ga_api.view_id');

        if (empty($viewId)) {
            $viewId = $this->_getFirstViewId();
        }

        // Create the DateRange object.
        $dateRange = new \Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($startDate);
        $dateRange->setEndDate($endDate);

        // Create the Metrics object.
        $users = new \Google_Service_AnalyticsReporting_Metric();
        $users->setExpression("ga:users");
        $users->setAlias("sessions");

        // Create the Dimension object.
        $dimensions = new \Google_Service_AnalyticsReporting_Dimension();
        $dimensions->setName($dimension);

        // Create the OrderBy object
        $sort = new \Google_Service_AnalyticsReporting_OrderBy();
        $sort->setFieldName($dimension);

        // Create request to Google Analytics
        $request = new \Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($viewId);
        $request->setIncludeEmptyRows(true);
        $request->setDateRanges($dateRange);
        $request->setMetrics([$users]);
        $request->setDimensions([$dimensions]);
        $request->setOrderBys([$sort]);

        $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests([$request]);

        $analyticsReporting = new \Google_Service_AnalyticsReporting($this->client);
        try {
            $response = $analyticsReporting->reports->batchGet($body);
        } catch (\Exception $e) {
            throw new exceptions\GoogleAPIConnectionException('Can\'t get a report from Google Analytics.');
        }

        /** @var \Google_Service_AnalyticsReporting_Report $report */
        $report = $response->current();
        $data = $report->getData();

        $result = [];
        $rows   = $data->getRows();
        foreach ($rows as $row) {
            $dimensions = $row->getDimensions();
            $metrics    = $row->getMetrics();
            $values     = $metrics[0]->getValues();

            $result['rows'][] = [
                'value'     => $values[0],
                'dimension' => $dimensions[0]
            ];
        }

        $totals = $data->getTotals();
        $result['totalCount'] = $totals[0]->getValues()[0];

        return $result;

    }

    /**
     * Get ID of first view from Google Analytics account
     *
     * @return string
     * @throws exceptions\GoogleAPIConnectionException
     */
    protected function _getFirstViewId() {
        // Get the user's first view (profile) ID.
        $analytics = new \Google_Service_Analytics($this->client);

        // Get the list of accounts for the authorized user.
        $accounts = $analytics->management_accounts->listManagementAccounts();

        $items = $accounts->getItems();
        if (count($items) > 0) {
            $firstAccountId = $items[0]->getId();

            // Get the list of properties for the authorized user.
            $properties = $analytics->management_webproperties
                ->listManagementWebproperties($firstAccountId);

            $items = $properties->getItems();
            if (count($items) > 0) {
                $firstPropertyId = $items[0]->getId();

                // Get the list of views (profiles) for the authorized user.
                $profiles = $analytics->management_profiles
                    ->listManagementProfiles($firstAccountId, $firstPropertyId);

                $items = $profiles->getItems();
                if (count($items) > 0) {
                    // Return the first view (profile) ID.
                    return $items[0]->getId();

                } else {
                    throw new exceptions\GoogleAPIConnectionException('No views (profiles) found for this user.');
                }
            } else {
                throw new exceptions\GoogleAPIConnectionException('No properties found for this user.');
            }
        } else {
            throw new exceptions\GoogleAPIConnectionException('No accounts found for this user.');
        }
    }

}