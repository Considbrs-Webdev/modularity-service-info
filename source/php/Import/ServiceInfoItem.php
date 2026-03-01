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
     * @param string               $sourceId       Unique identifier from the source system (used for deduplication)
     * @param string               $title          The service information title
     * @param string               $content        The service information content (HTML allowed)
     * @param \DateTimeImmutable    $startDate      When the service information becomes active
     * @param \DateTimeImmutable|null $endDate      When the service information expires (null = no expiry)
     * @param string[]             $categories     Taxonomy term names for service_category
     * @param \DateTimeImmutable|null $unpublishDate When to automatically unpublish the post (null = no auto-unpublish). Must be expressed in GMT/UTC.
     * @param string               $onUnpublish    Action on unpublish: 'draft' or 'trash' (defaults to 'draft')
     * @param \DateTimeImmutable|null $publishDate  When the WordPress post should be published (null = publish immediately). If in the future the post will be scheduled. Must be expressed in GMT/UTC.
     */
    public function __construct(
        private readonly string $sourceId,
        private readonly string $title,
        private readonly string $content,
        private readonly \DateTimeImmutable $startDate,
        private readonly ?\DateTimeImmutable $endDate = null,
        private readonly array $categories = [],
        private readonly ?\DateTimeImmutable $unpublishDate = null,
        private readonly string $onUnpublish = 'draft',
        private readonly ?\DateTimeImmutable $publishDate = null,
    ) {
    }

    /**
     * @return string Returns the unique source identifier for this service information item.
     */
    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * @return string Returns the title of the service information.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string Returns the content of the service information.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return \DateTimeImmutable Returns the start date of the service information.
     */
    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * @return \DateTimeImmutable|null Returns the end date, or null if the service information does not expire
     */
    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * @return string[] Returns the categories of the service information.
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return \DateTimeImmutable|null Returns the unpublish date, or null if the service information does not have an automatic unpublish date.
     */
    public function getUnpublishDate(): ?\DateTimeImmutable
    {
        return $this->unpublishDate;
    }

    /**
     * Returns the action to take on unpublish: 'draft' or 'trash'.
     */
    public function getOnUnpublish(): string
    {
        return $this->onUnpublish;
    }

    /**
     * Returns the WordPress post publish date, or null to publish immediately.
     * If the date is in the future the post will be scheduled ('future' status).
     */
    public function getPublishDate(): ?\DateTimeImmutable
    {
        return $this->publishDate;
    }
}
