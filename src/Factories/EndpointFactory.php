<?php

namespace CodersFree\LaravelGreenter\Factories;

class EndpointFactory
{
    public function create($type)
    {
        $mode = config('greenter.mode');
        $endpoints = config('greenter.endpoints');

        return match ($type) {
            'invoice', 
            'note',
            'voided',
            'summary' => $mode === 'prod'
                ? $endpoints['fe']['prod']
                : $endpoints['fe']['beta'],

            'perception', 
            'retention'=> $mode === 'prod'
                ? $endpoints['retencion']['prod']
                : $endpoints['retencion']['beta'],

            default => throw new \InvalidArgumentException("Tipo de documento no soportado: $type"),
        };
    }
}