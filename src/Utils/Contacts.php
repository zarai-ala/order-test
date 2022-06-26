<?php

namespace App\Utils;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Contacts
{
    private $client;
    private $containerInterface;

    public function __construct(HttpClientInterface $client, ContainerInterface $containerInterface)
    {
        $this->client = $client;
        $this->containerInterface = $containerInterface;
    }

    public function GetContacts()
    {
        $response = $this->client->request(
            'GET',
            $this->containerInterface->getParameter('CONTACT_URL'),
            [
                'headers' => [
                    'x-api-key' => $this->containerInterface->getParameter('X-API-KEY'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],

            ]
        );

        $content = $response->getContent();

        $content = $response->toArray();

        return $content;
    }
}
