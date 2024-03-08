<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Pizza;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class PizzaController extends AbstractController {

    public function loadPizza(EntityManagerInterface $em): Response {

        $pizzaData = [
            ['name' => 'Pizza Margarita', 'ingredients' => ['Tomato', 'Mozzarella'], 'ovenTimeInSeconds' => 602340, 'especial' => false],
            ['name' => 'Pizza Pepperoni', 'ingredients' => ['Tomato', 'Mozzarella', 'Pepperoni'], 'ovenTimeInSeconds' => 722340, 'especial' => true],
        ];

        foreach ($pizzaData as $data) {
            $pizza = new Pizza();
            $pizza->setName($data['name']);
            $pizza->setIngredients($data['ingredients']);
            $pizza->setOvenTimeInSeconds($data['ovenTimeInSeconds']);
            $pizza->setSpecial($data['especial']);

            $em->persist($pizza);
        }

        $em->flush();

        return new Response('Pizzas cargadas de prueba correctamente.');
    }

    #[Route('/welcome', name: 'index')]
    public function index(): Response {

        return $this->render('index.html.twig');
    }
}
