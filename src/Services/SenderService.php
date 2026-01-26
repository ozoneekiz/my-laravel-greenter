<?php

namespace CodersFree\LaravelGreenter\Services;

use CodersFree\LaravelGreenter\Builders\Responses\SendResultBuilder;
use CodersFree\LaravelGreenter\Builders\Responses\SunatResponseBuilder;
use CodersFree\LaravelGreenter\DTOs\ErrorDto;
use CodersFree\LaravelGreenter\DTOs\SendResultDTO;
use CodersFree\LaravelGreenter\Factories\DocumentBuilderFactory;
use CodersFree\LaravelGreenter\Factories\SenderFactory;
use Greenter\Model\Response\SummaryResult;

class SenderService
{
    public function send(string $type, array $data): SendResultDTO
    {
        try {

            // Sender
            $sender = (SenderFactory::create($type))->build();

            // Documento
            $document = DocumentBuilderFactory::create($type)->build($data);

            //Envío a Sunat
            $result = $sender->send($document);

            // Resultado base
            $sendResult = SendResultBuilder::make()
                ->document($document)
                ->xml(
                    $type === 'despatch'
                        ? $sender->getLastXml()
                        : $sender->getFactory()->getLastXml()
                );

            // Procesar respuesta SUNAT
            $sunatResponse = $this->processSunatResult($sender, $result);

            return $sendResult
                ->sunatResponse($sunatResponse)
                ->build();

        } catch (\Exception $e) {

            return $this->exceptionResult($e);

        }
    }

    public function sendXml(string $type, string $xml, $name = null)
    {
        try {
            $sender = (SenderFactory::create($type))->build();

            $result = $type === 'despatch'
                ? $sender->sendXml($name, $xml)
                : $sender->sendXmlFile($xml);

            // Verificamos que la conexión con SUNAT fue exitosa.
            $sunatResponse = $this->processSunatResult($sender, $result);

            return SendResultBuilder::make()
                ->sunatResponse($sunatResponse)
                ->build();

        } catch (\Throwable $th) {

            return $this->exceptionResult($th);

        }
    }

    public function processSunatResult($sender, $result)
    {
        $sunatResponse = SunatResponseBuilder::make();

        // Error de conexión o validación
        if (!$result->isSuccess()) {

            return $sunatResponse
                ->success(false)
                ->error(new ErrorDto(
                    code: $result->getError()->getCode(),
                    message: $result->getError()->getMessage()
                ))->build();
        }

        if ($result instanceof SummaryResult) {
            $result = $sender->getStatus($result->getTicket());

            if (!$result->isSuccess()) {
                return $sunatResponse
                    ->success(false)
                    ->error(new ErrorDto(
                        code: $result->getError()->getCode(),
                        message: $result->getError()->getMessage()
                    ))->build();
            }
        }

        // Guardamos el CDR
        return $sunatResponse
            ->success(true)
            ->cdrZip($result->getCdrZip())
            ->cdrResponse($result->getCdrResponse())
            ->build();
    }

    private function exceptionResult(\Exception $e): SendResultDTO
    {
        return SendResultBuilder::make()
            ->sunatResponse(
                SunatResponseBuilder::make()
                    ->success(false)
                    ->error(new ErrorDto(
                        code: $e->getCode(),
                        message: $e->getMessage()
                    ))
                    ->build()
            )
            ->build();
    }
}
