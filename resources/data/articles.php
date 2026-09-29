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
        'after-a-crash' => [
            'title' => 'After a Crash',
            'order' => 1,
            'icon' => 'exclamation-triangle',
            'description' => 'What to do at the scene, police reports, determining fault, and the first days after a collision.',
        ],
        'insurance-claims' => [
            'title' => 'Insurance Claims',
            'order' => 2,
            'icon' => 'shield-check',
            'description' => 'Filing a claim, working with adjusters, coverage types, total losses, and disputed payouts.',
        ],
        'injury-legal-help' => [
            'title' => 'Injury & Legal Help',
            'order' => 3,
            'icon' => 'scale',
            'description' => 'Injury claims, medical bills, settlements, deadlines, and when to talk to an attorney.',
        ],
        'repairs-replacement' => [
            'title' => 'Repairs & Replacement',
            'order' => 4,
            'icon' => 'wrench-screwdriver',
            'description' => 'Body shops, rental cars, diminished value, and financing a replacement vehicle.',
        ],
    ],

    'authors' => [
        'bedig-sarkissian' => [
            'name' => 'Bedig Sarkissian',
            'role' => 'Legal Research & Consumer Rights',
            'bio' => 'Bedig Sarkissian is a legal researcher and consumer advocate focusing on cross-border legal frameworks, documentation standards, and public rights education.',
            'bio_long' => 'Bedig Sarkissian has spent years navigating complex legal statutes, regulatory compliance, and documentation standards. Drawing on a strong background in analytical research and public advocacy, Bedig is dedicated to making dense legal procedures clear and accessible for everyday consumers. His guides focus on helping people understand their statutory rights, navigate administrative hurdles, and protect themselves during complex disputes. Outside of his research work, Bedig enjoys typography and exploring traditional architecture.',
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

    'programs' => [],

];
