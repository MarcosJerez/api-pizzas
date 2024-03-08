<?php

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Pizza;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class PizzaTest extends ApiTestCase {

    protected function setUp(): void {
        parent::setUp();
        restore_error_handler();
        restore_exception_handler();
    }

    protected function tearDown(): void {
        parent::tearDown();
        restore_error_handler();
        restore_exception_handler();
    }

    public function testGetAllPizzas(): void {
        $response = static::createClient()->request('GET', '/api/pizzas');
        $responseData = json_decode($response->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertArrayHasKey('hydra:totalItems', $responseData);
        $this->assertGreaterThan(0, $responseData['hydra:totalItems']);
    }

    public function testGetOnePizza(): void {
        $response = static::createClient()->request('GET', '/api/pizzas/1');

        $responseData = json_decode($response->getContent(), true);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertArrayHasKey('@type', $responseData);
        $this->assertEquals('Pizza', $responseData['@type']);
    }

    public function testCreatePizza(): void {
        $client = new Client();

        $data = [
            'name' => 'Nueva Pizza',
            'ingredients' => ['Ingrediente1', 'Ingrediente2'],
            'ovenTimeInSeconds' => 3000,
            'special' => false
        ];

        $response = $client->post('http://localhost/api/pizzas', [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode()); // Verificar que se ha creado la pizza correctamente
        $responseData = json_decode($response->getBody(), true);

        // Realizar las aserciones sobre los datos de la pizza creada
        $this->assertArrayHasKey('@id', $responseData);
        $this->assertArrayHasKey('@type', $responseData);
        $this->assertEquals('/api/pizzas/' . $responseData['id'], $responseData['@id']);
        $this->assertEquals('Pizza', $responseData['@type']);
        $this->assertEquals('Nueva Pizza', $responseData['name']);
        $this->assertEquals(['Ingrediente1', 'Ingrediente2'], $responseData['ingredients']);
        $this->assertEquals(3000, $responseData['ovenTimeInSeconds']);
        $this->assertFalse($responseData['special']);
    }

    public function testUpdatePizza(): void {
        $client = self::createClient(); // Crea un cliente para hacer la solicitud

        $updatedData = [
            'name' => 'Pizza actualizada',
            'ingredients' => ['Nuevo Ingrediente1', 'Nuevo Ingrediente2'],
            'ovenTimeInSeconds' => 4000,
            'special' => true
        ];

        $response = $client->request('PUT', '/api/pizzas/1', [
            'json' => $updatedData,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $updatedResponseData = json_decode($response->getContent(), true);

        $this->assertEquals('Pizza actualizada', $updatedResponseData['name']);
        $this->assertEquals(['Nuevo Ingrediente1', 'Nuevo Ingrediente2'], $updatedResponseData['ingredients']);
        $this->assertEquals(4000, $updatedResponseData['ovenTimeInSeconds']);
        $this->assertTrue($updatedResponseData['special']);
    }

    public function testDeletePizza(): void {
        $client = self::createClient(); // Crea un cliente para hacer la solicitud
        // Realiza la solicitud DELETE para eliminar la pizza con ID 1
        $client->request('DELETE', '/api/pizzas/1');

        // Verifica que la eliminación haya sido exitosa
        $this->assertResponseStatusCodeSame(204); // 204 significa "No Content" en la respuesta de éxito
    }
}
