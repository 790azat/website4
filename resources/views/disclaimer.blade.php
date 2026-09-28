@extends('layouts.site')

@php
    $siteName = config('app.name', 'Laravel');
    $siteDomain = config('app.display_domain');
    $title = __('Disclaimer');
    $description = __('Read the :site disclaimer: our content is educational only and is not legal, medical, insurance, or financial advice.', ['site' => $siteName]);

    $lastUpdated = \Carbon\Carbon::parse('2026-09-28');

    $sections = [
        [
            'heading' => __('Educational Purposes Only'),
            'body' => __('The content provided on :domain is for informational and educational purposes only and should not be construed as legal, medical, insurance, or financial advice. :domain is not a law firm, insurance company, or medical provider, and our articles do not replace advice from a licensed professional who can review your specific situation.', ['domain' => $siteDomain]),
        ],
        [
            'heading' => __('Policies, Deadlines, and Laws Vary'),
            'body' => __('Insurance policies, claim deadlines, fault rules, and injury laws vary by insurer and by state. Before making any claim, legal, medical, or financial decision, conduct your own research and consult a qualified, licensed professional who understands your specific situation.'),
        ],
        [
            'heading' => __('No Warranties or Liability'),
            'body' => __(':domain makes no representations or warranties as to the accuracy, completeness, or suitability of the information contained herein, and assumes no liability for any losses or damages arising from the use of this content.', ['domain' => $siteDomain]),
        ],
    ];
@endphp

@section('content')
    @include('partials.legal-page', [
        'heading' => __('Disclaimer'),
        'docName' => __('this Disclaimer'),
        'closingNote' => __('This Disclaimer applies to all content published on :site, including articles and guides.', ['site' => $siteName]),
    ])
@endsection
