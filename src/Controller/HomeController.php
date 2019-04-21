<?php
/**
 * Created by PhpStorm.
 * User: Oriane
 * Date: 28/03/2019
 * Time: 17:02
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function afficherPartie()
    {
        return $this->render('home/index.html.twig');
    }
}