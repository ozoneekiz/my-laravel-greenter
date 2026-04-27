<?php

namespace CodersFree\LaravelGreenter\Services;

use Greenter\Model\DocumentInterface;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Illuminate\Support\Facades\File;

class ReportService
{
    protected ?string $templatePath = null;
    protected ?string $templateTheme = null;
    protected array $customParams = [];
    protected array $customOptions = [];

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
        $pdfReport->setOptions(array_merge(config('greenter.report.options', []), $this->customOptions));
        
        $pdf = $pdfReport->render($document, $this->buildReportParams($document));

        if ($pdf === null) {
            throw new \RuntimeException($pdfReport->getExporter()->getError());
        }

        return $pdf;
    }

    public function setTemplatePath(string $templatePath): self
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    public function setTemplateTheme(string $templateTheme): self
    {
        $this->templateTheme = $templateTheme;

        return $this;
    }

    public function setParams(array $params): self
    {
        $this->customParams = array_merge_recursive($this->customParams, $params);

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->customOptions = array_merge($this->customOptions, $options);

        return $this;
    }

    public function createHtmlReport(DocumentInterface $document): HtmlReport
    {
        $templatePath = $this->determineTemplatePath($document);
        $twigOptions = config('greenter.report.twigOptions');

        $htmlReport = File::isDirectory($templatePath)
            ? new HtmlReport($templatePath, $twigOptions)
            : new HtmlReport();

        $resolver = new DefaultTemplateResolver();
        $htmlReport->setTemplate($resolver->getTemplate($document));

        return $htmlReport;
    }

    protected function determineTemplatePath(DocumentInterface $document): string
    {
        if ($this->templatePath !== null && File::isDirectory($this->templatePath)) {
            return $this->templatePath;
        }

        $resolver = new DefaultTemplateResolver();
        $templateName = $resolver->getTemplate($document);

        $templateByType = config("greenter.report.template_by_type.{$templateName}");
        if ($templateByType) {
            $themePath = $this->resolveThemePath($templateByType);
            if ($themePath !== null) {
                return $themePath;
            }
        }

        $theme = $this->templateTheme ?? config('greenter.report.template_theme');
        if ($theme) {
            $themePath = $this->resolveThemePath($theme);
            if ($themePath !== null) {
                return $themePath;
            }
        }

        return config('greenter.report.templates');
    }

    protected function resolveThemePath(string $theme): ?string
    {
        $themes = config('greenter.report.template_themes', []);

        if (isset($themes[$theme]) && File::isDirectory($themes[$theme])) {
            return $themes[$theme];
        }

        return File::isDirectory($theme) ? $theme : null;
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
        $params = array_merge_recursive(config('greenter.report.params', []), $this->customParams);

        // Extras
        $extras = $params['user']['extras'] ?? [];
        array_unshift($extras, [
            'name' => 'CONDICIÓN DE PAGO', 
            'value' => method_exists($document, 'getCuotas') && !empty($document->getCuotas())
                ? 'Crédito'
                : 'Contado'
        ]);

        $params['user']['extras'] = $extras;
        
        // Logotipo
        if (!empty($params['system']['logo']) && file_exists($params['system']['logo'])) {
            $params['system']['logo'] = file_get_contents($params['system']['logo']);
        }

        return $params;
    }
}