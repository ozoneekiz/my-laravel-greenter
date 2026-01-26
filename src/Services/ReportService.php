<?php

namespace CodersFree\LaravelGreenter\Services;

use Greenter\Model\DocumentInterface;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Illuminate\Support\Facades\File;

class ReportService
{
    public function generateHtml(DocumentInterface $document)
    {
        $htmlReport = $this->createHtmlReport($document);
        return $htmlReport->render($document, $this->buildReportParams($document));
    }

    public function generatePdf(DocumentInterface $document)
    {
        $htmlReport = $this->createHtmlReport($document);

        $pdfReport = new PdfReport($htmlReport);
        $pdfReport->setBinPath(config('greenter.report.bin_path'));
        $pdfReport->setOptions(config('greenter.report.options'));
        
        $pdf = $pdfReport->render($document, $this->buildReportParams($document));

        if ($pdf === null) {
            throw new \RuntimeException($pdfReport->getExporter()->getError());
        }

        return $pdf;
    }

    public function createHtmlReport(DocumentInterface $document): HtmlReport
    {
        $templatePath = config('greenter.report.templates');
        $twigOptions = config('greenter.report.twigOptions');

        $htmlReport = File::isDirectory($templatePath)
            ? new HtmlReport($templatePath, $twigOptions)
            : new HtmlReport();

        $resolver = new DefaultTemplateResolver();
        $htmlReport->setTemplate($resolver->getTemplate($document));

        return $htmlReport;
    }

    protected function getParamsWithLogo(): array
    {
        $params = config('greenter.report.params');

        if (isset($params['system']['logo']) && file_exists($params['system']['logo'])) {
            $params['system']['logo'] = file_get_contents($params['system']['logo']);
        }

        return $params;
    }

    //Cambiar configuracion
    protected function buildReportParams(DocumentInterface $document): array
    {
        $params = config('greenter.report.params', []);

        //Extras
        $extras = $params['user']['extras'] ?? [];
        array_unshift($extras, [
            'name' => 'CONDICIÓN DE PAGO', 
            'value' => method_exists($document, 'getCuotas') && !empty($document->getCuotas())
                ? 'Crédito'
                : 'Contado'
        ]);

        $params['user']['extras'] = $extras;
        
        //Logotipo
        if (file_exists($params['system']['logo'])) {
            $params['system']['logo'] = file_get_contents($params['system']['logo']);
        }

        return $params;
    }
}