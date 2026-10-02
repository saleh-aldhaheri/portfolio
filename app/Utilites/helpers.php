<?php

function resolveComponents(string $command, array $data = []): array
{
    if ($command == 'default') {
        $command = config('terminal.default');
    }

    $config = config('terminal.'.$command);
    $resolved = [];

    if (! isset($config['component'])) {
        return $resolved;
    }

    $resolved['component'] = $config['component'];
    $resolved['props'] = [];

    if (! empty($config['props']) && ! empty($data)) {

        $allowedKeys = array_flip($config['props']);
        $filteredProps = array_intersect_key($data, $allowedKeys);

        if (! empty($filteredProps)) {
            $resolved['props'] = $filteredProps;
        }
    }

    return $resolved;
}
