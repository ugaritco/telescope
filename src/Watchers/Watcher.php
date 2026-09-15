<?php

namespace Ugarit\Telescope\Watchers;

abstract class Watcher
{
    /**
     * The configured watcher options.
     *
     * @var array
     */
    public $options = [];

    /**
     * Create a new watcher instance.
     *
     * @param  array  $options
     * @return void
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Register the watcher.
     *
     * @param  \Heritage\Contracts\Foundation\Application  $app
     * @return void
     */
    abstract public function register($app);
}
