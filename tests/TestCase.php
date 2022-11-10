<?php

namespace Tests;
use Faker\Generator;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Exception;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;


    private Generator $faker;

    public function setUp()
    : void {

        parent::setUp();
        $this->faker = Factory::create();
        Artisan::call('migrate:refresh');
    }

    public function __get($key) {

        if ($key === 'faker')
            return $this->faker;
        throw new Exception('Unknown Key Requested');
    }
}
