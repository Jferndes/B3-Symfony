<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; //Donne accès au fonction render

#[Route('/lucky')]
class LuckyController extends AbstractController
{
    #[Route('/number', name: "lucky_number")]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return $this->render('lucky/number.html.twig',[
            'number' => $number,
        ]);
    }
}