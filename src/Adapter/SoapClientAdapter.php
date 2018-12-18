<?php

namespace Gotrex\VoipNow\Adapter;

use Auth;
use File;
use GuzzleHttp\Client as Guzzle;

class SoapClientAdapter implements ConnectorInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param array $config
     * @return mixed
     */
    public function connect(array $config)
    {
        $this->config = $config;
        return $this->getAdapter();
    }

    private function getAdapter()
    {
        $wsdl = $this->config['domain'] . '/soap2/schema/latest/voipnowservice.wsdl';

        $options = [
            'uri' => 'http://schemas.xmlsoap.org/soap/envelope/',
            'style' => SOAP_RPC,
            'use' => SOAP_ENCODED,
            'soap_version' => SOAP_1_1,
            'cache_wsdl' => WSDL_CACHE_BOTH,
            'connection_timeout' => 15,
            'trace' => true,
            'encoding' => 'UTF-8',
            'exceptions' => true,
        ];

        $client = new \SoapClient($wsdl, $options);

        $auth = new \stdClass();
        $auth->accessToken = $this->authorize();
        $authvalues = new \SoapVar($auth, SOAP_ENC_OBJECT, 'http://4psa.com/HeaderData.xsd/' . $this->config['version']);

        $header = new \SoapHeader('http://4psa.com/HeaderData.xsd/' . $this->config['version'], 'userCredentials', $authvalues, false);
        $client->__setSoapHeaders([$header]);

        return $client;
    }

    private function authorize()
    {
       $tokenData = $this->getTokenInformation();

        if (!isset($tokenData->voipnow_access_token) || $tokenData->voipnow_expired_at <= now()) {
            $client = new Guzzle;

            $request = $client->post($this->config['domain'] . '/oauth/token.php', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->config['key'],
                    'client_secret' => $this->config['secret'],
                ],
            ]);

            $result = json_decode($request->getBody()->getContents());

            $tokenData->voipnow_access_token = $result->access_token;
            $tokenData->voipnow_expires_in = $result->expires_in;
            $tokenData->voipnow_expired_at = now()->addSeconds($result->expires_in)->format('Y-m-d H:i:s');

            $this->storeTokenInformation($tokenData);

            return $tokenData->voipnow_access_token;
        }

        return $tokenData->voipnow_access_token;
    }

    private function getTokenInformation()
    {
        if (config('voipnow.multi_user')) {
            if(Auth::guest())
            {
                throw new \Exception('You need to be authorized to make a request');
            }

            return Auth::user();
        } 

        return (object) json_decode(File::get(storage_path('voipnow.api.json')), true);
    }

    private function storeTokenInformation($tokenData)
    {
        if (config('voipnow.multi_user')) {
            $tokenData->save();
        } else {
            File::put(storage_path('voipnow.api.json'), json_encode($tokenData));
        }
    }
}