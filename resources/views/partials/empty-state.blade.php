{{--
    Shown where an article list has nothing to display yet.
--}}
<div class="mt-12 flex flex-col items-center rounded-md border-2 border-dashed border-line bg-surface px-6 py-16 text-center dark:border-brand-800">
    <span class="flex size-16 items-center justify-center rounded-md bg-navy-950 text-zest-400">
        <flux:icon name="pencil-square" class="size-8" />
    </span>
    <h3 class="mt-6 font-display text-2xl font-bold text-ink">{{ $heading ?? __('New lessons are on the way') }}</h3>
    <p class="mt-3 max-w-md leading-relaxed text-muted">
        {{ $text ?? __('Our editors are preparing the first guides for this section. Check back soon.') }}
    </p>
    <a href="{{ route('home') }}" wire:navigate class="btn-ghost mt-8">{{ __('Back to the homepage') }}</a>
</div>
