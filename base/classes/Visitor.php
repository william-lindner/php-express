<?php

namespace Express;

class Visitor
{
    protected $data = [];

    /**
     * Builds the visitor data array with supplied data or blank data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
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
        return $this->data['role'] ?? 'Guest';
    }

    /**
     * Merges and overrides the data that is currently existing on the data structure
     */
    public function mergeData(array $data = [])
    {
        if (empty($this->data)) {
            $this->data = $data;
            return;
        }

        foreach ($data as $key => $attribute) {
            $this->data[$key] = $_SESSION['user'][$key] = $attribute;
        }
    }

    /**
     * Opens access to the Visitor data
     */
    public function __invoke($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Getter for the Visitor data array
     */
    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }
}
