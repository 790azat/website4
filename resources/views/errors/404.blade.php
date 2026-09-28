@extends('layouts.site')

@php
    $title = __('Page not found');
    $description = null;
@endphp

@section('content')
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(var(--color-line)_1px,transparent_1px)] [background-size:26px_26px] [mask-image:linear-gradient(to_bottom,black,transparent)]"></div>

        <div class="relative mx-auto flex max-w-3xl flex-col items-center px-6 py-24 text-center lg:py-32">
            <p class="font-display text-[7rem] leading-none font-semibold text-brand-600 sm:text-[10rem] dark:text-brand-400">404</p>
            <h1 class="mt-6 font-display text-4xl font-semibold tracking-tight text-ink">{{ __('This page took a different path') }}</h1>
            <p class="mt-4 max-w-md text-lg leading-relaxed text-body">
                {{ __('The page you are looking for does not exist or may have moved. Let’s get you back to learning.') }}
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" wire:navigate class="btn-primary">{{ __('Back to homepage') }}</a>
                <a href="{{ route('articles') }}" wire:navigate class="btn-ghost">{{ __('Browse articles') }}</a>
            </div>
        </div>
    </section>
@endsection
