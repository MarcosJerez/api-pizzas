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

        $client = self::createClient();

        $response = $client->request('GET', '/api/pizzas');

        $this->assertResponseIsSuccessful();

        $pizzas = json_decode($response->getContent(), true);

        // Busca la pizza inicial por su nombre (asumiendo que se llama "Pizza inicial")
        $pizzaId = null;

        foreach ($pizzas['hydra:member'] as $pizza) {

            if ($pizza['name'] === 'Pizza inicial') {
                $pizzaId = $pizza['id'];
                break;
            }
        }
        // Si no se encuentra la pizza inicial, créala
        if (!$pizzaId) {
            $response = $client->request('POST', '/api/pizzas', [
                'json' => [
                    'name' => 'Pizza inicial',
                    'ingredients' => ['Ingrediente1', 'Ingrediente2'],
                    'ovenTimeInSeconds' => 3000,
                    'special' => false
                ],
                'headers' => [
                    'Content-Type' => 'application/ld+json'
                ]
            ]);

            // Verifica que la creación haya sido exitosa
            $this->assertEquals(201, $response->getStatusCode());

            // Decodifica la respuesta JSON para obtener el ID de la pizza creada
            $responseData = json_decode($response->getContent(), true);
            $pizzaId = $responseData['id'];
        }


        // Verifica que se haya encontrado la pizza inicial
        $this->assertNotNull($pizzaId, 'La pizza inicial no se encontró');

        // Datos actualizados para la pizza
        $updatedData = [
            'name' => 'Pizza actualizada',
            'ingredients' => ['Nuevo Ingrediente1', 'Nuevo Ingrediente2'],
            'ovenTimeInSeconds' => 4000,
        ];

        // Realiza la solicitud para actualizar la pizza
        $response = $client->request('PUT', '/api/pizzas/' . $pizzaId, [
            'json' => $updatedData,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        // Decodifica la respuesta JSON
        $updatedResponseData = json_decode($response->getContent(), true);

        // Verifica que los datos de la pizza se hayan actualizado correctamente
        $this->assertEquals('Pizza actualizada', $updatedResponseData['name']);
        $this->assertEquals(['Nuevo Ingrediente1', 'Nuevo Ingrediente2'], $updatedResponseData['ingredients']);
        $this->assertEquals(4000, $updatedResponseData['ovenTimeInSeconds']);
    }

    public function testDeletePizza(): void {
        $client = self::createClient();

        $client->request('DELETE', '/api/pizzas/1');

        $this->assertResponseStatusCodeSame(204); // 204 significa "No Content" en la respuesta de éxito
    }
}
