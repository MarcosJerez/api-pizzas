<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Pizza;

class AppFixtures extends Fixture {

    public function load(ObjectManager $manager): void {

        $pizzasData = [
            [
                'name' => 'Pizza Margarita',
                'ingredients' => ['Tomate', 'Mozzarella'],
                'ovenTimeInSeconds' => 6000,
                'special' => true,
            ],
            [
                'name' => 'Pepperoni Pizza',
                'ingredients' => ['Tomate', 'Mozzarella', 'Pepperoni'],
                'ovenTimeInSeconds' => 7777,
                'special' => false,
            ],
                // Agrega más datos de prueba según sea necesario
        ];

        foreach ($pizzasData as $pizzaData) {
            $pizza = new Pizza();
            $pizza->setName($pizzaData['name']);
            $pizza->setIngredients($pizzaData['ingredients']);
            $pizza->setOvenTimeInSeconds($pizzaData['ovenTimeInSeconds']);
            $pizza->setSpecial($pizzaData['special']);

            $manager->persist($pizza);
        }

        $manager->flush();
    }
}
