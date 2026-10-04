<?php

return [
    'help' => [
        ['command' => 'about',    'description' => 'Show information about me'],
        ['command' => 'projects', 'description' => 'Show my projects'],
        ['command' => 'banner',   'description' => 'Show the welcome banner'],
        ['command' => 'contact',  'description' => 'Contact details and message form'],
        ['command' => 'social',   'description' => 'My social profiles'],
        ['command' => 'theme set <name>', 'description' => 'Change themes, use theme list to check available themes'],
        ['command' => 'theme list', 'description' => 'show available themes'],
        ['command' => 'help',             'description' => 'Show this list'],
    ],

    'about' => [
        'name' => 'Saleh Ahmed',
        'role' => 'Junior Software Engineer',
        'intro' => 'I am a junior software engineer from Yemen, living in Kuala Lumpur. '
            .'I build backend apps with PHP and Laravel, and I also work with .NET. '
            .'I like APIs, authentication, queues, real-time apps, and clean code with tests.',
        'status' => 'Open to work',
        'location' => 'Kuala Lumpur, Malaysia',
        'languages' => 'Arabic (native), English (fluent)',

        'experience' => [
            [
                'role' => 'Junior Software Engineer (Part-Time)',
                'company' => 'Pixalink',
                'period' => '04/2025 - 07/2026',
                'points' => [
                    'Built CRM, rewards, and campaign features with Laravel, Livewire, Filament, and MySQL.',
                    'Used queue workers to turn external transactions into customer rewards without blocking.',
                    'Wrote tests with Pest PHP and worked with code reviews, branches, and pull requests.',
                ],
            ],
            [
                'role' => 'Junior Software Engineer',
                'company' => 'Triple Y Studio',
                'period' => '03/2025 - 10/2025',
                'points' => [
                    'Built an internal automation system for multi-stage business workflows.',
                    'Used Server-Sent Events (SSE) for real-time status updates.',
                    'Managed multithreaded processes and connected external services.',
                ],
            ],
        ],
        'skills' => [
            'Languages' => [
                'PHP',
                'JavaScript',
                'C#',
            ],
            'Frameworks' => [
                'Laravel',
                '.NET',
                'Slim',
            ],
            'Software architecture' => [
                'MVC',
                'Multi-tier architecture',
                'Monolithic architecture',
                'Dependency Injection',
                'REST APIs',
            ],
            'Authentication' => [
                'JWT',
                'OAuth 2.0',
                'Session-based auth',
                'API auth',
            ],
            'Frontend' => [
                'HTML',
                'CSS',
                'Blade',
                'Livewire',
                'Filament',
                'Alpine.js',
            ],
            'Databases' => [
                'MySQL',
                'SQL Server',
                'Redis',
            ],
            'Tools' => [
                'Git',
                'GitHub Actions',
                'Docker',
                'Pest',
                'PHPStan',
            ],
            'Practices' => [
                'OOP',
                'SOLID',
                'Clean Code',
                'Testing',
            ],
        ],
        'education' => [
            [
                'degree' => 'BSc (Hons) in Software Engineering',
                'school' => 'Asia Pacific University (APU)',
                'year' => '03/2025',
            ],
            [
                'degree' => 'BSc (Hons) in Earth and Environmental Science',
                'school' => "Sana'a University, Yemen",
                'year' => '10/2020',
            ],
            [
                'degree' => 'BSc (Hons) in Software Engineering',
                'school' => 'De Montfort University',
                'year' => '03/2025',
            ],
        ],
        'certificates' => [
            [
                'name' => 'Backend Development Diploma (.NET)',
                'issuer' => 'EraaSoft',
                'date' => 'Sep 2026',
                'url' => 'https://www.linkedin.com/in/saleh-aldhaheri-625b09419/overlay/Certifications/2009870546/treasury/',
            ],
            [
                'name' => 'CCNA: Introduction to Networks',
                'issuer' => 'Cisco',
                'date' => 'Jul 2022',
            ],
        ],
    ],

    'ascii' => [
        'name' => '
███████  █████  ██      ███████ ██   ██      █████  ██   ██ ███    ███ ███████ ██████
██      ██   ██ ██      ██      ██   ██     ██   ██ ██   ██ ████  ████ ██      ██   ██
███████ ███████ ██      █████   ███████     ███████ ███████ ██ ████ ██ █████   ██   ██
     ██ ██   ██ ██      ██      ██   ██     ██   ██ ██   ██ ██  ██  ██ ██      ██   ██
███████ ██   ██ ███████ ███████ ██   ██     ██   ██ ██   ██ ██      ██ ███████ ██████
            ',
        'software' => '
 ╦┬ ┬┌┐┌┬┌─┐┬─┐  ┌─┐┌─┐┌─┐┌┬┐┬ ┬┌─┐┬─┐┌─┐  ╔═╗┌┐┌┌─┐┬┌┐┌┌─┐┌─┐┬─┐
 ║│ ││││││ │├┬┘  └─┐│ │├┤  │ │││├─┤├┬┘├┤   ║╣ ││││ ┬││││├┤ ├┤ ├┬┘
╚╝└─┘┘└┘┴└─┘┴└─  └─┘└─┘└   ┴ └┴┘┴ ┴┴└─└─┘  ╚═╝┘└┘└─┘┴┘└┘└─┘└─┘┴└─',
        'keyboard' => "
 -------------------------------------------------------------------------------------------------------------------
|  ___       ___  ___  ___  ___    ___  ___  ___  ___     ___  ___  ___  ___    ___  ___  ___                        |
| |esc|     | F1|| F2|| F3|| F4|  | F5|| F6|| F7|| F8|   | F9||F10||F11||F12|  |pri||scr||bre|        Saleh Ahmed    |
| |___|     |___||___||___||___|  |___||___||___||___|   |___||___||___||___|  |_nt||oll||_ak|                       |
|                                                                                                                    |
|  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ________    ___  ___  ___   ___  ___  ___  ___   |
| |°  ||!  ||   ||§  ||$  ||%  ||&  ||  ||(  ||)  ||=  ||?  ||¸  || --     |  |ins||pos||up |  |Num||   ||*  ||-  |  |
| |^__||1__||2__||3__||4__||5__||6__||7_{||8_[||9_]||0_}||ß_\||__||________|  |___||__1||___|  |___||___||___||___|  |
|  _____  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ______    ___  ___  ___    ___  ___  ___  ___  |
| ||-- ||Q  ||W  ||E  ||R  ||T  ||Y  ||U  ||I  ||O  ||P  ||Ü  ||*  ||      |  |del||end||do |  |7  ||8  ||9  ||+  |  |
| |_--|||__@||___||___||___||___||___||___||___||___||___||___||+_~||_    ||  |___||___||_wn|  |___||___||___||   |  |
|  _______  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___ | - |                    ___  ___  ___ |   |  |
| | |     ||A  ||S  ||D  ||F  ||G  ||H  ||J  ||K  ||L  ||Ö  ||Ä  ||  ||    |                   |4  ||5  ||6  ||   |  |
| |_v_____||___||___||___||___||___||___||___||___||___||___||___||#__||____|                   |___||___||___||___| |
|  _____  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ___  ____________        ___         ___  ___  ___  ___  |
| | ^   ||   ||Z  ||X  ||C  ||V  ||B  ||N  ||M  ||;  ||:  ||_  || ^          |      | ^ |       |1  ||2  ||3  ||e  | |
| |_|___||___||___||___||___||___||___||___||___||,__||.__||-__||_|__________|      |_|_|       |___||___||___||n  | |
|  _____       _____  _________________________________  ______        _____    ___  ___  ___    ________  ___ |t  | |
| |Ctrl |     |Alt  ||                                 ||Alt Gr|      |Ctrl |  | <-|| | ||-> |  |0       ||,  ||e| | |
| |_____|     |_____||_________________________________||______|      |_____|  |___||_v_||___|  |________||___||r__| |
|                                                                                                                    |
----------------------------------------------------------------------------------------------------------------------
",
        'mouse' => '
                              ue$$$$$$$$$e.
                          o$$$P""     "#$$$b
                        d$$$"            ?$$N
                      x$$$"               $$$
                     d$$$     .ueeeeeeeeee$$$beeeeeu
                    @$$$     d$            $>      $$
$$$$      $$            $>      4$
$$$.             .$$$P       $$            $>      4$
?$$$L           z$$$"        $$eeeeeeeeeeee$beeeeeed$
  *$$$e..   .z$$$$"          $$                    4$
    ^#R$$$$$$$*"             $$                    4$
                             $$                    4$
$$                    4$
$$                    4$
$$                    4$
$$                    4$
                             9$                    d$
$k                   $F
                               $N                z$"
                                \'*$o.         .e$"
                                    "#*$$$$P*""

             ',
    ],
    'projects' => [
        'categories' => ['all', 'web', 'ai'],
        'data' => [
            'portfolio-terminal' => [
                'name' => 'Portfolio Terminal',
                'categories' => ['web', 'ai'],
                'image' => 'images/projects/portfolio.webp',
                'info' => 'A command-style developer portfolio built with Laravel and Livewire, featuring an interactive terminal-inspired interface.',
                'github' => 'https://github.com/saleh-aldhaheri/portfolio',
                'link' => null,
            ],
            'blog-system' => [
                'name' => 'Blog System',
                'categories' => ['web'],
                'image' => 'images/projects/zenith.webp',
                'info' => 'A Laravel REST API for a blogging platform with authentication, post management, comments, interactions, follows, notifications, media uploads, and real-time WebSocket events.',
                'github' => 'https://github.com/saleh-aldhaheri/blog',
                'link' => 'http://app.zenith-blog.blog/',
            ],
            'yalla-chat' => [
                'name' => 'Yalla Chat',
                'categories' => ['web', 'ai'],
                'image' => 'images/projects/yalla-chat.webp',
                'info' => 'A real-time chat application with private and group messaging, AI agents, scheduled messages, Google Calendar integration, and email and WhatsApp notifications.',
                'github' => 'https://github.com/saleh-aldhaheri/yallachat',
                'link' => 'https://yallachat.zenith-blog.blog/',
            ],
            'rapix' => [
                'name' => 'Rapix',
                'categories' => ['web'],
                'image' => 'images/projects/rapix.webp',
                'info' => 'A custom PHP framework project focused on building reusable application infrastructure with routing, dependency injection, middleware, database access, authentication, and testing.',
                'github' => 'https://github.com/saleh-aldhaheri/Rapix',
                'link' => null,
            ],
            'recipe-system' => [
                'name' => 'Recipe Management System',
                'categories' => ['web'],
                'image' => null,
                'info' => 'A recipe and inventory management system for professional kitchens, with recipe scheduling, ingredient tracking, automatic inventory updates, transaction history, dashboards, and Excel imports.',
                'github' => 'https://github.com/saleh-aldhaheri/recipe_system',
                'link' => null,
            ],
        ],
    ],

    'themes' => [
        'dark',
        'light',
        'dracula',
        'nord',
        'gruvbox',
        'tokyo-night',
        'catppuccin',
        'solarized',
        'one-dark',
        'rose-pine',
        'kanagawa',
        'monokai',
        'everforest',
    ],

    'social' => [
        'twitter' => [
            'name' => 'Twitter',
            'link' => 'https://twitter.com/',
        ],
        'instagram' => [
            'name' => 'Instagram',
            'link' => 'https://instagram.com/',
        ],
        'github' => [
            'name' => 'Github',
            'link' => 'https://github.com/saleh-aldhaheri',
        ],
        'linkedin' => [
            'name' => 'LinkedIn',
            'link' => 'https://www.linkedin.com/in/saleh-aldhaheri-625b09419/',
        ],
    ],
];
