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
        <ul class="mod-service-info__list">
            @foreach ($posts as $post)
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
                                    {{ $post->formattedDate }}
                                </div>
                            @endif

                            @typography([
                                'element' => 'h3',
                                'variant' => 'h4',
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

    @if ($linkToServiceInformationArchive)
        <div class="mod-service-info__archive-link">
            @button([
                'text' => __('View all service information', 'modularity-service-info'),
                'href' => $archiveLink,
                'color' => 'primary',
            ])
            @endbutton
        </div>
    @endif
</div>
