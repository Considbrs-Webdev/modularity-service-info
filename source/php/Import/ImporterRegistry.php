<?php

declare(strict_types=1);

namespace ModularityServiceInfo\Import;

/**
 * Class ImporterRegistry
 *
 * Central registry for service information importers.
 * External plugins register their importers here via the
 * 'modularity_service_info_register_importers' action.
 *
 * @package ModularityServiceInfo\Import
 */
class ImporterRegistry
{
    /** @var array<string, ImporterInterface> */
    private array $importers = [];

    /**
     * Register an importer.
     *
     * @param ImporterInterface $importer
     * @return void
     * @throws \InvalidArgumentException If an importer with the same key is already registered.
     */
    public function register(ImporterInterface $importer): void
    {
        $key = $importer->getKey();

        if (isset($this->importers[$key])) {
            throw new \InvalidArgumentException(
                sprintf('An importer with key "%s" is already registered.', $key)
            );
        }

        $this->importers[$key] = $importer;
    }

    /**
     * Get all registered importers.
     *
     * @return array<string, ImporterInterface>
     */
    public function getImporters(): array
    {
        return $this->importers;
    }

    /**
     * Get a specific importer by key.
     *
     * @param string $key
     * @return ImporterInterface|null
     */
    public function getImporter(string $key): ?ImporterInterface
    {
        return $this->importers[$key] ?? null;
    }

    /**
     * Check if an importer with the given key is registered.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->importers[$key]);
    }
}
