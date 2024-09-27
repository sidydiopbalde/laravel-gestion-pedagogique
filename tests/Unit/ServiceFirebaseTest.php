<?php

namespace Tests\Unit;

use App\Services\ServiceFirebase;
use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;
use PHPUnit\Framework\TestCase;
use Mockery;

class ServiceFirebaseTest extends TestCase
{
    protected $firebaseService;
    protected $firebaseMock;

    public function setUp(): void
    {
        parent::setUp();

        // Créer un mock de la classe Database
        $this->firebaseMock = Mockery::mock(Database::class);

        // Mock du factory Firebase pour retourner notre mock de Database
        $factoryMock = Mockery::mock(Factory::class);
        $factoryMock->shouldReceive('withServiceAccount')->andReturnSelf();
        $factoryMock->shouldReceive('withDatabaseUri')->andReturnSelf();
        $factoryMock->shouldReceive('createDatabase')->andReturn($this->firebaseMock);

        // Injecter le mock dans notre service
        $this->firebaseService = new ServiceFirebase($factoryMock);
    }

    public function tearDown(): void
    {
        // Nettoyer les mocks après chaque test
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_create_a_record_in_firebase()
    {
        $path = 'users';
        $data = ['name' => 'Test User', 'email' => 'test@example.com'];

        // Mock la méthode push et set dans Firebase
        $referenceMock = Mockery::mock();
        $referenceMock->shouldReceive('push->getKey')->andReturn('testKey');
        $referenceMock->shouldReceive('getChild->set')->with($data);

        $this->firebaseMock->shouldReceive('getReference')
            ->with($path)
            ->andReturn($referenceMock);

        // Appeler la méthode create et vérifier le résultat
        $key = $this->firebaseService->create($path, $data);
        $this->assertEquals('testKey', $key);
    }

    /** @test */
    public function it_can_find_a_record_in_firebase()
    {
        $path = 'users';
        $id = 'testKey';

        $expectedData = ['name' => 'Test User', 'email' => 'test@example.com'];

        // Mock la méthode getValue dans Firebase
        $referenceMock = Mockery::mock();
        $referenceMock->shouldReceive('getValue')->andReturn($expectedData);

        $this->firebaseMock->shouldReceive('getReference')
            ->with($path.'/'.$id)
            ->andReturn($referenceMock);

        // Appeler la méthode find et vérifier le résultat
        $result = $this->firebaseService->find($path, $id);
        $this->assertEquals($expectedData, $result);
    }

    /** @test */
    public function it_can_update_a_record_in_firebase()
    {
        $path = 'users';
        $id = 'testKey';
        $data = ['name' => 'Updated User'];

        // Mock la méthode update dans Firebase
        $referenceMock = Mockery::mock();
        $referenceMock->shouldReceive('update')->with($data);

        $this->firebaseMock->shouldReceive('getReference')
            ->with($path.'/'.$id)
            ->andReturn($referenceMock);

        // Appeler la méthode update et vérifier qu'il n'y a pas d'exception
        $this->firebaseService->update($path, $id, $data);
        $this->assertTrue(true); // Si aucun problème n'est survenu
    }

    /** @test */
    public function it_can_delete_a_record_in_firebase()
    {
        $path = 'users';
        $id = 'testKey';

        // Mock la méthode remove dans Firebase
        $referenceMock = Mockery::mock();
        $referenceMock->shouldReceive('remove');

        $this->firebaseMock->shouldReceive('getReference')
            ->with($path.'/'.$id)
            ->andReturn($referenceMock);

        // Appeler la méthode delete et vérifier qu'il n'y a pas d'exception
        $this->firebaseService->delete($path, $id);
        $this->assertTrue(true); // Si aucun problème n'est survenu
    }
}
