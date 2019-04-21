<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminGameController extends AbstractController
{
    /**
     * @Route("/admin/game/index", name="admin.game.index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator,GameRepository $gameRepository, Request $request): Response
    {
        $games = $paginator->paginate(
            $actors = $gameRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/game/index.html.twig', [
            'games' => $games,
            'finish' => $gameRepository->getFinished(),
            'nonfinish' => $gameRepository->getNonFinished(),
            'latestPlayed'  => $gameRepository->getLatestPlayed(),
            'latestFinish'  => $gameRepository->getLatestFinish(),
            'victory'   => ['ninja'=> $gameRepository->getVictory(1), 'maison'=> $gameRepository->getVictory(2), 'abandon'=> $gameRepository->getVictory(3)]
        ]);
    }

    /**
     * @Route("/admin/game/stat", name="admin.game.stat", methods={"GET"})
     */
    public function stat(GameRepository $gameRepository): Response
    {
        return $this->render('admin/game/stat.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/game/new", name="admin.game.new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('admin.game.index');
        }

        return $this->render('admin/game/new.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/game{id}", name="admin.game.show", methods={"GET"})
     */
    public function show(Game $game): Response
    {
        return $this->render('admin/game/show.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/admin/game/edit{id}", name="admin.game.edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Game $game): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin.game.index', [
                'id' => $game->getId(),
            ]);
        }

        return $this->render('admin/game/edit.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/game/delete/{id}", name="admin.game.delete", methods={"DELETE"})
     */
    public function delete(Request $request, Game $game): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.game.index');
    }
}
