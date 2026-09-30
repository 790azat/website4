@extends('layouts.site')

@php
    $siteName = config('app.name', 'Laravel');
    $title = __('Privacy Policy');
    $description = __('Read the :site privacy policy to learn how we collect, use, and protect your information.', ['site' => $siteName]);

    $lastUpdated = \Carbon\Carbon::parse('2026-09-28');

    $sections = [
        [
            'heading' => __('Introduction'),
            'body' => __('Welcome to :site. This Privacy Policy explains how we collect, use, and protect information when you visit our website. By using :site, you agree to the practices described in this policy.', ['site' => $siteName]),
        ],
        [
            'heading' => __('Information We Collect'),
            'body' => __('We collect only the limited information needed to operate :site:', ['site' => $siteName]),
            'list' => [
                __('Usage data — we may collect general information about how you interact with our site, such as pages viewed and links clicked, to help us understand what content is useful.'),
                __('Technical data — like most websites, our servers automatically log standard technical details such as browser type, device type, and IP address for security and troubleshooting purposes.'),
                __('Communications — if you contact us directly, such as through our Contact page, we keep a record of that correspondence so we can respond to you.'),
            ],
        ],
        [
            'heading' => __('How We Use Information'),
            'body' => __('We use the information we collect to:'),
            'list' => [
                __('Provide, maintain, and secure our website.'),
                __('Respond to questions, feedback, and correction requests you send us.'),
                __('Understand, in aggregate, how our articles and sections are used so we can improve them.'),
                __('Detect, investigate, and prevent fraudulent or unauthorized activity.'),
                __('Comply with applicable legal obligations.'),
            ],
        ],
        [
            'heading' => __('Cookies'),
            'body' => __(':site does not require an account. We may use a small number of essential cookies or similar technologies to deliver the site securely and remember basic preferences, such as your language. We do not use cookies to build advertising profiles, and we do not sell any information collected through cookies. You can configure your browser to refuse cookies, though some features of the site may not work as intended if you do.', ['site' => $siteName]),
        ],
        [
            'heading' => __('Third-Party Links'),
            'body' => __('Our articles may reference or link to third-party websites, tools, or sources for further reading. We are not responsible for the privacy practices or content of those external sites, and we encourage you to review their own privacy policies before sharing any information with them.'),
        ],
        [
            'heading' => __('Data Security'),
            'body' => __('We take reasonable technical and organizational measures to protect the information we hold from loss, misuse, and unauthorized access. However, no method of transmission or storage over the internet is completely secure, and we cannot guarantee absolute security.'),
        ],
        [
            'heading' => __('Children\'s Privacy'),
            'body' => __(':site is intended for adult readers and is not directed at children. We do not knowingly collect personal information from children. If you believe a child has provided us with personal information, please contact us so we can remove it.', ['site' => $siteName]),
        ],
        [
            'heading' => __('Changes to This Policy'),
            'body' => __('We may update this Privacy Policy from time to time to reflect changes in our practices or for legal or operational reasons. When we do, we will revise the date at the top of this page. We encourage you to review this policy periodically.'),
        ],
    ];
@endphp

@section('content')
    @include('partials.legal-page', [
        'heading' => __('Privacy Policy'),
        'docName' => __('this Privacy Policy or how we handle your information'),
        'closingNote' => __(':site publishes educational and informational content only and is not a substitute for personalized legal, financial, or professional advice. This policy describes our data practices and is not itself legal advice.', ['site' => $siteName]),
    ])
@endsection
