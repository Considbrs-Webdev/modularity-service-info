<?php

declare(strict_types=1);

namespace ModularityServiceInfo\Import;

/**
 * Interface ImporterInterface
 *
 * All service information importers must implement this interface.
 * Register your importer via the 'modularity_service_info_register_importers' action:
 *
 * ```php
 * add_action('modularity_service_info_register_importers', function (ImporterRegistry $registry) {
 *     $registry->register(new MyCustomImporter());
 * });
 * ```
 *
 * @package ModularityServiceInfo\Import
 */
interface ImporterInterface
{
    /**
     * Get the unique key identifying this importer.
     *
     * Used for deduplication together with ServiceInfoItem::getSourceId().
     * Must be a slug-like string (e.g. 'arcgis-trafikinfo').
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Get a human-readable name for this importer.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Run the import and return an array of service information items.
     *
     * @return ServiceInfoItem[]
     */
    public function import(): array;
}
