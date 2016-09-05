<?php

class Helpers_General_GoogleAnalytics
{
    /** @var Google_Service_Analytics $analytics */
    private $analytics;
    private $profileId;

    private $startDate;
    private $endDate;

    public function __construct()
    {
        $config = Zend_Registry::get('config');

        $client = new Google_Client();
        $client->setApplicationName("photoprint analytics");
        $this->analytics = new Google_Service_Analytics($client);

        // Read the generated client_secrets.p12 key.
        $key = file_get_contents($config->statistic->keyFileLocation);
        $cred = new Google_Auth_AssertionCredentials(
            $config->statistic->serviceAccountEmail,
            array(Google_Service_Analytics::ANALYTICS_READONLY),
            $key
        );
        $client->setAssertionCredentials($cred);

        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }

        // Get the user's first view (profile) ID.
        // Get the list of accounts for the authorized user.
        $accounts = $this->analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            $firstAccountId = $items[0]->getId();

            // Get the list of properties for the authorized user.
            $properties = $this->analytics->management_webproperties
                ->listManagementWebproperties($firstAccountId);

            if (count($properties->getItems()) > 0) {
                $items = $properties->getItems();
                $firstPropertyId = $items[0]->getId();

                // Get the list of views (profiles) for the authorized user.
                $profiles = $this->analytics->management_profiles
                    ->listManagementProfiles($firstAccountId, $firstPropertyId);

                if (count($profiles->getItems()) > 0) {
                    $items = $profiles->getItems();

                    // Return the first view (profile) ID.
                    $this->profileId = $items[0]->getId();
                } else {
                    throw new Exception('No views (profiles) found for this user.');
                }
            } else {
                throw new Exception('No properties found for this user.');
            }
        } else {
            throw new Exception('No accounts found for this user.');
        }

        $this->startDate = (new DateTime('-31 days'))->format('Y-m-d');
        $this->endDate = (new DateTime())->format('Y-m-d');
    }

    public function getVisitorsCount()
    {
        // Calls the Core Reporting API and queries for the number of sessions
        // for the last seven days.
        $results = $this->analytics->data_ga->get(
            'ga:' . $this->profileId,
            $this->startDate,
            $this->endDate,
            'ga:sessions'
        );

        if (count($results->getRows()) > 0) {
            // Get the entry for the first entry in the first row.
            $rows = $results->getRows();

            return $rows[0][0];
        } else {
            throw new Exception('No results found.\n');
        }
    }

    public function getSessionsWithEvent()
    {
        // Calls the Core Reporting API and queries for the number of sessions
        // for the last seven days.
        $results = $this->analytics->data_ga->get(
            'ga:' . $this->profileId,
            $this->startDate,
            $this->endDate,
            'ga:sessionsWithEvent'
        );

        if (count($results->getRows()) > 0) {
            // Get the entry for the first entry in the first row.
            return $results->getRows();
        } else {
            throw new Exception('No results found.\n');
        }
    }

    public function callApi($metric, $dimensions = '', $timeInterval = [])
    {
        $requestParams = [];

        if (isset($timeInterval['startDate'])) {
            $this->startDate = $timeInterval['startDate'];
        }

        if (isset($timeInterval['endDate'])) {
            $this->endDate = $timeInterval['endDate'];
        }

        if ($dimensions) {
            $requestParams['dimensions'] = $dimensions;
        }

        $results = $this->analytics->data_ga->get(
            'ga:' . $this->profileId,
            $this->startDate,
            $this->endDate,
            $metric,
            $requestParams
        );

        if (count($results->getRows()) > 0) {
            $headers = $results->getColumnHeaders();
            $data = $results->getRows();
            $sortedData = [];

            foreach ($data as $record) {
                $groupedRecord = [];

                foreach ($headers as $headerPosition => $header) {
                    $groupedRecord[$header->getName()] = $record[$headerPosition];
                }

                $sortedData[] = $groupedRecord;
            }

            return $sortedData;
        } else {
            return [];
        }
    }
}
