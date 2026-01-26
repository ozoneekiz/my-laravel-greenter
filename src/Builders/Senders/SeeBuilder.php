<?php

namespace CodersFree\LaravelGreenter\Builders\Senders;

use CodersFree\LaravelGreenter\Contracts\SenderInterface;
use CodersFree\LaravelGreenter\Exceptions\GreenterException;
use CodersFree\LaravelGreenter\Factories\EndpointFactory;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;

class SeeBuilder implements SenderInterface
{
    public function __construct(
        protected string $type
    ) {}

    public function build(): See
    {
        $company = config('greenter.company');
        $certPath = config('greenter.company.certificate');
        $endpoint = (new EndpointFactory())->create($this->type);

        if (!file_exists($certPath)) {
            throw new GreenterException("Certificate file not found: $certPath");
        }

        $see = new See();
        $see->setCertificate(
            file_get_contents($certPath)
        );
        $see->setService($endpoint);
        $see->setClaveSOL(
            $company['ruc'],
            $company['clave_sol']['user'],
            $company['clave_sol']['password']
        );

        return $see;
    }
}
