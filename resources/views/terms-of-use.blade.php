@extends('layouts.site')

@php
    $siteName = config('app.name', 'Laravel');
    $title = __('Terms of Use');
    $description = __('Read the :site terms of use for the rules and guidelines that govern use of our site.', ['site' => $siteName]);

    $lastUpdated = \Carbon\Carbon::parse('2026-09-28');

    $sections = [
        [
            'heading' => __('Acceptance of Terms'),
            'body' => __('These Terms of Use ("Terms") govern your access to and use of :site, including our website and articles. By accessing or using :site, you agree to be bound by these Terms. If you do not agree, please do not use the site.', ['site' => $siteName]),
        ],
        [
            'heading' => __('Educational Use Only'),
            'body' => __(':site publishes content for general informational and educational purposes only. Nothing on this site constitutes personalized legal, financial, or professional advice, and it should not be relied upon as such. You should consult a qualified professional before making decisions based on information found here.', ['site' => $siteName]),
        ],
        [
            'heading' => __('Use of the Site'),
            'body' => __('When using our site, you agree to:'),
            'list' => [
                __('Use the site only for lawful purposes and in a manner consistent with these Terms.'),
                __('Not attempt to gain unauthorized access to any part of the site or related systems.'),
                __('Not interfere with or disrupt the site, its servers, or its networks.'),
                __('Not scrape, copy, or republish substantial portions of our content without permission.'),
            ],
        ],
        [
            'heading' => __('Intellectual Property'),
            'body' => __('Unless otherwise noted, all articles, graphics, logos, and other content on :site are the property of :site or its licensors and are protected by copyright and other intellectual property laws. You may view and share our content for personal, non-commercial use, but you may not reproduce, distribute, or create derivative works from it without our prior written consent.', ['site' => $siteName]),
        ],
        [
            'heading' => __('Third-Party Links'),
            'body' => __('Our articles may reference or link to third-party websites, tools, or sources for further reading. These links are provided for convenience only. We do not control, endorse, or take responsibility for the content, accuracy, or practices of any third-party site.'),
        ],
        [
            'heading' => __('Disclaimer of Warranties'),
            'body' => __(':site is provided on an "as is" and "as available" basis. While we aim to keep our content accurate and up to date, we make no warranties, express or implied, regarding the completeness, reliability, or accuracy of any information on the site.', ['site' => $siteName]),
        ],
        [
            'heading' => __('Limitation of Liability'),
            'body' => __('To the fullest extent permitted by law, :site and its team members are not liable for any indirect, incidental, or consequential damages arising from your use of, or inability to use, the site or any content published on it.', ['site' => $siteName]),
        ],
        [
            'heading' => __('Changes to These Terms'),
            'body' => __('We may update these Terms from time to time to reflect changes in our practices or for legal or operational reasons. When we do, we will revise the date at the top of this page. Continued use of the site after changes are posted constitutes your acceptance of the updated Terms.'),
        ],
    ];
@endphp

@section('content')
    @include('partials.legal-page', [
        'heading' => __('Terms of Use'),
        'docName' => __('these Terms of Use'),
        'closingNote' => __(':site publishes educational and informational content only and is not a substitute for personalized legal, financial, or professional advice. These Terms describe the rules for using our site and are not themselves legal advice.', ['site' => $siteName]),
    ])
@endsection
