<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConnectionTest extends TestCase
{
    /**
     * Verify that tests use the correct separate test database
     */
    public function test_tests_use_separate_test_database(): void
    {
        $driver = DB::connection()->getDriverName();
        $database = DB::connection()->getDatabaseName();
        
        // Should be MySQL for tests
        $this->assertEquals('mysql', $driver);
        
        // Should be the test database, not the local one
        $this->assertEquals('revesta_testing', $database);
    }
}
