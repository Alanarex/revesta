# Revesta Testing System - Complete Guide

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Database Configuration](#database-configuration)
3. [Running Tests](#running-tests)
4. [Current Test Suite](#current-test-suite)
5. [How Testing Works](#how-testing-works)
6. [Debugging Failed Tests](#debugging-failed-tests)
7. [Writing New Tests](#writing-new-tests)
8. [Troubleshooting](#troubleshooting)

---

## System Architecture

### Testing Stack
- **Framework**: Laravel 12 with PHPUnit 11.5
- **Database**: Separate MySQL test database (`revesta_testing`)
- **Configuration**: `phpunit.xml` + `.env.testing`
- **Base Class**: `tests/TestCase.php` with `RefreshDatabase` trait

### How It Works

```
┌─────────────────────────────────────┐
│  Run: php artisan test              │
└────────────────┬────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  Load phpunit.xml configuration     │
│  - APP_ENV=testing                  │
│  - DB_DATABASE=revesta_testing      │
│  - Other test settings              │
└────────────────┬────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  Load .env.testing environment      │
│  (overrides local .env)             │
└────────────────┬────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  For each test class:               │
│  ├─ RefreshDatabase trait executes  │
│  ├─ Runs all migrations on test DB  │
│  ├─ Executes test code              │
│  ├─ Truncates/clears tables         │
│  └─ Repeats for next test           │
└────────────────┬────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────┐
│  Local database (revesta_db)        │
│  remains UNTOUCHED                  │
└─────────────────────────────────────┘
```

---

## Database Configuration

### ✅ Current Setup (Recommended)

Tests use a **separate MySQL test database** (`revesta_testing`) which:
- ✅ Does NOT affect your local/development database (`revesta_db`)
- ✅ Completely isolated from production
- ✅ Automatically migrated before each test
- ✅ Automatically cleaned between tests via `RefreshDatabase` trait

**Configuration Files:**
- `phpunit.xml` - PHPUnit environment variables
- `.env.testing` - Laravel test environment variables

**Database Details:**
```
Local Database:     revesta_db       (YOUR DEVELOPMENT DATA)
Test Database:      revesta_testing  (AUTOMATIC, ISOLATED)
```

### Create Test Database (One-time)

```bash
# Create the test database
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS revesta_testing;"

# Verify it was created
mysql -u root -proot -e "SHOW DATABASES;" | grep revesta_testing
```

### Alternative: File-based SQLite

If you prefer a file-based test database instead:

**In `.env.testing`:**
```env
DB_CONNECTION=sqlite
DB_DATABASE=storage/app/test-database.sqlite
```

**Update `phpunit.xml`:**
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value="storage/app/test-database.sqlite"/>
```

**Add to `.gitignore`:**
```
storage/app/test-database.sqlite
```

**Note**: File SQLite is slower than MySQL but requires no database server

### Current Configuration Files

**phpunit.xml (Test Settings):**
```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_HOST" value="127.0.0.1"/>
<env name="DB_PORT" value="3306"/>
<env name="DB_DATABASE" value="revesta_testing"/>
<env name="DB_USERNAME" value="root"/>
<env name="DB_PASSWORD" value="root"/>
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
```

**.env.testing (Environment Variables):**
```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=revesta_testing
DB_USERNAME=root
DB_PASSWORD=root
```

---

## Running Tests

### Basic Commands

```bash
# Run all tests
php artisan test

# Run all tests without coverage (faster)
php artisan test --no-coverage

# Run specific test file
php artisan test tests/Feature/Controllers/AddressControllerTest.php

# Run specific test method
php artisan test --filter="test_create_address_with_valid_data"

# Run tests matching a pattern
php artisan test --filter="Address"

# Show verbose output
php artisan test --verbose

# Generate coverage report
php artisan test --coverage

# Generate JUnit XML report
php artisan test --log-junit=junit.xml
```

### Filtering Tests

```bash
# Run only Unit tests
php artisan test tests/Unit

# Run only Feature tests
php artisan test tests/Feature

# Run only Auth tests
php artisan test tests/Feature/Auth

# Run tests containing "label" in the name
php artisan test --filter="label"
```

### Test Groups (Using @group annotations)

```bash
# If tests use @group fast annotation
php artisan test --group=fast

# Exclude slow tests
php artisan test --exclude-group=slow
```

---

## Current Test Suite

### Summary

**Total: ~16 Test Files**
- Feature Tests: ~11 files (API, Controllers, Forms, Auth)
- Unit Tests: ~2 files (Services, Models)
- Database Connection: 1 verification test
- Example Tests: 2 placeholder tests

### Feature Tests (Integration/HTTP Tests)

#### Authentication Tests (`tests/Feature/Auth/`)
- `AuthenticationTest.php` - Login/logout functionality
- `EmailVerificationTest.php` - Email verification workflow
- `PasswordConfirmationTest.php` - Password confirmation
- `PasswordResetTest.php` - Password reset flow
- `PasswordUpdateTest.php` - Password update functionality
- `RegistrationTest.php` - User registration

#### Address Management Tests
**`tests/Feature/Controllers/AddressControllerTest.php`** (20+ tests)
- Page loading (index, create, edit)
- CRUD operations via HTTP
- Form submission and validation
- Flash messages and redirects
- Label auto-generation
- Label security (backend prevents manipulation)
- Breadcrumbs rendering
- Authorization checks

**`tests/Feature/Requests/AddressRequestValidationTest.php`** (20+ tests)
- Required field validation (street, postal_code, city)
- Field length constraints
- Coordinate range validation (-90 to 90 latitude, -180 to 180 longitude)
- Special characters handling
- Empty optional fields
- Validation error messages

**`tests/Feature/Endpoints/AddressListingEndpointTest.php`** (12+ tests)
- JSON API endpoint (`GET /admin/addresses/list`)
- Pagination
- Search functionality
- Sorting (ascending/descending)
- Response structure validation
- Data integrity checks

#### Other Feature Tests
- `DatabaseConnectionTest.php` - Verifies test database is configured correctly
- `ExampleTest.php` - Example test (can be removed)
- `ProfileTest.php` - User profile related tests

### Unit Tests (Isolated Component Tests)

**`tests/Unit/Services/AddressServiceTest.php`** (13+ tests)
- Label generation with various field combinations
- Address creation and updates
- Label regeneration on field changes
- Label security (ignores submitted labels)
- Address deletion
- Address retrieval

**`tests/Unit/Models/AddressModelTest.php`** (14+ tests)
- Model attributes
- Factory creation
- Timestamps (created_at, updated_at)
- Polymorphic relationships (users, housings)
- Cascading deletes
- Query scopes (if any)

---

## How Testing Works

### Test Lifecycle (Per Test)

```
1. Setup Phase
   ├─ RefreshDatabase migrates test database
   ├─ All migrations run on revesta_testing
   ├─ Database is in clean state
   └─ Seeders do NOT run (unless specified)

2. Test Execution
   ├─ Test code runs
   ├─ Assertions are checked
   └─ Database changes happen in test DB only

3. Teardown Phase
   ├─ Database tables are truncated
   ├─ Next test starts with clean database
   └─ Local database (revesta_db) is NEVER touched
```

### RefreshDatabase Trait

The base `tests/TestCase.php` includes the `RefreshDatabase` trait:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    // This ensures the test database is reset before each test
}
```

**What it does:**
- Runs all pending migrations on the test database
- Truncates tables after each test
- Prevents database state from leaking between tests
- Guarantees each test starts with a clean database

### Test Isolation

Each test is completely isolated:
```php
public function test_first()
{
    Address::factory()->create(['city' => 'Paris']);
    $this->assertDatabaseHas('addresses', ['city' => 'Paris']);
}

public function test_second()
{
    // This test does NOT see the address created in test_first
    $this->assertDatabaseMissing('addresses', ['city' => 'Paris']);
    // Database is fresh for this test
}
```

---

## Debugging Failed Tests

### 1. Run Failing Test in Isolation

```bash
# Run just the failing test
php artisan test tests/Feature/Controllers/AddressControllerTest.php --filter="test_create_address_with_valid_data"

# With verbose output
php artisan test tests/Feature/Controllers/AddressControllerTest.php --filter="test_create_address_with_valid_data" --verbose
```

### 2. Inspect Test Database During Test

Add temporary debugging within a test:

```php
public function test_something()
{
    Address::factory()->create(['city' => 'Paris']);
    
    // Enable query logging
    DB::enableQueryLog();
    
    $addresses = Address::all();
    
    // Dump the queries that were run
    dump(DB::getQueryLog());
    
    // Dump the results
    dd($addresses);
}
```

### 3. Check Database State with Assertions

```php
public function test_address_creation()
{
    // Assert database HAS something
    $this->assertDatabaseHas('addresses', [
        'street' => '123 Main St',
        'city' => 'Paris',
    ]);
    
    // Assert database MISSING something
    $this->assertDatabaseMissing('addresses', [
        'city' => 'London',
    ]);
    
    // Count records
    $this->assertCount(1, Address::all());
}
```

### 4. Inspect HTTP Responses

```php
public function test_form_validation()
{
    $response = $this->postCsrf('/addresses', [
        'street' => '',  // Invalid
        'city' => 'Paris',
        'postal_code' => '75000',
    ]);
    
    // Check status code
    $response->assertStatus(422);  // Unprocessable Entity
    
    // Check error message
    $response->assertSessionHasErrors('street');
    
    // Check error text
    $response->assertSessionHasErrors('street', 'The street field is required');
    
    // Dump response for inspection
    dump($response->getContent());
}
```

### 5. Check Test Output

```bash
# Verbose mode shows what each test does
php artisan test --verbose

# Test with pretty printer (colors, progress)
php artisan test --colors

# Show test names as they run
php artisan test -v 2>&1 | head -50
```

### 6. Common Debug Patterns

**When a query fails:**
```php
public function test_query_issue()
{
    try {
        $result = Address::where('city', 'Paris')->get();
        dump($result);
    } catch (\Exception $e) {
        dump("Error: " . $e->getMessage());
    }
}
```

**When HTTP request fails:**
```php
public function test_request_issue()
{
    $response = $this->post('/addresses', $data);
    
    // See actual response
    dump([
        'status' => $response->getStatusCode(),
        'content' => $response->getContent(),
        'json' => $response->json(),
    ]);
}
```

**When assertion fails:**
```php
public function test_assertion_issue()
{
    $address = Address::factory()->create(['city' => 'Paris']);
    
    dump([
        'created_address' => $address,
        'in_database' => Address::find($address->id),
    ]);
    
    $this->assertTrue(true);  // Passes
}
```

### 7. Check Configuration

Verify tests are using the correct database:

```bash
php artisan test tests/Feature/DatabaseConnectionTest.php --no-coverage
```

This test confirms:
- Database driver is correct (mysql)
- Database name is `revesta_testing` (not your local db)
- Connection is working

---

## Writing New Tests

### Test File Location and Naming

```bash
# Feature test (HTTP, integration)
tests/Feature/Controllers/MyFeatureTest.php
tests/Feature/Endpoints/MyEndpointTest.php

# Unit test (isolated, fast)
tests/Unit/Services/MyServiceTest.php
tests/Unit/Models/MyModelTest.php
```

### Basic Test Structure (AAA Pattern)

```php
<?php

namespace Tests\Feature;

use App\Models\Address;
use Tests\TestCase;

class AddressCreationTest extends TestCase
{
    public function test_create_address_with_valid_data()
    {
        // ARRANGE - Set up test data
        $data = [
            'street' => '123 Main St',
            'city' => 'Paris',
            'postal_code' => '75000',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ];
        
        // ACT - Perform the action
        $response = $this->postCsrf('/addresses', $data);
        
        // ASSERT - Verify the result
        $response->assertRedirect('/addresses');
        $this->assertDatabaseHas('addresses', [
            'city' => 'Paris',
            'street' => '123 Main St',
        ]);
    }
}
```

### Feature Test Example

```php
<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    public function test_user_can_view_address_form()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/addresses/create');
        
        $response->assertStatus(200)
            ->assertViewIs('addresses.create')
            ->assertViewHas('breadcrumbs');
    }
    
    public function test_form_requires_city()
    {
        $response = $this->postCsrf('/addresses', [
            'street' => 'Main St',
            'postal_code' => '75000',
            // Missing city
        ]);
        
        $response->assertSessionHasErrors('city');
        $this->assertDatabaseMissing('addresses', [
            'street' => 'Main St',
        ]);
    }
}
```

### Unit Test Example

```php
<?php

namespace Tests\Unit\Services;

use App\Models\Address;
use App\Services\AddressService;
use Tests\TestCase;

class AddressServiceTest extends TestCase
{
    private AddressService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AddressService();
    }
    
    public function test_label_generation_includes_street()
    {
        $address = Address::factory()->create([
            'street' => 'Oak Avenue',
            'city' => 'Paris',
            'postal_code' => '75000',
        ]);
        
        $address = $this->service->regenerateLabel($address);
        
        $this->assertStringContainsString('Oak Avenue', $address->label);
    }
}
```

### Using Factories

```php
// Create a single model
$address = Address::factory()->create();

// Create with specific attributes
$address = Address::factory()->create([
    'city' => 'Paris',
    'street' => 'Main St',
]);

// Create multiple
$addresses = Address::factory(5)->create();

// Create without saving to DB
$address = Address::factory()->make();

// Create and customize
$address = Address::factory()
    ->count(3)
    ->state(['city' => 'Paris'])
    ->create();
```

### Common Assertions

```php
// HTTP Response
$response->assertStatus(200);
$response->assertRedirect('/path');
$response->assertViewIs('template');
$response->assertSessionHasErrors('field');
$response->assertSessionMissing('key');

// Database
$this->assertDatabaseHas('table', ['field' => 'value']);
$this->assertDatabaseMissing('table', ['field' => 'value']);
$this->assertDatabaseCount('table', 5);

// Collections
$this->assertCount(3, $collection);
$this->assertTrue($condition);
$this->assertEquals('expected', $actual);
$this->assertNull($value);
```

### Test Classes Should Have

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;  // ✅ Extends TestCase (not just PHPUnit\TestCase)

class MyTest extends TestCase
{
    // ✅ No RefreshDatabase here - it's in base TestCase
    
    public function test_something()
    {
        // Tests can use database safely
    }
}
```

---

## Troubleshooting

### Problem: "Access Denied for user 'root'@'localhost'"

**Cause**: Wrong MySQL password in configuration

**Solution**: Update `phpunit.xml` and `.env.testing`
```xml
<env name="DB_PASSWORD" value="your_correct_password"/>
```

### Problem: "SQLSTATE[HY000]: General error: 1030 Got error..."

**Cause**: Test database doesn't exist

**Solution**: Create it
```bash
mysql -u root -proot -e "CREATE DATABASE revesta_testing;"
```

### Problem: "Table not found" errors

**Cause**: Migrations didn't run (RefreshDatabase not included)

**Solution**: Verify `tests/TestCase.php` has:
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;  // ✅ Must be here
}
```

### Problem: Tests are slow

**Causes & Solutions:**
- Not using `--no-coverage` → add it: `php artisan test --no-coverage`
- Running too many tests → filter: `php artisan test --filter="specific"`
- Database queries in loops → use factory relationships
- Seeders running → disable them in test configuration

### Problem: Local database was accidentally modified

**Cause**: Tests were configured to use `revesta_db` instead of `revesta_testing`

**Check:**
```bash
php artisan test tests/Feature/DatabaseConnectionTest.php

# Look for:
# - driver: mysql ✅
# - database: revesta_testing ✅  (NOT revesta_db)
```

### Problem: "CSRF token mismatch"

**Cause**: Not using `postCsrf()` helper method

**Solution**: Use CSRF helpers in tests
```php
// ❌ Wrong
$this->post('/addresses', $data);

// ✅ Correct (auto-includes CSRF token)
$this->postCsrf('/addresses', $data);

// Or manual
$this->post('/addresses', $this->withCsrfToken($data));
```

### Problem: "Class not found" or import errors

**Cause**: Test namespace mismatch

**Check:**
```php
// ✅ Correct namespace
namespace Tests\Feature\Controllers;

// ❌ Wrong (should be in Tests namespace)
namespace Feature\Controllers;
```

### Clear Test Configuration Cache (if needed)

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Performance & Best Practices

### Keep Tests Fast

```php
// ✅ Good - Minimal database operations
public function test_label_generation()
{
    $address = Address::factory()->create(['street' => 'Main']);
    $this->assertTrue($address->label->contains('Main'));
}

// ❌ Slow - Unnecessary operations
public function test_address()
{
    for ($i = 0; $i < 1000; $i++) {
        Address::factory()->create();  // Too many!
    }
}
```

### One Assertion Per Test (When Possible)

```php
// ✅ Good - Clear what failed
public function test_street_appears_in_label()
{
    $address = Address::factory()->create(['street' => 'Oak Ave']);
    $this->assertStringContainsString('Oak Ave', $address->label);
}

// ✅ Also OK - Related assertions
public function test_complete_label_format()
{
    $address = Address::factory()->create(['street' => 'Oak Ave', 'city' => 'Paris']);
    $label = $address->label;
    $this->assertStringContainsString('Oak Ave', $label);
    $this->assertStringContainsString('Paris', $label);
}
```

### Use Descriptive Test Names

```php
// ❌ Bad
public function test_address()
public function test_it()

// ✅ Good
public function test_address_label_includes_street_and_city()
public function test_form_validation_requires_city_field()
```

### Avoid Test Interdependencies

```php
// ❌ Bad - Tests depend on order
public function test_a_create_address() { /* ... */ }
public function test_b_update_same_address() { /* uses address from test_a */ }

// ✅ Good - Tests are independent
public function test_create_address() { /* ... */ }
public function test_update_address() { /* creates fresh address */ }
```

---

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: revesta_testing
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
      - uses: actions/checkout@v2
      - uses: php-actions/setup-php@v1
        with:
          php-version: '8.2'
          
      - name: Install Dependencies
        run: composer install
        
      - name: Create Test Database
        run: mysql -h 127.0.0.1 -uroot -proot -e "CREATE DATABASE revesta_testing;"
        
      - name: Run Tests
        run: php artisan test --no-coverage
```

---

## Future Test Coverage

As the system grows, consider adding tests for:
- [ ] Blog system (CRUD, comments, likes, bookmarks)
- [ ] User profiles and roles
- [ ] Aid simulations
- [ ] Notifications
- [ ] Search and filtering across all features
- [ ] Performance-critical queries
- [ ] API rate limiting
- [ ] File uploads
- [ ] Email sending
- [ ] Background jobs (queue)

**Note**: Tests are designed to be maintainable and should be updated as features change.

## Test Categories

### Unit Tests (22 tests)

#### AddressServiceTest (13 tests)
- Label generation with various field combinations
- Address creation and updates
- Label regeneration on updates
- Label security (ignores submitted labels)
- Address deletion
- Address retrieval

**Coverage:**
- `App\Services\AddressService`

#### AddressRepositoryTest (24 tests)
- Pagination
- Sorting (ascending/descending)
- Search by: id, label, street, city, postal_code, departement
- Case-insensitive search
- Partial matching
- Combined filters
- CRUD operations

**Coverage:**
- `App\Repositories\AddressRepository`

#### AddressModelTest (14 tests)
- Polymorphic relationships (users, housings)
- Model attributes and factory
- Timestamps
- Cascading deletes
- Multiple relations

**Coverage:**
- `App\Models\Address`

### Feature Tests (34+ tests)

#### AddressControllerTest (20 tests)
- Page loading (index, create, edit)
- CRUD operations via HTTP
- Form submission and validation
- Flash messages
- Redirects
- Label auto-generation
- Label security (ignores user input)
- Breadcrumbs rendering

**Coverage:**
- `App\Http\Controllers\AddressController`
- Views: create, edit, index

#### AddressRequestValidationTest (20 tests)
- Required field validation
- Field length constraints
- Coordinate range validation
- Special characters handling
- Empty optional fields
- Authorization checks

**Coverage:**
- `App\Http\Requests\CreateAddressRequest`
- `App\Http\Requests\UpdateAddressRequest`

#### AddressListingEndpointTest (12 tests)
- JSON API endpoint
- Pagination
- Search functionality
- Sorting
- Response structure
- Data integrity

**Coverage:**
- API endpoint: `GET /admin/addresses/list`

## Key Test Scenarios

### Label Generation Security
- ✅ Labels are ALWAYS regenerated from address fields
- ✅ User-submitted labels are ignored (prevents manipulation)
- ✅ Backend regeneration even if user bypasses frontend restrictions

### Validation
- ✅ Required fields (street, postal_code, city)
- ✅ Field length constraints
- ✅ Coordinate range validation (-90 to 90 for lat, -180 to 180 for lng)
- ✅ Special characters support

### Search Functionality
- ✅ Search by address ID
- ✅ Search by label, street, city, postal code, department
- ✅ Case-insensitive search
- ✅ Partial string matching

### Polymorphic Relationships
- ✅ Addresses associated with multiple users
- ✅ Addresses associated with multiple housings
- ✅ Proper addressables table population

## Debugging Failed Tests

### Show more information
```bash
php artisan test -v
```

### Run single failing test
```bash
php artisan test tests/Unit/Services/AddressServiceTest.php --filter="test_name_here"
```

### Check database queries
Add to test:
```php
// Enable query logging
DB::enableQueryLog();

// ... your test code ...

// Dump queries
dump(DB::getQueryLog());
```

### Inspect test database state
Add assertions within tests:
```php
$this->assertDatabaseHas('addresses', [
    'street' => 'Expected Street',
    'city' => 'Expected City',
]);
```

## Test Coverage

To generate coverage report:
```bash
php artisan test --coverage
```

Current coverage targets:
- Service layer: 100%
- Repository layer: 100%
- Model layer: 95%+
- Controller layer: 95%+

## Best Practices

### Writing New Tests

1. **Test one thing per test**
   ```php
   public function test_label_includes_street_name()
   {
       // Only test that street is in label, not other components
   }
   ```

2. **Use descriptive names**
   ```php
   // Good
   public function test_update_ignores_submitted_label()
   
   // Bad
   public function test_update()
   ```

3. **Follow AAA pattern**
   ```php
   public function test_something()
   {
       // Arrange
       $address = Address::factory()->create();
       
       // Act
       $result = $this->service->doSomething($address);
       
       // Assert
       $this->assertEquals('expected', $result);
   }
   ```

4. **Use factories**
   ```php
   // Good
   $address = Address::factory()->create(['street' => 'Custom']);
   
   // Avoid
   Address::create(['label' => 'X', 'street' => 'Y', ...]);
   ```

### Keeping Tests Fast

- ✅ Use in-memory SQLite (default)
- ✅ Use factories instead of fixtures
- ✅ Minimize database queries
- ✅ Test the smallest unit possible
- ✅ Mock external services

## Common Issues & Solutions

### Issue: "Production database would be wiped"
**Solution:** Ensure `.env.testing` or `phpunit.xml` has correct database config
```bash
php artisan test --env=testing
```

### Issue: Tests are slow
**Solution:** Already using in-memory SQLite (fastest option)
- Avoid unnecessary factories
- Don't create unnecessary related models

### Issue: Validation tests failing
**Solution:** Check `UpdateAddressRequest` rules are correct
- Required: street, postal_code, city
- Max lengths: street (255), postal_code (10), city (255), etc.

## CI/CD Integration

For GitHub Actions or other CI/CD:

```yaml
- name: Run tests
  run: php artisan test --no-coverage
```

Since tests use in-memory SQLite, they'll run in any environment without database setup.

## Test Reports

Generate test report:
```bash
php artisan test --log-junit=junit.xml
```

## Future Improvements

- [ ] Add test factories for complex scenarios
- [ ] Add integration tests with external APIs
- [ ] Add performance benchmarks
- [ ] Add accessibility tests for views
- [ ] Add E2E tests with Dusk

## Questions?

Refer to:
- `tests/TestCase.php` - Base test class
- `phpunit.xml` - Test configuration
- `.env.testing` - Test environment variables
- Individual test files for examples
