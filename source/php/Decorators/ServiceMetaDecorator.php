<?php

namespace ModularityServiceInfo\Decorators;

use Municipio\PostObject\PostObjectInterface;
use Municipio\PostObject\Decorators\AbstractPostObjectDecorator;

use Municipio\Helper\AcfService;
use ModularityServiceInfo\Helper\DateFormatter;

/**
 * Class ServiceMetaDecorator
 *
 * Decorator to add service information meta data for templates.
 *
 * @package ModularityServiceInfo\Decorators
 */
class ServiceMetaDecorator extends AbstractPostObjectDecorator implements PostObjectInterface {
    public function __construct(PostObjectInterface $postObject) {
        parent::__construct($postObject);
    }

    /**
     * Get the service date
     */
    public function getServiceDate(): string
    {
        $acfService = AcfService::get();

        $startDateRaw = $acfService->getField('start_date', $this->getId());
        $endDateRaw = $acfService->getField('end_date', $this->getId());

        return DateFormatter::formatDateRange((string) $startDateRaw, (string) $endDateRaw);
    }
}