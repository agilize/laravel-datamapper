<?php

namespace Agilize\LaravelDataMapper\Tests;

use Agilize\LaravelDataMapper\DataMappingMiddleware;
use Agilize\LaravelDataMapper\Tests\Model\TestUser;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class DataMappingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Define environment setup.
     *
     * @param  Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->app = $app;
        /** @var Kernel $kernel */
        $kernel = $app->make(Kernel::class);
        $kernel->prependMiddleware(DataMappingMiddleware::class);

        $app['config']->set('database.default', 'testing');

        parent::getEnvironmentSetUp($app);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    public function testItShouldReturnPongAsContent()
    {
        $crawler = $this->call('POST', 'web/ping', [], [], [], []);

        $this->assertEquals('Pong', $crawler->getContent());

    }

    public function testItShouldReturnUserPassingIdentityOnPathThroughGetMethod()
    {
        $testUser = \DB::table('test_users')->where('id', '=', 1)->first();
        $crawler = $this->call('GET', 'api/test-user/' . $testUser->id, [], [], [], []);
        $response = json_decode($crawler->getContent(), true);

        $this->assertEquals($testUser->id, $response['id']);
    }

    public function testItShouldReturnUserPassingIdentityOnPathThroughPostMethod()
    {
        $testUser = \DB::table('test_users')->where('id', '=', 1)->first();
        $crawler = $this->call('POST', 'api/test-user/' . $testUser->id, [], [], [], []);
        $response = json_decode($crawler->getContent(), true);

        $this->assertEquals($testUser->id, $response['id']);
    }

    public function testItShouldReturnUserPassingIdentityOnPathThroughPutMethod()
    {
        $testUser = \DB::table('test_users')->where('id', '=', 1)->first();
        $crawler = $this->call('PUT', 'api/test-user/' . $testUser->id, [], [], [], []);
        $response = json_decode($crawler->getContent(), true);

        $this->assertEquals($testUser->id, $response['id']);
    }

    public function testItShouldReturnUserPassingIdentityOnParametersThroughGetMethod()
    {
        $testUser = \DB::table('test_users')->where('id', '=', 1)->first();
        $crawler = $this->call('GET', 'api/test-user', ['test-user' => $testUser->id], [], [], []);
        $response = json_decode($crawler->getContent(), true);

        $this->assertEquals($testUser->id, $response['id']);
    }

    public function testItShouldReturnUserPassingIdentityOnParametersThroughPostMethod()
    {
        $testUser = \DB::table('test_users')->where('id', '=', 1)->first();
        $crawler = $this->call('POST', 'api/test-user', ['test-user' => $testUser->id], [], [], []);
        $response = json_decode($crawler->getContent(), true);

        $this->assertEquals($testUser->id, $response['id']);
    }

    public function testItShouldReturnUserWithRolePassingIdentityOnPathThroughGetMethod()
    {
        $testUser = TestUser::where('id', '=', 1)->with('testUserRole')->first();
        $crawler = $this->call('GET', 'api/test-user/' . $testUser->id, [], [], [], []);
        $response = json_decode($crawler->getContent(), true);
        $role = collect($response['test_user_role'])->first();
        $testUserWithRole = $testUser->testUserRole->first();

        $this->assertEquals(
            $testUserWithRole->id,
            $role['id']
        );
    }

    public function testItShouldNotDataMapPassingIdentityOnPathThroughGetMethod()
    {
        $middleware = new DataMappingMiddleware([
            'entityDirectory' => __DIR__ . '/Model',
            'primaryKeyType' => 'integer',
            'apiVersion' => 'v1',
        ]);
        $request = Request::create('api/test-user', 'GET', ['test-user' => 1]);

        $middleware->handle($request, function ($req) {
            /** @var Request $req */
            $testUserRequest = $req->request->all()['test-user'];
            $testUser = TestUser::where('id', '=', 1)->first();
            $this->assertEquals($testUser, $testUserRequest);
        }, DataMappingMiddleware::NO_RELATIONS);
    }
}
