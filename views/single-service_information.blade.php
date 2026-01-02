@extends('templates.single')

@section('content')
    {!! $hook->loopStart !!}

    @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])

    @element([
        'componentElement' => 'article',
        'id' => 'article',
        'attributeList' => array_merge(!empty($postLanguage) ? ['lang' => $postLanguage] : []),
        'classList' => array_merge(isset($centerContent) && $centerContent ? ['u-margin__x--auto'] : [], [
            'c-article',
            'c-article--readable-width',
            's-article',
            'u-clearfix'
        ])
    ])

        <!-- Title -->
        @section('article.title.before')@show
        @section('article.title')
            @if (
                (is_object($post) && method_exists($post, 'getTitle') ? $post->getTitle() : $post->post_title) ||
                    isset($callToActionItems['floating']))
                @group([
                    'justifyContent' => 'space-between'
                ])
                    @if (method_exists($post, 'getTitle') ? $post->getTitle() : $post->post_title)
                        @typography([
                            'element' => 'h1',
                            'variant' => 'h1',
                            'id' => 'page-title'
                        ])
                            {!! method_exists($post, 'getTitle') ? $post->getTitle() : $post->post_title !!}
                        @endtypography
                    @endif
                    @if (!empty($callToActionItems['floating']['icon']) && !empty($callToActionItems['floating']['wrapper']))
                        @element($callToActionItems['floating']['wrapper'] ?? [])
                            @icon($callToActionItems['floating']['icon'])
                            @endicon
                        @endelement
                    @endif
                @endgroup
            @endif
        @show
        @section('article.title.after')@show

        @include('service.meta')

        <!-- Content -->
        @section('article.content.before')@show
        {!! $hook->articleContentBefore !!}
        @section('article.content')
            {!! is_object($post) && method_exists($post, 'getContent') ? $post->getContent() : $post->post_content !!}
        @show
        @section('article.content.after')@show

        <!-- Blog style author signature -->
        @section('content.below')
            @includeWhen(
                $postTypeDetails && $postTypeDetails->hierarchical && !$isBlogStyle,
                'partials.signature',
                array_merge((array) $signature, (array) ['classList' => []]))
        @endsection

    @endelement

    @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])

    {!! $hook->loopEnd !!}
@stop
