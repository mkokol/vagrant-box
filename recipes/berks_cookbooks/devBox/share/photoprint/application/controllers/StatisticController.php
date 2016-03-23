<?php

class StatisticController extends Helpers_General_ControllerAction
{
    public function indexAction()
    {

        // OAuth client
        // Here is your client ID
        // 506309579723-1apm2g0k6hhenmlq8unfpcet5urkksel.apps.googleusercontent.com

        // Here is your client secret
        // SbbzgkyfQSTzGXGZ9OZtSjWO

        // json
        // client_secret_506309579723-1apm2g0k6hhenmlq8unfpcet5urkksel.apps.googleusercontent.com.json

        $clientId = "506309579723-1apm2g0k6hhenmlq8unfpcet5urkksel.apps.googleusercontent.com";
        $clientSecret = "SbbzgkyfQSTzGXGZ9OZtSjWO";

        // Create a new user and set the oAuth settings
        $user = new AdWordsUser();
        $user->SetOAuth2Info(array(
            "client_id" => $clientId,
            "client_secret" => $clientSecret
        ));
    }

    public function getOrdersMatrixAction() {
        $analytics = new Helpers_General_GoogleAnalytics();

        $timeInterval = [
                'startDate' => (new DateTime('-999 days'))->format('Y-m-d'),
                'endDate' => (new DateTime())->format('Y-m-d'),
        ];
        //TODO: uncomment after initial fetch
//        $timeInterval = [
//            'startDate' => (new DateTime('-7 days'))->format('Y-m-d'),
//            'endDate' => (new DateTime())->format('Y-m-d')
//        ];

        $transactionInfo = $this->getTransactionInfo($analytics, $timeInterval);

        foreach ($transactionInfo as $record) {
            if (!$record['ga:transactionId']) {
                continue;
            }

            $orders = new Orders();
            $hasOrders = $orders->fetchRow('id = ' . $record['ga:transactionId']);
            if (!$hasOrders) {
                continue;
            }

            $transactionDate = $record['ga:date'][0] . $record['ga:date'][1] . $record['ga:date'][2] . $record['ga:date'][3] . '-' .
                $record['ga:date'][4] . $record['ga:date'][5] . '-' .
                $record['ga:date'][6] . $record['ga:date'][7];

            $data = [
                'order_id' => $record['ga:transactionId'],
                'source' => ($record['ga:adGroup'] != '(not set)')
                    ? 'ad words'
                    : str_replace(['(', ')'], '', $record['ga:source']),
                'ads_group' => ($record['ga:adGroup'] != '(not set)')
                    ? $record['ga:keyword']
                    : '',
                'query' => ($record['ga:adMatchedQuery'] != '(not set)')
                    ? str_replace(['(', ')'], '', $record['ga:adMatchedQuery'])
                    : str_replace(['(', ')'], '', $record['ga:keyword']),
                'days_to_transaction' => $record['ga:transactions'],
                'created_on' => $transactionDate
            ];

            $ordersMatrices = new OrdersMatrices();
            $ordersMatrixRecord = $ordersMatrices->fetchRow('order_id = ' . $record['ga:transactionId']);

            if ($ordersMatrixRecord) {
                $ordersMatrices->update($data, 'order_id = ' . $record['ga:transactionId']);
            } else {
                $ordersMatrices->insert($data);
            }
        }

        echo "Done - " . count($transactionInfo);
        exit;
    }

    public function analyticsAction()
    {
        $analytics = new Helpers_General_GoogleAnalytics();
        $type = $this->_request->getQuery('type', '');

        $startDate = $this->_request->getQuery('startDate', null);
        $endDate = $this->_request->getQuery('endDate', null);
        $date = $this->_request->getQuery('endDate', null);
        $timeInterval = [];

        if ($startDate) {
            $timeInterval['startDate'] = $startDate;
        }
        if ($endDate) {
            $timeInterval['endDate'] = $endDate;
        }
        if ($date) {
            $timeInterval['startDate'] = $date;
            $timeInterval['endDate'] = $date;
        }

        echo '<pre>';

        switch ($type) {
            case 'browser_statistic':
                var_dump($this->getBrowserStatistic($analytics));
                break;

            case 'organic_searches':
                var_dump($this->getOrganicSearches($analytics));
                break;

            case 'events_grouped_by_page':
                var_dump($this->getEventsGroupedByPage($analytics));
                break;

            case 'transaction_info':
                $transactionInfo = $this->getTransactionInfo($analytics, $timeInterval);


                var_dump($transactionInfo);
                break;
        }

        die;
    }

    private function getEventsGroupedByPage(Helpers_General_GoogleAnalytics $analytics)
    {
        return $analytics->callApi(
            'ga:totalEvents,ga:uniqueEvents,ga:eventValue',
            'ga:eventCategory,ga:eventAction,ga:eventLabel,ga:pagePath'
        );
    }

    private function getTransactionInfo(Helpers_General_GoogleAnalytics $analytics, $timeInterval)
    {
        return $analytics->callApi(
            'ga:transactions',
            'ga:transactionId,ga:date,ga:source,ga:adGroup,ga:adMatchedQuery,ga:keyword,ga:daysToTransaction',
            $timeInterval
        );
    }

    private function getBrowserStatistic(Helpers_General_GoogleAnalytics $analytics)
    {
        return $analytics->callApi(
            'ga:sessions',
            'ga:operatingSystem,ga:browser,ga:browserVersion'
        );
    }


    private function getOrganicSearches(Helpers_General_GoogleAnalytics $analytics)
    {
        return $analytics->callApi(
            'ga:organicSearches',
            'ga:referralPath,ga:keyword'
        );
    }
}
