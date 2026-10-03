<?php

/**
 * Site content data: sections, authors, and main guides (programs).
 *
 * Main guides: settings here, text in resources/data/programs/{slug}.md
 *   (front matter: title, intro), translations in programs/{es,fr}/.
 *   A line with just [[CTA]] in the body becomes a button to cta_url.
 *
 * There is no database; this file and the Markdown files next to it are the
 * single source of truth, read through App\Support\SiteContent.
 *
 * Articles live in resources/data/articles/{slug}.md: a front-matter block
 * (title, section, author, date, optional image/excerpt) followed by the
 * Markdown body. Images are picked up automatically from
 * public/images/articles/{slug}.{webp,jpg,jpeg,png} (or the front-matter
 * "image" path); without one, generated artwork is shown.
 *
 * Sections: 'title', 'order', optional 'icon' (Heroicon name) and
 *   'description' (shown on topic cards and the section header).
 * Authors: 'name', 'role', 'bio' (short, shown under articles), 'bio_long'
 *   (the author's own page at /author/{key}), optional 'photo' (file in
 *   public/images/team/). Without 'photo', public/images/team/{key}.{webp,jpg,
 *   jpeg,png} is used when present, otherwise the author's initials.
 */

return [

    'sections' => [
        'media-buying' => [
            'title' => 'Media Buying Guides',
            'order' => 0,
            'icon' => 'presentation-chart-line',
            'description' => 'Actionable playbooks built from daily media buying. No generic theory - just field-tested strategies covering search arbitrage, RSOC compliance, TikTok and Meta optimization, traffic quality evaluation, and creative automation systems.',
        ],
        'personal-injury' => [
            'title' => 'Personal Injury & Accident Law',
            'order' => 1,
            'icon' => 'scale',
            'description' => 'Accident claims, liability and fault, insurance, medical records, settlements, and working with legal professionals.',
        ],
        'family-immigration' => [
            'title' => 'Family & Immigration Law',
            'order' => 2,
            'icon' => 'users',
            'description' => 'Divorce, custody and support, adoption, guardianship, visas, permanent residency, naturalization, and removal proceedings.',
        ],
        'criminal-employment' => [
            'title' => 'Criminal & Employment Law',
            'order' => 3,
            'icon' => 'briefcase',
            'description' => 'Criminal procedure and defense, DUI stops, record sealing, workplace rights, discrimination, wages, and workers\' compensation.',
        ],
        'business-property' => [
            'title' => 'Business, Property & Financial Law',
            'order' => 4,
            'icon' => 'building-office-2',
            'description' => 'Corporate governance, contracts, tax and compliance, real estate transactions, foreclosure, estate planning, and trusts.',
        ],
    ],

    'authors' => [
        'bedig-sarkissian' => [
            'name' => 'Bedig Sarkissian',
            'role' => 'Business, Property & Financial Compliance',
            'bio' => 'Bedig Sarkissian is a legal researcher and financial compliance writer specializing in corporate governance, commercial transactions, and real property regulations.',
            'bio_long' => 'Bedig Sarkissian is a legal researcher and financial compliance writer specializing in corporate governance, commercial transactions, and real property regulations. With a strong background in analyzing complex regulatory updates and commercial frameworks, Bedig focuses on translating intricate financial codes, contract laws, and property statutes into accessible, practical guidance for entrepreneurs, small business owners, and investors.',
        ],
        'laura-bennett' => [
            'name' => 'Laura Bennett',
            'role' => 'Public Policy & Family Law',
            'bio' => 'Laura Bennett specializes in public policy, cross-border legal frameworks, and domestic relations law.',
            'bio_long' => 'Laura Bennett specializes in public policy, cross-border legal frameworks, and domestic relations law. With extensive experience in legal journalism, Laura is dedicated to providing clear, structured, and reliable information to individuals and families navigating complex legal transitions.',
        ],
        'daniel-foster' => [
            'name' => 'Daniel Foster',
            'role' => 'Civil Litigation & Workplace Rights',
            'bio' => 'Daniel Foster is a legal writer and consumer advocacy specialist with a deep focus on civil litigation, tort law, and workplace rights.',
            'bio_long' => 'Daniel Foster is a legal writer and consumer advocacy specialist with a deep focus on civil litigation, tort law, and workplace rights. Over his career, Daniel has broken down complex legal statutes, workers\' compensation frameworks, and defendant rights into transparent, actionable resources. His work helps everyday readers understand their legal rights and options following motor vehicle accidents, workplace disputes, and consumer injury claims.',
        ],
        'marcus-thompson' => [
            'name' => 'Marcus Thompson',
            'role' => 'Criminal Procedure & Employment Law',
            'bio' => 'Marcus Thompson is a legal researcher specializing in criminal procedure, employment law, workplace rights, and employment disputes.',
            'bio_long' => 'Marcus Thompson is a legal researcher specializing in criminal procedure, employment law, workplace rights, and employment disputes. His work focuses on explaining legal processes, criminal justice developments, and the practical consequences of laws affecting individuals and communities.',
        ],
        'mateo-alvarez' => [
            'name' => 'Mateo Alvarez',
            'role' => 'Personal Injury & Legal Research',
            'bio' => 'Mateo Alvarez is a legal researcher and former litigation assistant with extensive experience in tort law documentation and personal injury claims.',
            'bio_long' => 'Mateo Alvarez has worked closely with civil litigation and personal injury practices, managing case files, medical chronologies, and statutory timelines. Having observed firsthand how intimidating the legal process can be for individuals recovering from accidents, Mateo transitioned to legal education. He writes clear, step-by-step breakdowns of personal injury lawsuits, settlement procedures, and liability rules to ensure accident victims stay informed. In his free time, Mateo enjoys long-distance running and cooking traditional regional dishes.',
        ],
        'kate-johnson' => [
            'name' => 'Kate Johnson',
            'role' => 'Insurance & Claims',
            'bio' => 'Kate Johnson is a former senior insurance claims adjuster with over a decade of experience evaluating auto collision policies and total loss payouts.',
            'bio_long' => 'Kate Johnson spent over ten years on the inside of the auto insurance industry, managing complex collision claims, property damage valuations, and dispute resolutions for major carriers. Frustrated by how often standard policyholders are left confused by internal formulas and adjusting tactics, Kate left the corporate side to write transparent consumer guides. Her mission is to decode insurance fine print so drivers can advocate for themselves fairly and effectively. When she isn\'t writing, Kate is an avid gardener and local animal shelter volunteer.',
        ],
        'camila-ferreira' => [
            'name' => 'Camila Ferreira',
            'role' => 'Insurance Policy & Consumer Education',
            'bio' => 'Camila Ferreira is an insurance risk analyst and consumer educator specializing in policy underwriting, coverage exclusions, and claim appeals.',
            'bio_long' => 'Camila Ferreira brings a sharp analytical background in risk assessment and policy underwriting to consumer advocacy. Having evaluated countless commercial and personal policy structures, Camila understands the exact clauses that dictate claim approvals and denials. She writes practical guides designed to help policyholders understand their coverage limits, spot bad-faith practices, and navigate insurance negotiations with confidence. Outside of work, Camila is passionate about acoustic guitar and landscape painting.',
        ],
    ],

    // Main articles: shown in the "Main guides" block on the homepage, and
    // visitors who open the homepage land on the first one after the captcha.
    'main_articles' => [
        'why-we-reject-traffic-even-when-the-volume-looks-good',
        'what-we-look-for-in-a-traffic-partner-and-how-we-evaluate-a-new-feed',
    ],

    'programs' => [
        [
            'slug' => '18-wheeler-accident-lawyers-the-full-guide-to-truck-accident-legal-representation',
            'section' => 'personal-injury',
            'date' => '2026-09-30',
            'cta_label' => 'See More Details',
            'cta_url' => 'https://website1-pink-delta.vercel.app/',
            'hero_icon' => 'truck',
            'related_slug' => '18-wheeler-regulations-understanding-federal-safety-standards-and-commercial-claims',
        ],
        [
            'slug' => 'oilfield-injury-attorneys-the-full-guide-to-oilfield-and-offshore-accident-legal-representation',
            'section' => 'personal-injury',
            'date' => '2026-09-29',
            'cta_label' => 'See More Details',
            'cta_url' => 'https://website1-pink-delta.vercel.app/',
            'hero_icon' => 'wrench-screwdriver',
            'related_slug' => 'workplace-incidents-occupational-safety-regulations-and-injury-protocols',
        ],
    ],

];
