<?php

namespace App\Controller;

use App\Entity\FriendInvitation;
use App\Entity\Player;
use App\Form\RegisterType;
use App\Repository\FriendInvitationRepository;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Class PlayerController
 * @package App\Controller
 */
class PlayerController extends AbstractController
{
    /**
     * @var PlayerRepository
     */
    private $playerRepo;

    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepo = $playerRepository;
    }

    /**
     * @Route("/player", name="player.index")
     * @param Request $request
     * @param FriendInvitationRepository $invitationRepository
     * @param GameRepository $gameRepository
     * @param PlayerRepository $playerRepository
     * @return Response
     */
    public function index(Request $request, FriendInvitationRepository $invitationRepository, GameRepository $gameRepository, PlayerRepository $playerRepository)
    {

        $em = $this->getDoctrine()->getManager();
        $player = $this->getUser();
        $player->setConnexion(1);
        $player->setDateCo(new \DateTime('now'));
        $em->persist($player);
        $em->flush();
        if ($this->isCsrfTokenValid('add_friend' .$player->getId(), $request->request->get('_token'))) {
            $newFriend = $request->get('friend');
            $fI = new FriendInvitation();
            $fI->setJoueurInvitant($player->getId());
            $fI->setJoueurInvite($newFriend);
            if ($invitationRepository->findOneBy(['joueurInvitant' => $player, 'joueurInvite'=>$newFriend])){
                $this->addFlash('success', 'L\'invitation est déjà envoyée à ce joueur');
            } elseif ($invitationRepository->findOneBy(['joueurInvite' => $player, 'joueurInvitant'=>$newFriend])){
                $this->addFlash('success', 'Ce joueur vous a déjà demandé en ami');
            } else{
                $em->persist($fI);
                $em->flush();
                $this->addFlash('success', 'L\'invitation a été envoyée avec succès.');
            }
        }
        $gagne = $player->getPoints();
        $perdu = $player->getGame1()->count() + $player->getGame2()->count() - $player->getPoints();
        $invitations = $invitationRepository->findBy(['joueurInvite'=>$player]);
        $joueurInvit= [];
        foreach ($invitations as $invit){
            $joueurInvit[]=['username'=>$playerRepository->find($invit->getJoueurInvitant()), 'id'=>$invit->getId()];
        }
        $allFriends=[];
        foreach ($player->getFriend() as $f){
            $allFriends[]=$playerRepository->find($f);
        }
        return $this->render('/player/index.html.twig',[
            'player' => $player,
            'friends'   => $allFriends,
            'newFriends' => $this->playerRepo->getOthersPlayers($player->getId()),
            'invitations' => $joueurInvit,
            'numberInvitation' => $invitationRepository->getNumberInvitation($player->getId()),
            'joignPartie' => $gameRepository->findBy(['j1' => null]),
            'partiesEnCours1' => $gameRepository->getEnCours1($player->getId()),
            'partiesEnCours2' => $gameRepository->getEnCours2($player->getId()),
            'rejoindreTable' => $gameRepository->getAllTables($player->getId()),
            'joueursCo' => $playerRepository->getConnects($player->getId()),
            'otherPlayers' => $playerRepository->findAll(),
            'classement'    => $playerRepository->getClassement($player->getPoints()),
            'partiesJoues' => $gameRepository->nbPartiesJouees($player),
            'partiesEnCours'   => $gameRepository->nbPartiesJouees($player)-$gagne,
            'gagne' => $gagne,
            'perdu' => $perdu,
            'partieFinie'   => $gameRepository->getPartiesFinies($player)
        ]);
    }

    /**
     * @Route("/register", name="player.register", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request,UserPasswordEncoderInterface $encoder): Response
    {
        $player = new Player();
        $form = $this->createForm(RegisterType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $player->setPassword($encoder->encodePassword($player, $player->getPassword()));
            $entityManager->persist($player);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('player/register.html.twig', [
            'player' => $player,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/friendInvitation/delete/{id}", name="fi.delete", methods={"DELETE"})
     */
    public function deleteInvitation(Request $request, FriendInvitation $fi): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fi->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('player.index');
    }

    /**
     * @Route("/friendInvitation/accepte/{id}", name="fi.accepte", methods={"POST"})
     */
    public function accepteInvitation(Request $request, FriendInvitation $fi, PlayerRepository $playerRepository): Response
    {
        if ($this->isCsrfTokenValid('accepte'.$fi->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $player = $this->getUser();
            $friends=$player->getFriend();
            $friends[]= $fi->getJoueurInvitant();
            $player->setFriend($friends);
            $entityManager->persist($player);
            $entityManager->flush();
            $otherPlayer = $playerRepository->find($fi->getJoueurInvitant());
            $otherFriends = $otherPlayer->getFriend();
            $otherFriends[] = $fi->getJoueurInvite();
            $entityManager->persist($otherPlayer);
            $entityManager->flush();
            $entityManager->remove($fi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('player.index');
    }

    /**
     * @Route("/player/modif/{id}", name="player.modif", methods={"GET","POST"})
     * @param Request $request
     * @param Player $player
     * @return Response
     */
    public function modifier(Request $request, Player $player): Response
    {
        $form = $this->createForm(Player::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('player.index', [
                'id' => $player->getId(),
            ]);
        }

        return $this->render('player/modify.html.twig', [
            'plyer' => $player,
            'form' => $form->createView(),
        ]);
    }
}
