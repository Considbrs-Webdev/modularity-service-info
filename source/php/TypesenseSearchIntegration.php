<?php

declare(strict_types=1);

namespace ModularityServiceInfo;

use ModularityServiceInfo\Helper\DateFormatter;
use ModularityServiceInfo\PostType\ServiceInformation;

/**
 * Integrates Service Information with Typesense search indexing.
 *
 * - Adds service-specific fields (formatted_date_range, icon_name).
 * - Registers hit template, post type mapping, and placeholder mappings for the service info card.
 */
class TypesenseSearchIntegration
{
    private const TEMPLATE_KEY = 'service-info';

    public function __construct()
    {
        add_filter(
            'Municipio/TypesenseSearch/DocumentBuilder/build',
            [$this, 'enrichServiceInfoDocument'],
            10,
            2
        );
        add_filter('Municipio/TypesenseSearch/hitTemplates', [$this, 'addHitTemplate']);
        add_filter('Municipio/TypesenseSearch/hitTemplateView', [$this, 'resolveHitTemplateView'], 10, 2);
        add_filter('Municipio/TypesenseSearch/postTypeToTemplate', [$this, 'mapPostTypesToTemplate']);
        add_filter('Municipio/TypesenseSearch/placeholderMappings', [$this, 'addPlaceholderMappings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueStylesOnSearch'], 20);
    }

    /**
     * Enqueue service-info styles on search page so hit templates render correctly.
     */
    public function enqueueStylesOnSearch(): void
    {
        if (!is_search()) {
            return;
        }
        $styleFile = \ModularityServiceInfo\Helper\CacheBust::name('css/modularity-service-info-general.css');
        if ($styleFile) {
            wp_enqueue_style(
                'modularity-service-info-general',
                MODULARITYSERVICEINFO_URL . '/assets/dist/' . $styleFile,
                [],
                null
            );
        }
    }

    /**
     * @param string[] $templates
     * @return string[]
     */
    public function addHitTemplate(array $templates): array
    {
        $templates[] = self::TEMPLATE_KEY;
        return $templates;
    }

    /**
     * @param string $view
     * @param string $key
     * @return string
     */
    public function resolveHitTemplateView(string $view, string $key): string
    {
        if ($key === self::TEMPLATE_KEY) {
            return 'templates.hits.service-info';
        }
        return $view;
    }

    /**
     * @param array<string, string> $mapping
     * @return array<string, string>
     */
    public function mapPostTypesToTemplate(array $mapping): array
    {
        $mapping[ServiceInformation::POST_TYPE_NAME] = self::TEMPLATE_KEY;
        return $mapping;
    }

    /**
     * @param array<string, string> $mappings
     * @return array<string, string>
     */
    public function addPlaceholderMappings(array $mappings): array
    {
        return array_merge($mappings, [
            'SEARCH_HIT_DATE_RANGE' => 'formatted_date_range',
            'SEARCH_HIT_ICON'       => 'icon_name',
        ]);
    }

    /**
     * Enrich the Typesense document with Service Information data.
     *
     * @param array<string, mixed> $document The Typesense document array.
     * @param \WP_Post             $post    The source post.
     * @return array<string, mixed>
     */
    public function enrichServiceInfoDocument(array $document, \WP_Post $post): array
    {
        if ($post->post_type !== ServiceInformation::POST_TYPE_NAME) {
            return $document;
        }

        $startDateRaw = get_field('start_date', $post->ID);
        $endDateRaw = get_field('end_date', $post->ID);
        $formattedDate = DateFormatter::formatDateRange((string) $startDateRaw, (string) $endDateRaw);
        // Store plain text (ndash entity would be escaped when displayed)
        $document['formatted_date_range'] = str_replace('&ndash;', "\u{2013}", $formattedDate);

        $iconName = null;
        $terms = get_the_terms($post->ID, 'service_category');
        if ($terms && !is_wp_error($terms)) {
            $firstTerm = reset($terms);
            $icon = get_field('icon', 'service_category_' . $firstTerm->term_id);
            if (is_array($icon)) {
                $iconName = $icon['icon_material_icon'] ?? $icon['icon'] ?? null;
            } else {
                $iconName = $icon;
            }
        }

        $rawIcon = is_string($iconName) && $iconName !== '' ? $iconName : 'fa-solid fa-circle-info';
        $document['icon_name'] = $iconName;

        return $document;
    }
}
