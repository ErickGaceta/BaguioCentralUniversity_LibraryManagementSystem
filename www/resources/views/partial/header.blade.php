<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance
    @livewireStyles

    <style>
        .loader {
            width: 200px;
            height: 140px;
            background: #860805;
            box-sizing: border-box;
            position: relative;
            border-radius: 8px;
            perspective: 1000px;
        }

        .loader:before {
            content: '';
            position: absolute;
            left: 10px;
            right: 10px;
            top: 10px;
            bottom: 10px;
            border-radius: 8px;
            background: #f5f5f5 no-repeat;
            background-size: 60px 10px;
            background-image: linear-gradient(#dcbb31 100px, transparent 0),
                linear-gradient(#870a06 100px, transparent 0),
                linear-gradient(#dcbb31 100px, transparent 0),
                linear-gradient(#870a06 100px, transparent 0),
                linear-gradient(#dcbb31 100px, transparent 0),
                linear-gradient(#870a06 100px, transparent 0);

            background-position: 15px 30px, 15px 60px, 15px 90px,
                105px 30px, 105px 60px, 105px 90px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
        }

        .loader:after {
            content: '';
            position: absolute;
            width: calc(50% - 10px);
            right: 10px;
            top: 10px;
            bottom: 10px;
            border-radius: 8px;
            background: #fff no-repeat;
            background-size: 60px 10px;
            background-image: linear-gradient(#dcbb31 100px, transparent 0),
                linear-gradient(#870a06 100px, transparent 0),
                linear-gradient(#dcbb31 100px, transparent 0);
            background-position: 50% 30px, 50% 60px, 50% 90px;
            transform: rotateY(0deg);
            transform-origin: left center;
            animation: paging 1s linear infinite;
        }

        @keyframes paging {
            to {
                transform: rotateY(-180deg);
            }
        }

        .loadModal {
            display: flex;
            align-items: center;
        }

        .bar {
            display: inline-block;
            width: 3px;
            height: 20px;
            background-color: rgba(255, 255, 255, .5);
            border-radius: 10px;
            animation: scale-up4 1s linear infinite;
        }

        .bar:nth-child(2) {
            height: 35px;
            margin: 0 5px;
            animation-delay: .25s;
        }

        .bar:nth-child(3) {
            animation-delay: .5s;
        }

        @keyframes scale-up4 {
            20% {
                background-color: #ffff;
                transform: scaleY(1.5);
            }

            40% {
                transform: scaleY(1);
            }
        }
    </style>
</head>

<body class="flex min-h-screen bg-zinc-200 dark:bg-zinc-800">
