<?php
return [
    'Info' => [
        ['command' => 'about',    'description' => 'Show information about me'],
        ['command' => 'projects', 'description' => 'Show my projects'],
        ['command' => 'banner',   'description' => 'Show the welcome banner'],
    ],
    'Contact' => [
        ['command' => 'contact',  'description' => 'Contact details and message form'],
        ['command' => 'linkedin', 'description' => 'Open my LinkedIn profile'],
        ['command' => 'github',   'description' => 'Open my GitHub profile'],
        ['command' => 'email',    'description' => 'Show my email address'],
    ],
    'System' => [
        ['command' => 'theme set <name>', 'description' => 'Change theme: dark, light, dracula'],
        ['command' => 'help',             'description' => 'Show this list'],
    ],
];
