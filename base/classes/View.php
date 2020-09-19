<?php

namespace Express;

use Express\Http\Response;

final class View
{
    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;

        Container::store('view', $this);
    }

    public static function resource(string $type, string $path, bool $absolute = false)
    {
        // todo : implement resource loader
    }

    /**
     * @param string|null $view
     *
     * @return false|string
     */
    public function render(string $view = null)
    {
        $view_path = __VIEWDIR__ . '/';
        $file = str_replace('.', '/', $view ?? $this->view) . '.view.php';

        if (!file_exists($view_path . $file)) {
            throw new \RuntimeException('Unable to locate view: ' . $view_path . $file, 500);
        }

        if (!$response = Container::retrieve('response')) {
            $response = Container::store('response', new Response());
        }

        // throw runtime error when it doesn't start
        $response->restart();

        require $view_path . $file;

        return $response->content();
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }
}
