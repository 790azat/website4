{{-- Button to a main guide's external link; replaces [[CTA]] lines in its text. --}}
<p class="not-prose my-8">
    <a href="{{ $program['cta_url'] }}" target="_blank" rel="noopener noreferrer nofollow" class="btn-primary">
        {{ $program['cta_label'] }}
        <flux:icon name="arrow-top-right-on-square" variant="mini" class="size-4" />
    </a>
</p>
