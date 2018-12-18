<?php

namespace Gotrex\VoipNow\Adapter;

/**
 * Interface ConnectorInterface.
 */
interface ConnectorInterface
{
    public function connect(array $config);
}