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
    
    /**
     * Get the service categories with icons
     */
    public function getCategoriesWithIcon(): array
    {
        $terms = get_the_terms($this->getId(), 'service_category');

        if (!$terms || is_wp_error($terms)) {
            return [];
        }

        $categories = [];

        foreach ($terms as $term) {
            $icon = get_field('icon', 'service_category_' . $term->term_id);

            $cat = new \stdClass();
            $cat->id = (int) $term->term_id;
            $cat->name = $term->name;
            $cat->slug = $term->slug;
            $cat->description = !empty($term->description) ? $term->description : '';
            $cat->icon = $icon ?: null;
            $cat->link = get_term_link($term);

            $categories[] = $cat;
        }
        return $categories;
    }

    /**
     * Get only the category names
     *
     * @return string[]
     */
    public function getCategoryNames(): array
    {
        $categories = $this->getCategoriesWithIcon();
        $names = [];

        foreach ($categories as $cat) {
            if (isset($cat->name) && $cat->name !== '') {
                $names[] = (string) $cat->name;
            }
        }

        return $names;
    }

    /**
     * Get only the category icons (non-empty)
     *
     * @return string[]
     */
    public function getCategoryIcons(): array
    {
        $categories = $this->getCategoriesWithIcon();
        $icons = [];

        foreach ($categories as $cat) {
            if (!empty($cat->icon)) {
                $icons[] = (string) $cat->icon;
            }
        }

        return $icons;
    }

    /**
     * @return string|null
     */
    public function getCustomIconSvg(): ?string
    {
        return apply_filters('ModularityServiceInfo/customIconSvg', null, $this->getId());
    }
}