@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <img id="logo" alt="Abstract logo with blue and orange geometric shapes forming a square" class="mb-8 opacity-0"
        height="100" src="{{ asset('assets/logos.png') }}" style="width: 100px; height: 100px" width="100" />

    <button id="playBtn"
        class="flex items-center gap-2 bg-blue-600 text-white text-sm font-normal rounded-full px-6 py-2 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 opacity-0">
        <a href="{{ route('login') }}"><i class="fas fa-search text-xs"></i> Play <i
                class="fas fa-arrow-right text-xs"></i></a>
    </button>
@endsection

@push('scripts')
    <script>
        // Animate logo and button on page load with scroll down effect
        window.addEventListener("DOMContentLoaded", () => {
            const logo = document.getElementById("logo");
            const playBtn = document.getElementById("playBtn");

            // Animate logo
            logo.classList.add("animate-scrollDown");
            logo.style.opacity = "1";

            // Animate button with slight delay
            setTimeout(() => {
                playBtn.classList.add("animate-scrollDown");
                playBtn.style.opacity = "1";
            }, 200);
        });

        // Animate button click with scale effect
        const playBtn = document.getElementById("playBtn");
        playBtn.addEventListener("click", () => {
            playBtn.classList.remove("animate-clickScale");
            // Trigger reflow to restart animation
            void playBtn.offsetWidth;
            playBtn.classList.add("animate-clickScale");
        });
    </script>
@endpush
