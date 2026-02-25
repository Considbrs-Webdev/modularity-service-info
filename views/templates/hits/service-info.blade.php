@element([
    'componentElement' => 'template',
    'attributeList' => [
        'data-js-search-hit-template-service-info' => true
    ]
])
    <div class="mod-service-info">
        <div class="mod-service-info__item">
            <a href="{SEARCH_HIT_LINK}" class="mod-service-info__link" aria-label="{SEARCH_HIT_ARIA_LABEL}">
                <div class="mod-service-info__icon">
                    <wa-icon name="{SEARCH_HIT_ICON}"></wa-icon>
                </div>
                <div class="mod-service-info__content">
                    <div class="mod-service-info__dates">{SEARCH_HIT_DATE_RANGE}</div>
                    <span class="mod-service-info__title">{SEARCH_HIT_HEADING}</span>
                </div>
            </a>
        </div>
    </div>
@endelement
