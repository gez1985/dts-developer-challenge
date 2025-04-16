<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Set up the test environment.
     *
     * This method is called before each test is executed.
     * It ensures that tests only run in the 'testing' environment to prevent
     * unintended data modification or disruption in other environments.
     *
     * @throws \Exception if the current application environment is not 'testing'.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite') {
            throw new \Exception('Tests must run with the test database.');
        }

        if (app()->environment() !== 'testing') {
            throw new \Exception('You can only run tests in the testing environment.');
        }
    }
}
