<?php

class CronController extends Helpers_General_ControllerAction
{
    public function sendEmailAction()
    {

    }

    public function reindexSearchAction()
    {
        $searchContent = new SearchContent();
        $searchContent->reindex();

        exit;
    }

    public function reindexElasticAction()
    {
        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
            'body' => ['testField' => 'abc']
        ];

        $client = \Elasticsearch\ClientBuilder::create()->build();

        $response = $client->index($params);
        print_r($response);

        echo "243";

        exit;
    }
}
