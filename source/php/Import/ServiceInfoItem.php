<?php

declare(strict_types=1);

namespace ModularityServiceInfo\Import;

/**
 * Class ServiceInfoItem
 *
 * Immutable data transfer object representing a single service information
 * item to be imported. All importers must return instances of this class.
 *
 * @package ModularityServiceInfo\Import
 */
class ServiceInfoItem
{
    /**
     * @param string               $sourceId   Unique identifier from the source system (used for deduplication)
     * @param string               $title      The service information title
     * @param string               $content    The service information content (HTML allowed)
     * @param \DateTimeImmutable    $startDate  When the service information becomes active
     * @param \DateTimeImmutable|null $endDate  When the service information expires (null = no expiry)
     * @param string[]             $categories Taxonomy term names for service_category
     */
    public function __construct(
        private readonly string $sourceId,
        private readonly string $title,
        private readonly string $content,
        private readonly \DateTimeImmutable $startDate,
        private readonly ?\DateTimeImmutable $endDate = null,
        private readonly array $categories = [],
    ) {
    }

    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
}
