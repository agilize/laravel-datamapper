<?php

namespace Agilize\LaravelDataMapper;

use Agilize\LaravelDataMapper\Tests\ServiceProvider\ServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;

class MigrateDatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function testItRunsTheMigrations()
    {
        $user = \DB::table('test_users')->where('id', '=', 1)->first();

        $this->assertEquals('testuser@agilize.com', $user->email);
        $this->assertTrue(\Hash::check('123456', $user->password));

        $this->assertEquals([
            'id',
            'email',
            'password',
            'created_at',
            'updated_at',
        ], \Schema::getColumnListing('test_users'));

        $user = \DB::table('test_user_roles')->where('id', '=', 1)->first();

        $this->assertEquals('admin', $user->role);

        $this->assertEquals([
            'id',
            'test_user_id',
            'role',
            'created_at',
            'updated_at',
        ], \Schema::getColumnListing('test_user_roles'));
    }
}