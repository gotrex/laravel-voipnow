<?php

namespace Gotrex\VoipNow;

use Illuminate\Config\Repository;
use Gotrex\VoipNow\Adapter\ConnectorInterface;

class VoipNowClass
{

    /**
     * @var [type]
     */
    protected $client;

    /**
     * @var [type]
     */
    protected $config;

    protected $collectionResults = [
        'GetChargingPlans',
        'GetServiceProviders',
        'GetOrganizations',
        'GetUsers',
        'GetUserGroups',
        'GetExtensions',
    ];

    /**
     * Create a new instance.
     */
    public function __construct(Repository $config, ConnectorInterface $client)
    {
        $this->config = $config;

        $this->client = $client;
    }

    /**
     * @return ConnectorInterface
     */
    public function connection()
    {
        return $this->client->connect($this->getConfig());
    }

    public function getConfig()
    {
        return $this->config->get('voipnow');
    }

    public function handle(string $action, $parameters = [])
    {
        $client = $this->connection();

        $handleRequest = $client->{$action}($parameters);

        if(in_array($action, $this->collectionResults)) {
            return collect(reset($handleRequest));
        }

        return $handleRequest;
    }

    public function __call($method, $parameters)
    {
        return $this->handle($method, ...$parameters);
    }
}
