@if (!$hideTitle && $postTitle)
    @typography([
        'element' => 'h4',
        'variant' => 'h2',
        'classList' => ['module-title']
    ])
        {{ $postTitle }}
    @endtypography
@endif

<div class="mod-service-info">
    @if (!empty($posts))
        @foreach ($groupByCategories ? $posts : ['' => $posts] as $category => $items)
            <div class="mod-service-info__group">
                @if ($groupByCategories && !empty($category))
                    @typography([
                        'element' => 'h4',
                        'variant' => 'h3',
                        'classList' => ['module-title']
                    ])
                        {{ $category }}
                    @endtypography
                @endif

                @if (empty($items))
                    @typography(['element' => 'span'])
                        {{ $translations['noServiceInformationAvailable'] }}
                    @endtypography
                @else
                    @php
                        $statusGroups = $sortUpcomingFirst
                            ? [
                                ['label' => $translations['ongoing'], 'list' => array_values(array_filter($items, fn($p) => !$p->isEnded))],
                                ['label' => $translations['ended'],   'list' => array_values(array_filter($items, fn($p) => $p->isEnded))],
                              ]
                            : [['label' => null, 'list' => $items]];
                    @endphp

                    <div class="mod-service-info__sections">
                        @foreach ($statusGroups as $statusGroup)
                            @if (!empty($statusGroup['list']))
                                <div class="mod-service-info__status">
                                    @if ($statusGroup['label'])
                                        @typography([
                                            'element' => 'h5',
                                            'variant' => 'h4',
                                            'classList' => ['mod-service-info__status-heading']
                                        ])
                                            {{ $statusGroup['label'] }}
                                        @endtypography
                                    @endif

                                    <ul class="mod-service-info__list">
                                        @foreach ($statusGroup['list'] as $post)
                                            <li class="mod-service-info__item">
                                                <a href="{{ $post->link }}" class="mod-service-info__link">
                                                    @if ($showIcons && ($post->customIconSvg || $post->iconName))
                                                        <div class="mod-service-info__icon">
                                                            @if ($post->customIconSvg)
                                                                {!! $post->customIconSvg !!}
                                                            @else
                                                                @icon(['icon' => $post->iconName])
                                                                @endicon
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <div class="mod-service-info__content">
                                                        @if ($post->formattedDate)
                                                            <div class="mod-service-info__dates">
                                                                {!! $post->formattedDate !!}
                                                            </div>
                                                        @endif

                                                        @typography([
                                                            'element' => 'span',
                                                            'classList' => ['mod-service-info__title']
                                                        ])
                                                            {{ $post->title }}
                                                        @endtypography
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @else
        @typography([
            'element' => 'span'
        ])
            {{ $translations['noServiceInformationAvailable'] }}
        @endtypography
    @endif

    @if (!empty($posts) && $linkToServiceInformationArchive && !$archiveMode)
        <div class="mod-service-info__archive-link">
            @button([
                'text' => $translations['archiveLinkText'],
                'href' => $archiveLink,
                'color' => 'primary',
                'icon' => $archiveLinkIcon,
            ])
            @endbutton
        </div>
    @endif
</div>
