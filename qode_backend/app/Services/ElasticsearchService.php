<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('services.elasticsearch.hosts')[0]])
            ->build();
    }

    public function index($index, $id, $body)
    {
        return $this->client->index([
            'index' => $index,
            'id' => $id,
            'body' => $body,
        ]);
    }

    public function get($index, $id)
    {
        return $this->client->get([
            'index' => $index,
            'id' => $id,
        ]);
    }

    public function delete($index, $id)
    {
        return $this->client->delete([
            'index' => $index,
            'id' => $id,
        ]);
    }

    public function bulkIndex($index, array $records)
    {
        $params = ['body' => []];
        foreach ($records as $record) {
            $params['body'][] = [
                'index' => [
                    '_index' => $index,
                    '_id' => $record['id'],
                ]
            ];
            $params['body'][] = $record;
        }
        return $this->client->bulk($params);
    }

    public function search($index, $query)
    {
        return $this->client->search([
            'index' => $index,
            'body' => [
                'query' => [
                    'match' => $query,
                ]
            ]
        ]);
    }

    public function searchAllColumns(string $index, string $query, array $fields = [])
    {
        $fields = empty($fields) ? ['*'] : $fields;
        $params = [
            'index' => $index,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => $fields,
                        'type' => 'best_fields',
                    ]
                ],
                'size' => 1000,
            ]
        ];
        return $this->client->search($params);
    }
}
