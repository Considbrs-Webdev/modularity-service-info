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
                    @typography([
                        'element' => 'span'
                    ])
                        {{ $translations['noServiceInformationAvailable'] }}
                    @endtypography
                @else
                    <ul class="mod-service-info__list">
                        @foreach ($items as $post)
                            <li class="mod-service-info__item">
                                <a href="{{ $post->link }}" class="mod-service-info__link">
                                    @if ($showIcons && $post->iconName)
                                        <div class="mod-service-info__icon">
                                            @icon(['icon' => $post->iconName])
                                            @endicon
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
