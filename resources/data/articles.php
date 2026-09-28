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
 * Authors: 'name', 'role', 'bio', optional 'photo' (file in
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
        'rachel-moreno' => [
            'name' => 'Rachel Moreno',
            'role' => 'Auto Insurance & Claims Analyst',
            'bio' => 'Rachel spent eight years as a field claims adjuster for a national auto insurer before moving into consumer writing. She covers coverage types, the claims process, total-loss valuations, and how to deal with adjusters.',
        ],
        'daniel-okafor' => [
            'name' => 'Daniel Okafor',
            'role' => 'Personal Injury & Legal Affairs Writer',
            'bio' => 'Daniel worked as a paralegal at a personal injury firm for six years. He writes about injury claims, settlements, statutes of limitations, medical liens, and what to expect when working with an attorney.',
        ],
        'tom-brennan' => [
            'name' => 'Tom Brennan',
            'role' => 'Collision Repair Specialist',
            'bio' => 'Tom is a former collision repair estimator with more than 15 years in body shops. His focus includes repair estimates, OEM versus aftermarket parts, frame damage, and choosing a repair shop after an accident.',
        ],
        'priya-nair' => [
            'name' => 'Priya Nair',
            'role' => 'Auto Finance & Consumer Credit Editor',
            'bio' => 'Priya has a background in auto lending and consumer credit counseling. She covers car loans after a total loss, gap insurance, rental coverage, and the financial side of recovering from an accident.',
        ],
        'karen-walsh' => [
            'name' => 'Karen Walsh',
            'role' => 'Road Safety & Accident Reporting Contributor',
            'bio' => 'Karen served as a traffic officer and later as a crash-report analyst. She writes about what happens at the scene, police reports, fault rules, and the road safety data behind common collisions.',
        ],
    ],

    'programs' => [],

];
