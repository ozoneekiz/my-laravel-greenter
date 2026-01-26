<?php

namespace CodersFree\LaravelGreenter\Services;

use CodersFree\LaravelGreenter\Exceptions\GreenterException;
use CodersFree\LaravelGreenter\Factories\DocumentBuilderFactory;
use CodersFree\LaravelGreenter\Factories\XmlBuilderFactory;
use Greenter\XMLSecLibs\Sunat\SignedXml;

class XmlService
{
    public function generateXml(string $type, array $data): string
    {
        $document = (DocumentBuilderFactory::create($type))->build($data);
        $xml = (XmlBuilderFactory::create($type))->build($document);

        $certPath = config('greenter.company.certificate');

        if (!file_exists($certPath)) {
            throw new GreenterException("Certificate file not found: $certPath");
        }

        $signer = new SignedXml();
        $signer->setCertificate(file_get_contents($certPath));
        $xmlSigned = $signer->signXml($xml);

        return $xmlSigned;
    }
}