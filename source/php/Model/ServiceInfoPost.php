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
    public ?string $formattedDate;
    public ?string $iconName;
    public ?string $customIconSvg;
    public string $link;
    public array $terms;
    public bool $isEnded;

    /**
     * @param string $title
     * @param string|null $formattedDate
     * @param string|null $iconName
     * @param string $link
     * @param array $terms
     * @param string|null $customIconSvg
     * @param bool $isEnded
     */
    public function __construct(
        string $title,
        ?string $formattedDate,
        ?string $iconName,
        string $link,
        array $terms = [],
        ?string $customIconSvg = null,
        bool $isEnded = false
    ) {
        $this->title = $title;
        $this->formattedDate = $formattedDate;
        $this->iconName = $iconName;
        $this->customIconSvg = $customIconSvg;
        $this->link = $link;
        $this->terms = $terms;
        $this->isEnded = $isEnded;
    }
}
