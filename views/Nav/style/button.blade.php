@php
    $labelContainsHtml = strip_tags($item['label']) !== $item['label'];
    $ariaLabel = $labelContainsHtml ? strip_tags($item['label']) : $item['label'] ?? '';
@endphp
@button([
    'id' => $id . '-' . $item['id'] . '-' . $loop->index . '__label',
    'icon' => $labelContainsHtml
        ? false
        : (isset($item['icon']['icon'])
            ? $item['icon']['icon']
            : false),
    'reversePositions' => true,
    'text' => $labelContainsHtml ? false : $item['label'] ?? '',
    'style' => $item['buttonStyle'] ?? $buttonStyle,
    'color' => $item['buttonColor'] ?? $buttonColor,
    'href' => $item['href'],
    'classList' => [$baseClass . '__button'],
    'attributeList' => [
        'aria-label' => $ariaLabel,
    ],
    'context' => ['component.nav.button'],
    'size' => $height == 'sm' ? 'sm' : 'md',
])
@if ($labelContainsHtml)
    @if (isset($item['icon']['icon']) && $item['icon']['icon'])
        <span class="c-button__label-icon c-button__label-icon--reverse">
            @icon([
                'icon' => $item['icon']['icon'],
                'size' => $height == 'sm' ? 'sm' : 'md',
                'attributeList' => ['aria-hidden' => 'true'],
                'classList' => $item['icon']['classList'] ?? []
            ])
            @endicon
        </span>
    @endif
    <span class="c-button__label-text c-button__label-text--reverse">
        {!! $item['label'] !!}
    </span>
@endif
@endbutton
