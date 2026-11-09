@props(['logs', 'public' => false])

<div class="tl">
    @php $logCount = $logs->count(); @endphp
    @foreach($logs as $i => $log)
        <div class="tli {{ $i === $logCount - 1 ? 'last' : '' }}">
            <div class="ln">
                <span class="dot"></span>
                @if($i < $logCount - 1)
                    <span class="bar"></span>
                @endif
            </div>
            <div class="tx">
                <div style="font-weight:500">{{ $log->event }}</div>
                <div class="small muted">{{ $log->created_at->locale('id')->isoFormat('D MMM Y') }}</div>
                @if($log->note)
                    <div class="small" style="margin-top:2px">{{ $log->note }}</div>
                @endif
                @if(!empty($log->changes) && !$public)
                    <div class="small muted" style="margin-top:2px">
                        @foreach($log->changes as $field => $val)
                            <div>{{ ucfirst($field) }}: "{{ $val['old'] }}" → "{{ $val['new'] }}"</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
