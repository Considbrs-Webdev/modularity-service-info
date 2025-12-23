<?php

declare(strict_types=1);

namespace ModularityServiceInfo\Model;

/**
 * Class ServiceInfoPost
 * 
 * Data transfer object for service information posts
 * 
 * @package ModularityServiceInfo\Model
 */
class ServiceInfoPost
{
    public string $title;
    public ?string $startDate;
    public ?string $endDate;
    public ?string $formattedDate;
    public ?string $iconName;
    public string $link;

    public function __construct(
        string $title,
        ?string $startDate,
        ?string $endDate,
        ?string $formattedDate,
        ?string $iconName,
        string $link
    ) {
        $this->title = $title;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->formattedDate = $formattedDate;
        $this->iconName = $iconName;
        $this->link = $link;
    }
}
