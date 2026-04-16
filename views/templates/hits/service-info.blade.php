@element([
    'componentElement' => 'template',
    'attributeList' => [
        'data-js-search-hit-template-service-info' => true
    ]
])
    <div class="c-card c-card--size-md c-card--action mod-service-info mod-service-info__card ts-search-hit-card">
        <div class="c-card__paint-container">
            <div class="c-card__body">
                <div class="c-group c-group--horizontal c-group--align-items-flex-start c-group--gap-2">
                    <div class="c-group c-group--vertical c-group--gap-1" style="min-width: 0;">
                        {{-- Metadata row: date range, separator dot, type --}}
                        <div class="c-group c-group--horizontal c-group--align-items-center c-group--gap-1">
                            <span class="c-badge c-badge--primary">{SEARCH_HIT_SUBHEADING}</span>
                            <span class="c-typography">
                                {SEARCH_HIT_DATE_RANGE}
                            </span>
                        </div>
                        <h2 class="c-typography c-card__heading u-margin__y--0 c-typography__variant--h3">
                            <a class="ts-search-hit-card__link" href="{SEARCH_HIT_LINK}">{SEARCH_HIT_HEADING}</a>
                        </h2>
                        <p class="c-typography c-card__content c-typography__variant--p u-margin__y--0">
                            {SEARCH_HIT_EXCERPT}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endelement
