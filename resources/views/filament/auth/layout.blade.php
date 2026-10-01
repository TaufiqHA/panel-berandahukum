@props([
    'livewire' => null,
    'hasTopbar' => true,
])

@php
    $hour = (int) now()->format('G');
    $greeting = match (true) {
        $hour < 11 => 'Good Morning',
        $hour < 15 => 'Good Afternoon',
        $hour < 19 => 'Good Evening',
        default => 'Good Night',
    };
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="fi-login-wrapper">
        <div class="fi-login-form-pane">
            <div class="fi-login-form-inner">
                {{ $slot }}
            </div>
        </div>
        <div class="fi-login-hero-pane">
            <div class="fi-login-hero-text">
                <h1>{{ $greeting }}</h1>
                <h5>Bali, Indonesia</h5>
            </div>
            <div class="fi-login-hero-credit">
                Photo by <a href="https://unsplash.com/photos/a8lTjWJJgLA" target="_blank" rel="noopener">Justin Kauffman</a> on <a href="https://unsplash.com" target="_blank" rel="noopener">Unsplash</a>
            </div>
        </div>
    </div>

    <style>
        .fi-login-wrapper { display: flex; min-height: 100vh; }
        .fi-login-form-pane { flex: 1 1 100%; display: flex; align-items: center; justify-content: center; padding: 48px 56px; background: #ffffff; }
        .fi-login-form-inner { width: 100%; max-width: 520px; }
        .fi-login-hero-pane { display: none; flex: 1 1 0; background-image: url('{{ asset('assets/img/unsplash/login-bg.jpg') }}'); background-repeat: no-repeat; background-position: 0 0%; background-size: 120%; animation: backgroundWalkY 70s linear infinite alternate; position: relative; }
        .fi-login-hero-text { position: absolute; bottom: 90px; left: 56px; color: #ffffff; }
        .fi-login-hero-text h1 { font-size: 52px; font-weight: 700; margin: 0; }
        .fi-login-hero-text h5 { margin: 8px 0 0; font-weight: 400; opacity: .9; font-size: 18px; }
        .fi-login-hero-credit { position: absolute; bottom: 24px; left: 56px; color: rgba(255,255,255,.85); font-size: 13px; }
        .fi-login-hero-credit a { color: #ffffff; }
        @media (min-width: 1024px) {
            .fi-login-form-pane { flex: 0 0 33.3333%; }
            .fi-login-hero-pane { display: block; }
        }

        @keyframes backgroundWalkY {
            0% { background-position: 0 0%; }
            100% { background-position: 0 100%; }
        }
    </style>
</x-filament-panels::layout.base>
