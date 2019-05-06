<?php

namespace Express;

class Authorization
{

    /**
     * Checks to see if a route is accessible to the user's permissions
     *
     * @return bool
     */
    public static function check(Request $request)
    {

        extract(
            self::getPermissions($request('uri'), Configuration::load('permissions'))
        );

        // There are no guards when there are no defaults
        if (empty($defaults)) {
            return true;
        }

        if (!empty($supercedes) && self::test($_SESSION['user'], $supercedes)) {
            return true;
        }

        if (!empty($requires) && !self::test($_SESSION['user'], $requires)) {
            return false;
        }

        if (!empty($regions) && !self::test($_SESSION['user']['region'], $regions)) {
            return false;
        }

        return self::test($_SESSION['user'], $defaults);
    }

    /**
     * Interprets the permissions file loaded and returns required permissions.
     *
     * @return array
     */
    private static function getPermissions(string $uri, array $permissions)
    {
        $locations = explode('/', $uri);
        array_shift($locations);
        array_pop($locations);

        $supercedes = $permissions['supercede'] ?? [];
        $requires   = $permissions['requires'] ?? [];
        $regions    = $permissions['regions'] ?? [];
        $defaults   = $permissions['defaults'] ?? [];

        foreach ($locations as $location) {
            $permissions = $permissions[$location] ?? $permissions;

            $supercedes = array_merge($supercedes, $permissions['supercede'] ?? []);
            $requires   = array_merge($requires, $permissions['requires'] ?? []);
            $regions    = array_merge($regions, $permissions['regions'] ?? []);
            $defaults   = $permissions['defaults'] ?? $defaults;
        }

        $supercedes = array_unique($supercedes);
        $requires   = array_unique($requires);
        $regions    = array_unique($regions);
        $defaults   = array_unique($defaults);

        return compact('supercedes', 'requires', 'regions', 'defaults');
    }

    /**
     * Tests the permission against the location recursively until access is determined.
     *
     * @return bool
     */
    private static function test($userPermissions, array $requiredPermissions)
    {

        if (is_string($userPermissions)) {
            return in_array($userPermissions, $requiredPermissions);
        }

        $authorized = false;

        foreach ($requiredPermissions as $permission) {
            if ($permission === '*') {
                return true;
            }
            if (!array_key_exists($permission, $userPermissions)) {
                return false;
            }

            $authorized = $authorized ?: (bool) $userPermissions[$permission];
        }

        return $authorized;

    }

    /**
     * Appends a random set of characters based on a provided length to a string.
     *
     * @return string
     */
    public static function appendRandomChar(string $input, int $length = 50)
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charlength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $input .= $characters[rand(0, $charlength - 1)];
        }
        return $input;
    }
}
