<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>Inventory</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/1.png') }}?v={{ time() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk interaktifitas dropdown -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        @keyframes scrollDown {
            0% {
                transform: translateY(-50px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-scrollDown {
            animation: scrollDown 0.8s ease forwards;
        }

        /* Button click scale animation */
        @keyframes clickScale {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(0.95);
            }

            100% {
                transform: scale(1);
            }
        }

        .animate-clickScale {
            animation: clickScale 0.3s ease forwards;
        }
    </style>
</head>

<body class="bg-white min-h-screen flex flex-col justify-center items-center">
    <img id="logo" alt="Abstract logo with blue and orange geometric shapes forming a square"
        class="mb-8 opacity-0" height="100" src="{{ asset('assets/logos.png') }}" style="width: 100px; height: 100px"
        width="100" />
    <button id="playBtn"
        class="flex items-center gap-2 bg-blue-600 text-white text-sm font-normal rounded-full px-6 py-2 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <a href="{{ route('login') }}"><i class="fas fa-search text-xs"></i> Play <i
                class="fas fa-arrow-right text-xs"></i></a>
    </button>

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
        playBtn.style.opacity = "0"; // initially hidden for animation on load

        playBtn.addEventListener("click", () => {
            playBtn.classList.remove("animate-clickScale");
            // Trigger reflow to restart animation
            void playBtn.offsetWidth;
            playBtn.classList.add("animate-clickScale");
        });
    </script>
</body>


</html>
@extends('layouts.app')
