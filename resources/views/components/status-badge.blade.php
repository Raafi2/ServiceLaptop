@props(['status'])

@php
    $statusEnum = is_string($status) ? \App\Enums\ServiceStatus::tryFrom($status) : $status;
    $label = $statusEnum?->label() ?? (string) $status;
    $iconName = $statusEnum?->icon() ?? 'clock';
    $isCancelled = $statusEnum === \App\Enums\ServiceStatus::Dibatalkan || $label === 'Dibatalkan';
@endphp

<span class="badge{{ $isCancelled ? ' x' : '' }}">
    <x-icon :name="$iconName" size="13" />
    <span>{{ $label }}</span>
</span>
