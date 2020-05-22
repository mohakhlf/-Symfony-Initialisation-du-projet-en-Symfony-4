<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WildController
 * @package App\Controller
 * @Route("wild", name="wild_")
 */
class WildController extends AbstractController
{
    /**
     *  Show all rows from Program's entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if(!$programs)
        {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }

    /**
     * @param string $categoryName
     * @return Response
     * @Route("/category/{categoryName}", name="show_category").
     */
    public function showByCategory(string $categoryName)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        $categoryProgram = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['Category' => $category], ['id' => 'DESC'], 3);
//        dd($categoryProgram);

        return $this->render(
        'wild/category.html.twig', [
        'categoryPrograms' => $categoryProgram
    ]);
    }

    /**
     * @param string|null $slug
     * @return Response
     * @Route("/serie/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="serie_show")
     */
    public function showByProgram(?string $slug)
    {
        if (!$slug)
        {
            throw $this
            ->createNotFoundException('');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render(
            'wild/serie.html.twig', [
                'program' => $program,
                'slug' => $slug,
                'seasons' => $season
            ]
        );
    }


    /**
     * @param SeasonRepository $seasonRepository
     * @param int $id
     * @return Response
     * @Route("/season/s{id}", name="season_show")
     */
    public function showBySeason(SeasonRepository $seasonRepository, int $id)
    {
        if(!$id)
        {
            throw $this
            ->createNotFoundException('Cette saison n\'existe pas');
        }
        $season = $seasonRepository->find($id);
        $program = $season->getProgram();
        $episode = $season->getEpisodes();
//        $season = $this->getDoctrine()
//            ->getRepository(Season::class)
//            ->findBy(['program' => $program,'id' =>$id]);



//        dd($season,$repo);
        return $this->render('wild/season.html.twig',[
            'season' => $season,
            'program' => $program,
            'episodes' => $episode
        ]);
    }

    /**
     * @param Episode $episode
     * @return Response
     * @Route("/episode/{id}", name="episode_show")
     */
    public function showEpisode(Episode $episode)
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program
        ]);
    }
}
