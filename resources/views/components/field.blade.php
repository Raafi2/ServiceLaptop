@props([
    'id',
    'label',
    'hint' => '',
    'error' => null,
])

<div class="f{{ $error ? ' err' : '' }}" id="f-{{ $id }}">
    @if($label)
        <label for="{{ $id }}">{{ $label }}</label>
    @endif
    {{ $slot }}
    <div class="hint" id="h-{{ $id }}">{{ $error ?: $hint }}</div>
</div>
