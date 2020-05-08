<?php

namespace Express;

class Visitor
{
    /**
     * Builds the visitor data array with supplied data or blank data
     *
     */
    public function __construct()
    {
        if (Session::get('user') === null) {
            Session::set('user', Configuration::load('user'));
        }
    }

    /**
     * Sets the user data in session to the new array, maintaining
     * original data structure.
     *
     * @param array $data
     *
     * @return void
     */
    public function set(array $data = []) : void
    {
        if ($data === []) {
            return;
        }

        Session::set('user', array_merge(Session::get('user'), $data));
    }

    /**
     * Find the dot pattern within the user array
     *
     * @param string $pattern
     *
     * @return mixed
     */
    public function get(?string $pattern = null)
    {
        if ($pattern === null) {
            return Session::get('user');
        }

        $user = Session::get('user');

        $jumps = explode('.', $pattern);
        $hay = &$user;

        foreach ($jumps as $jump) {
            if (!isset($hay[$jump])) {
                return null;
            }

            $hay = $user[$jump];
        }

        return $hay;
    }
}
