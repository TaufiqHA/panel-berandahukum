@php
    $state = $getState();
@endphp

@if ($state)
    <img
        src="{{ $state }}"
        alt="Tanda Tangan"
        style="height:60px;background:#fff;border:1px solid #d1d5db;padding:2px;border-radius:4px;max-width:200px;"
    >
@else
    <span style="color:#b45309;">Belum ada</span>
@endif
