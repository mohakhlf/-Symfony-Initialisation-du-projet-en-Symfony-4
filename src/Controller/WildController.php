<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="0_index")
     */
    public function index()
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("wild/show/{slug<[0-9a-z-]+>?}", name="show_index")
     */
    public function show($slug)
    {
        if (!empty($slug)) {
            return $this->render('wild/show.html.twig', [
                'title' => sprintf(
                    ucwords(str_replace('-', ' ', $slug))
                )
            ]);
        } else {
            return $this->render('wild/show.html.twig', [
                'title' => 'Aucune série sélectionnée, veuillez choisir une série'
            ]);
        }
    }
}
