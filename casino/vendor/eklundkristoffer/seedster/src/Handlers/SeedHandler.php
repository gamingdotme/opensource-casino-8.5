<?php

namespace Seedster\Handlers;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Foundation\Application;

class SeedHandler
{
    /**
     * Holds the Application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Holds all of the seeders.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $seeders;

    /**
     * Create a new instance object.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Support\Collection  $seeders
     */
    public function __construct(Application $app, Collection $seeders)
    {
        $this->app = $app;
        $this->seeders = $seeders;
    }

    /**
     * Register a seeder class.
     *
     * @param  string  $seeder
     * @return void
     */
    public function register($seeder)
    {
        foreach ((array)$seeder as $seed) {
            $this->seeders->push($seed);
        }
    }

    /**
     * Retrieve all the registered seeders.
     *
     * @return \Illuminate\Support\Collection
     */
    public function seeders()
    {
        return $this->seeders;
    }
}
