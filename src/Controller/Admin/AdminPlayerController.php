<?php

namespace App\Controller\Admin;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Form\RegisterType;
use App\Repository\PlayerRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPlayerController extends AbstractController
{
    /**
     * @Route("/admin/player/index", name="admin.player.index", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param PlayerRepository $playerRepository
     * @return Response
     */
    public function index(PaginatorInterface $paginator, PlayerRepository $playerRepository, Request $request): Response
    {
        $players = $paginator->paginate(
            $actors = $playerRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/player/index.html.twig', [
            'players' => $players,
            'player' => $playerRepository->getLatestConnect(),
            'connect'   => $playerRepository->getConnect(),
            'disconnect'    => $playerRepository->getDisconnect(),
        ]);
    }

    /**
     * @Route("/admin/player/new", name="admin.player.new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $player = new Player();
        $form = $this->createForm(RegisterType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($player);
            $entityManager->flush();

            return $this->redirectToRoute('admin.player.index');
        }

        return $this->render('admin/player/new.html.twig', [
            'player' => $player,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/player/{id}", name="admin.player.show", methods={"GET"})
     * @param Player $player
     * @return Response
     */
    public function show(Player $player): Response
    {
        $victoire_ninja = 0;
        $victoire_maison = 0;
        $victoire_abandon = 0;
        foreach ($player->getGame1()->getValues() as $game) {
            if ($game->getEtat()) {
                if ($game->getGagnant() == 1){
                    switch ($game->getTypeVictoire()) {
                        case 1 :
                            $victoire_ninja++;
                            break;
                        case 2:
                            $victoire_maison++;
                            break;
                        case 3:
                            $victoire_abandon++;
                    }
                }
            }
        }
        foreach ($player->getGame2()->getValues() as $game) {
            if ($game->getEtat()) {
                if ($game->getGagnant() == 2){
                    switch ($game->getTypeVictoire()) {
                        case 1 :
                            $victoire_ninja++;
                            break;
                        case 2:
                            $victoire_maison++;
                            break;
                        case 3:
                            $victoire_abandon++;
                    }
                }
            }
        }
        $gagne = $player->getPoints();
        $perdu = $player->getGame1()->count() + $player->getGame2()->count() - $player->getPoints();
        return $this->render('admin/player/show.html.twig', [
            'player' => $player,
            'gagne' => $gagne,
            'perdu' => $perdu,
            'victoire' => ['ninja' => $victoire_ninja, 'maison' => $victoire_maison, 'abandon' => $victoire_abandon]
        ]);
    }

    /**
     * @Route ("/admin/player/block/{id}", name="admin.player.block", requirements={"id" : "[0-9]*"}, methods="GET|POST")
     * @param Player $player
     * @param Request $request
     * @param ObjectManager $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function blocked(Player $player, Request $request, ObjectManager $em)
    {
        if ($this->isCsrfTokenValid('block' . $player->getId(), $request->get('_token'))) {
            if ($player->getBlocage() == false) {
                $player->setBlocage(true);
                $em->flush();
            } else {
                $player->setBlocage(false);
                $em->flush();
            }
        }
        return $this->redirectToRoute('admin.player.index', [
            'id' => $player->getId()
        ]);
    }

    /**
     * @Route("/admin/player/edit/{id}", name="admin.player.edit", methods={"GET","POST"})
     * @param Request $request
     * @param Player $player
     * @return Response
     */
    public function edit(Request $request, Player $player): Response
    {
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin.player.index', [
                'id' => $player->getId(),
            ]);
        }

        return $this->render('admin/player/edit.html.twig', [
            'player' => $player,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/player/delete/{id}", name="admin.player.delete", methods={"DELETE"})
     * @param Request $request
     * @param Player $player
     * @return Response
     */
    public function delete(Request $request, Player $player): Response
    {
        if ($this->isCsrfTokenValid('delete' . $player->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($player);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.player.index');
    }
}
