@php
    $label = $item['label'] ?? '';
    $badgeHtml = '';
    $textLabel = $label;
    $hasServiceInfoBadge = is_string($label) && str_contains($label, 'service-info-badge');

    if ($hasServiceInfoBadge && preg_match('/<span class="service-info-badge"[^>]*>.*?<\/span>/s', $label, $match)) {
        $badgeHtml = $match[0];
        $textLabel = trim(strip_tags(str_replace($match[0], '', $label)));
    }

    $hasIcon = !empty($item['icon']['icon']);
    $anchorBadgeOnIcon = $hasServiceInfoBadge && $hasIcon && $badgeHtml !== '';
@endphp
@if($item['icon'] || $item['label'])
  @link([
    'id' => $id . '-' . $item['id'] . '-' . $loop->index . '__label',
    'classList' => [$baseClass . '__link'],
    'href' => $item['href'],
    'xfn' => $item['xfn'] ?? false
  ])
    @if ($anchorBadgeOnIcon)
      <span class="service-info-nav-icon">
        @icon([
          'icon' => $item['icon']['icon'] ?? null,
          'size' => $item['icon']['size'] ?? 'inherit',
          'filled' => $item['icon']['filled'] ?? false,
          'classList' => $item['icon']['classList'] ?? [],
          'attributeList' => array_merge($item['icon']['attributeList'] ?? [], [
            'style' => 'background-color:' . ($item['color'] ?? '') . ';'
          ])
        ])
        @endicon
        {!! $badgeHtml !!}
      </span>
      @if ($textLabel)
      <span
        class="{{$baseClass}}__text"
        style="{{isset($item['color']) ? 'color:' . $item['color'] . ';' : ''}}">
        {{ $textLabel }}
      </span>
      @endif
    @else
      @icon([
        'icon' => $item['icon']['icon'] ?? null,
        'size' => $item['icon']['size'] ?? 'inherit',
        'filled' => $item['icon']['filled'] ?? false,
        'classList' => $item['icon']['classList'] ?? [],
        'attributeList' => array_merge($item['icon']['attributeList'] ?? [], [
          'style' => 'background-color:' . ($item['color'] ?? '') . ';'
        ])
      ])
      @endicon
      @if ($item['label'])
      <span
        class="{{$baseClass}}__text"
        style="{{isset($item['color']) ? 'color:' . $item['color'] . ';' : ''}}">
        {!! $item['label'] !!}
      </span>
      @endif
    @endif
  @endlink
@else
  <!-- Hidden link: Both label and icon is missing -->
@endif
