<?php

namespace Express;

final class Visitor
{
    /**
     * Stores information about the visitor, including guest status, etc.
     */
    protected $information = [
        'guest' => true,
    ];

    private static $instance = null;

    /**
     * Builds the visitor data array with supplied data or blank data
     */
    public function __construct(array $data = [])
    {
        if (self::$instance) {
            return self::$instance;
        }

        $this->information += $data;
        return self::$instance = $this;
    }

    /**
     *
     *
     * @return void
     */
    public function identify(array $data = [])
    {
        $_SESSION['user'] = $_SESSION['user'] ?? [];

        $defaults = Configuration::load('user');

        array_walk($defaults, function ($attribute, $key) use ($data) {
            $_SESSION['user'][$key] = $data[$key] ?? $attribute;
        });

        $this->mergeData($_SESSION['user']);
    }

    /**
     * Finds the role of the visitor
     *
     * @return string
     */
    public function role()
    {
        return $this->information['role'] ?? 'uest';
    }

    /**
     * Merges and overrides the data that is currently existing on the data structure
     */
    public function mergeData(array $data = [])
    {
        if (empty($this->information)) {
            $this->information = $data;
            return;
        }

        foreach ($data as $key => $attribute) {
            $this->information[$key] = $_SESSION['user'][$key] = $attribute;
        }
    }

    /**
     * Opens access to the Visitor data
     */
    public function __invoke($key)
    {
        return $this->information[$key] ?? null;
    }

    /**
     * Getter for the Visitor data array
     */
    public function __get($key)
    {
        return $this->information[$key] ?? null;
    }
}
