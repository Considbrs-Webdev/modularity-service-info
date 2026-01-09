<div class="modularity-mod-service-info">
    <div class="mod-service-info mod-service-info__item">
        <div class="mod-service-info__wrapper">
            @if (!empty($post->getCategoryIcons()))
                <div class="mod-service-info__icon">
                    @foreach ($post->getCategoryIcons() as $icon)
                        @icon(['icon' => $icon])
                        @endicon
                    @endforeach
                </div>
            @endif

            <div class="mod-service-info__content">
                @if ($post->getServiceDate())
                    <div class="mod-service-info__dates">
                        {!! $post->getServiceDate() !!}
                    </div>
                @endif

                @typography([
                    'element' => 'span',
                    'classList' => ['mod-service-info__title']
                ])
                    {!! implode(', ', $post->getCategoryNames()) !!}
                @endtypography
            </div>
        </div>
    </div>
</div>
