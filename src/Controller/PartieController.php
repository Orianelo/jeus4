<?php

namespace App\Controller;

use App\Entity\Carte;
use App\Entity\ChatGame;
use App\Entity\Game;
use App\Entity\Player;
use App\Repository\CarteRepository;
use App\Repository\ChatGameRepository;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartieController extends AbstractController
{

    /**
     * @Route("/partie/rejoindre/{partie}", name="partie.rejoindre-table", methods={"POST"})
     */
    public function rejoindreTable(Request $request, CarteRepository $carteRepository, Game $partie)
    {
        $player = $this->getUser();
        if ($this->isCsrfTokenValid('rejoindreTable' . $player->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $partie->setJ1($player);
            $partie->setDateDebut(new \DateTime());
            $entityManager->persist($partie);
            $entityManager->flush();
        }
        return $this->redirectToRoute("partie.affiche.jeu", [
            'partie' => $partie->getId()
        ]);
    }

    /**
     * @Route("/partie/nouvelle-table", name="partie.nouvelle-table", methods={"POST"})
     */
    public function newTable(Request $request, CarteRepository $carteRepository)
    {
        $player = $this->getUser();
        if ($this->isCsrfTokenValid('nouvelleTable' . $player->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $partie = new Game();
            $partie->setJ2($player);
            $partie->setDateDebut(new \DateTime());
            $partie->setTourJoueur(1);
            $this->melangeCartes($carteRepository, $partie);
            $partie->setDe([]);
            $entityManager->persist($partie);
            $entityManager->flush();
        }
        return $this->redirectToRoute("partie.index");
    }

    /**
     * @Route("/partie/distribuer-carte", name="jouer.distribuer")
     * @param Request $request
     * @param PlayerRepository $playerRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function distribuerCartes(Request $request, PlayerRepository $playerRepository, CarteRepository $carteRepository, GameRepository $gameRepository)
    {
        $player = $this->getUser();
        if ($this->isCsrfTokenValid('joueurCo' . $player->getId(), $request->request->get('_token')) ||
            $this->isCsrfTokenValid('ami' . $player->getId(), $request->request->get('_token'))) {
            $joueur2 = $playerRepository->find($request->request->get('adversaire'));
            if ($player !== null && $joueur2 !== null) {
                $partie = new Game();
                $partie->setJ1($player);
                $partie->setJ2($joueur2);
                $partie->setDateDebut(new \DateTime());
                $partie->setTourJoueur(1);
                $this->melangeCartes($carteRepository, $partie);
                $partie->setDe([]);

                $em = $this->getDoctrine()->getManager();
                $em->persist($partie);
                $em->flush();

                return $this->redirectToRoute('partie.affiche.jeu', [
                    'partie' => $partie->getId()
                ]);
            }
        } else {
            return $this->render('partie/index.html.twig', [
                'player' => $player,
                'joignPartie' => $gameRepository->findBy(['j1' => null]),
                'partiesEnCours1' => $gameRepository->getEnCours1($player->getId()),
                'partiesEnCours2' => $gameRepository->getEnCours2($player->getId()),
                'rejoindreTable' => $gameRepository->getAllTables($player->getId()),
                'joueursCo' => $playerRepository->findBy(['connexion' => 'true']),
                'otherPlayers' => $playerRepository->findAll()
            ]);
        }
    }

    /**
     * @Route("/partie/plateau/{partie}", name="partie.plateau")
     */
    public function plateau(CarteRepository $carteRepository, Game $partie)
    {
        $cartes = $carteRepository->findAll();
        $tCartes = [];
        foreach ($cartes as $carte) {
            $tCartes[$carte->getId()] = $carte;
        }

        if ($partie->getJ1()->getId() === $this->getUser()->getId()) {
            // En bas J1, en haut J2
            $terrainJoueur = $partie->getTerrainJ1();
            $terrainAdversaire = $partie->getTerrainJ2();
        } else {
            $terrainJoueur = $partie->getTerrainJ2();
            $terrainAdversaire = $partie->getTerrainJ1();
        }

        return $this->render('jeu/plateau.html.twig', [
            'tCartes' => $tCartes,
            'partie' => $partie,
            'terrainJoueur' => $terrainJoueur,
            'terrainAdversaire' => $terrainAdversaire
        ]);
    }

    /**
     * @Route("/partie/tchat/{partie}", name="partie.tchat")
     * @param ChatGameRepository $chatGameRepository
     * @param Game $partie
     * @return Response
     */
    public function tchat(ChatGameRepository $chatGameRepository, Game $partie)
    {
        return $this->render('jeu/tchat.html.twig', [
            'partie'    => $partie,
            'messages' => $chatGameRepository->getMessages($partie),
            'player'   => $this->getUser()
        ]);
    }

    /**
     * @Route("/partie/affiche/{partie}", name="partie.affiche.jeu")
     */
    public function afficherPartie(Game $partie, ChatGameRepository $chatGameRepository)
    {
        // Lance els dés automatiquement si il ne sont pas déjà lancés
        if (!$partie->getDe()) {
            $des = ['de1' => [rand(1, 3), false, false], 'de2' => [rand(1, 3), false, false], 'de3' => [rand(1, 3), false, false]];
            $partie->setDe($des);
            $em = $this->getDoctrine()->getManager();
            $em->persist($partie);
            $em->flush();
        }
        return $this->render('jeu/jouer.html.twig', [
            'partie' => $partie,
            'player' => $this->getUser(),
            'messages' => $chatGameRepository->getMessages($partie)
        ]);
    }


    /**
     * @Route ("/ajax/chat/envoie/{partie}", methods={"POST"}, name="jouer.chat.terrain")
     * @param Request $request
     * @param Game $partie
     */
    public function sauvegardeMessage(Request $request, Game $partie)
    {
        $chat = new ChatGame();
        $chat->setJoueur($partie->getJ1()->getId() === $this->getUser()->getId() ? 1 : 2);
        $chat->setPartie($partie);
        $chat->setMessage($request->get('message'));
        dump($chat);
        $em = $this->getDoctrine()->getManager();
        $em->persist($chat);
        $em->flush();
        return $this->json(['etat' => 'aPile']);
    }

    /**
     * @Route ("/ajax/sauvegar/lance/de", methods={"POST"}, name="sauvegarder_lance_de")
     * @param Request $request
     * @param Game $partie
     */
    public function sauvegardeLancerDeDes(Request $request, Game $partie)
    {
        $de1 = $request->request->get('de1');
        $de2 = $request->request->get('de2');
        $de3 = $request->request->get('de3');
        $des = ['de1' => $de1, 'de2' => $de2, 'de3' => $de3];
        $partie->setDe($des);
        $em = $this->getDoctrine()->getManager();
    }

    /**
     * @param CarteRepository $carteRepository
     * @param $partie
     */
    private function melangeCartes(CarteRepository $carteRepository, Game $partie)
    {
        $cartes = $carteRepository->findAll();
        $carteJ1 = [];
        $carteJ2 = [];
        $shogunJ1 = null;
        $shogunJ2 = null;

        foreach ($cartes as $carte) {
            if ($carte->getCamp() === 'J1') {
                if ($carte->getStrong() === 4) {
                    $shogunJ1 = $carte->getId();
                } else {
                    $carteJ1[] = $carte->getId();
                }
            }
            if ($carte->getCamp() === 'J2') {
                if ($carte->getStrong() === 4) {
                    $shogunJ2 = $carte->getId();
                } else {
                    $carteJ2[] = $carte->getId();
                }
            }
        }

        shuffle($carteJ1);
        shuffle($carteJ2);

        $terrainJ1 = [
            1 => [1 => $shogunJ1, 2 => $carteJ1[0], 3 => $carteJ1[1], 4 => $carteJ1[2]],
            2 => [1 => $carteJ1[3], 2 => $carteJ1[4], 3 => $carteJ1[5]],
            3 => [1 => $carteJ1[6], 2 => $carteJ1[7]],
            4 => [1 => $carteJ1[8]],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => []
        ];

        $terrainJ2 = [
            1 => [1 => $shogunJ2, 2 => $carteJ2[0], 3 => $carteJ2[1], 4 => $carteJ2[2]],
            2 => [1 => $carteJ2[3], 2 => $carteJ2[4], 3 => $carteJ2[5]],
            3 => [1 => $carteJ2[6], 2 => $carteJ2[7]],
            4 => [1 => $carteJ2[8]],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => []
        ];
        $partie->setTerrainJ1($terrainJ1);
        $partie->setTerrainJ2($terrainJ2);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param CarteRepository $carteRepository
     * @param Request $request
     * @param Game $partie
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/ajax/sauvegarde/deplacement/{partie}", name="jouer.deplacement.cartes", methods={"POST"})
     */
    public function move(EntityManagerInterface $entityManager, CarteRepository $carteRepository, Request $request, Game $partie)
    {
        $carte = $carteRepository->find($request->request->get('id'));
        if ($carte !== null) {
            $pile = $request->request->get('pile');
            $position = $request->request->get('position');
            $deplacement = $request->request->get('de');
            $power = $request->request->get('power');
            $couleurDe = $request->request->get('couleurDe');
            $joueur = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getTerrainJ1() : $partie->getTerrainJ2();
            $adv = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getTerrainJ2() : $partie->getTerrainJ1();
            $pileArrivee = $pile + $deplacement;
            $nbCartes = count($joueur[$pileArrivee]);
            if ($couleurDe == 'de3') {
                $powerDe = 'att';
            } elseif ($couleurDe == 'de2') {
                $powerDe = 'def';
            } elseif ($couleurDe == 'de1') {
                $powerDe = 'vit';
            }
            //Vérifier si la couleur correspond
            if ($power == 'tout' || $powerDe == $power) {
                //Vérifie si la carte jouée est un shogun si il peut être déplace
                if ($power == 'tout') {
                    foreach ($partie->getDe() as $de) {
                        if ($de[2]) {
                            return $this->json(['etat' => 'DeplaceShogun']);
                        }
                    }
                }
                //Vérifie si le dé est jouable
                if ($partie->getDe()[$couleurDe][1] == false) {
                    //Vérifie nombre de cartes sur la carte jouée
                    if ($nbCartes - $position < 3) {
                        $pileDeplacer = $joueur[$pile];
                        $joueur[$pile] = [];
                        //Test si il y a des cartes dans la pile de destination
                        if ($nbCartes === 0) {
                            $joueur[$pileArrivee] = [];
                            $posArrivee = 1;
                        } else {
                            $posArrivee = count($joueur[$pileArrivee]) + 1;
                        }
                        $posDepart = 1;
                        //Pour chaque carte de la pile à deplacer on les injecte dans la pile de dest
                        foreach ($pileDeplacer as $posTableau => $id) {
                            if ($posTableau >= $position) {
                                $joueur[$pileArrivee][$posArrivee] = $id;
                                $posArrivee++;
                            } else {
                                $joueur[$pile][$posDepart] = $id;
                                $posDepart++;
                            }
                        }
                        $tousDes = $partie->getDe();
                        $tousDes[$couleurDe][1] = true;
                        if ($power == 'tout') {
                            $tousDes[$couleurDe][2] = true;
                        }
                        $partie->setDe($tousDes);
                        if ($partie->getJ1()->getId() === $this->getUser()->getId()) {
                            $partie->setTerrainJ1($joueur);
                        } else {
                            $partie->setTerrainJ2($joueur);
                        }
                        $etatPartie=$partie->getEtat();
                        $partie->setEtat($etatPartie+1);
                        $entityManager->persist($partie);
                        $entityManager->flush();
                        $pileAdv = 11 - $pileArrivee + 1;
                        if (count($adv[$pileAdv]) > 0) {
                            return $this->json(['etat' => 'combat', 'pile' => $pileArrivee]);
                        } else {
                            if ($partie->getDe()['de1'][1] && $partie->getDe()['de2'][1] && $partie->getDe()['de3'][1]) {
                                $this->actionFinTour($partie);
                                return $this->json(['etat' => 'finTour']);
                            } else {
                                return $this->json(['etat' => 'ok']);
                            }
                        }
                    } else {
                        return $this->json(['etat' => 'tropCartes']);
                    }
                } else {
                    return $this->json(['etat' => 'deJoue']);
                }
            } else {
                return $this->json(['etat' => 'couleur']);
            }
        }
    }

    /**
     * @param Request $request
     * @Route("/ajax/sauvegarde/combat/{partie}", name="jouer.combat.cartes", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function fight(EntityManagerInterface $entityManager, CarteRepository $carteRepository, Request $request, Game $partie)
    {
        $joueur = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getTerrainJ1() : $partie->getTerrainJ2();
        $adv = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getTerrainJ2() : $partie->getTerrainJ1();
        $pile = $request->request->get('pile');
        $pileAdv = 11 - $pile + 1;
        $cartePerdues = [];
        while (!(empty($joueur[$pile]) || empty($adv[$pileAdv]))) {
            $lastJoueur = array_pop($joueur[$pile]);
            $lastAdv = array_pop($adv[$pileAdv]);
            $compare = $this->compareCarte($carteRepository->find($lastJoueur), $carteRepository->find($lastAdv));
            if ($compare == 'joueur') {
                $cartePerdues['adv'][] = $lastAdv;
                $joueur[$pile][count($joueur[$pile])] = $lastJoueur;
            } elseif ($compare == 'adv') {
                $cartePerdues['joueur'][] = $lastJoueur;
                $adv[$pileAdv][count($adv[$pileAdv])] = $lastAdv;
            } elseif ($compare == 'egalite') {
                if (isset($joueur[$pile - 1])) {
                    $joueur[$pile - 1][count($joueur[$pile-1])] = $lastJoueur;
                } else {
                    $cartePerdues['joueur'][] = $lastJoueur;
                }
                if (isset($adv[$pileAdv - 1])) {
                    $adv[$pileAdv - 1][count($adv[$pileAdv-1])] = $lastAdv;
                } else {
                    $cartePerdues['adv'][] = $lastAdv;
                }
            }
        }
        //Verifie qui est le joueur
        if ($this->getUser()->getId() == $partie->getJ1()->getId()) {
            $partie->setTerrainJ1($joueur);
            $partie->setTerrainJ2($adv);
        } else {
            $partie->setTerrainJ1($adv);
            $partie->setTerrainJ2($joueur);
        }
        $etatPartie = $partie->getEtat();
        $partie->setEtat($etatPartie++);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($partie);
        $entityManager->flush();
        return $this->json(['cartesCombat' => $cartePerdues]);
    }

    /**
     * @Route("/ajax/finTour/{partie}", name="jouer.fintour.terrain", methods={"POST"})
     */
    public function finTour(Request $request, Game $partie): Response
    {
        $this->actionFinTour($partie);

        return $this->json(['etat' => 'ok']);
    }

    /**
     * @Route ("/ajax/verifFin/{partie}", name="jouer.finpartie.terrain")
     */
    public function finPartie(Game $partie){
        $joueur = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getTerrainJ1() : $partie->getTerrainJ2();
        $adv = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getTerrainJ2() : $partie->getTerrainJ1();
        $joueurCombat = $this->verifCartes($joueur);
        $advCombat = $this->verifCartes($adv);
        $joueurPile = $this->verifDernierePile($joueur, $adv);
        $advPile = $this->verifDernierePile($adv, $joueur);
        if($joueurCombat){
            $vainqueur = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getJ1() : $partie->getJ2();
            $partie->setDateFin(new \DateTime('now'));
            $partie->setGagnant($partie->getJ1()->getId() === $this->getUser()->getId() ? 1 : 2);
            $partie->setTypeVictoire(1);
            $points=$vainqueur->getPoints();
            $vainqueur->setPoints($points+1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($partie);
            $em->persist($vainqueur);
            $em->flush();
            return $this->json(['etat' => 'jCombat']);
        } elseif ($advCombat){
            $vainqueur = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getJ2() : $partie->getJ1();
            $partie->setDateFin(new \DateTime('now'));
            $partie->setGagnant($partie->getJ1()->getId() === $this->getUser()->getId() ? 2 : 1);
            $points=$vainqueur->getPoints();
            $vainqueur->setPoints($points+1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($partie);
            $em->persist($vainqueur);
            $em->flush();
            return $this->json(['etat' => 'aCombat']);
        }elseif ($joueurPile){
            $vainqueur = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getJ1() : $partie->getJ2();
            $partie->setDateFin(new \DateTime('now'));
            $partie->setGagnant($partie->getJ1()->getId() === $this->getUser()->getId() ? 1 : 2);
            $partie->setTypeVictoire(2);
            $points=$vainqueur->getPoints();
            $vainqueur->setPoints($points+1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($partie);
            $em->persist($vainqueur);
            $em->flush();
            return $this->json(['etat' => 'jPile']);
        }elseif ($advPile){
            $vainqueur = $partie->getJ1()->getId() === $this->getUser()->getId() ? $partie->getJ2() : $partie->getJ1();
            $partie->setDateFin(new \DateTime('now'));
            $partie->setGagnant($partie->getJ1()->getId() === $this->getUser()->getId() ? 2 : 1);
            $partie->setTypeVictoire(2);
            $points=$vainqueur->getPoints();
            $vainqueur->setPoints($points+1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($partie);
            $em->persist($vainqueur);
            $em->flush();
            return $this->json(['etat' => 'aPile']);
        } else {
            return $this->json(['etat' => 'continue']);
        }
    }

    /**
     * @param Game $partie
     * @return Response
     * @Route("/ajax/refreshAdverse/{partie}", name="jouer.refresh.adverse")
     */
    public function verifieChangementTerrain(Game $partie)
    {
        return $this->json(['etat' => $partie->getEtat(), 'tour' => $partie->getTourJoueur(), 'victoire'=>$partie->getGagnant()]);
    }

    /**
     * @param CarteRepository $carteRepository
     * @param Game $partie
     * @return Response
     * @Route("/ajax/refresh/{partie}", name="jouer.refresh.terrain")
     */
    public function refreshTerrain(CarteRepository $carteRepository, Game $partie)
    {
        if ($partie->getJ1()->getId() === $this->getUser()->getId()) {
            //en base c'est J1, adversaire = J2;
            $terrainJoueur = $partie->getTerrainJ1();
            $terrainAdversaire = $partie->getTerrainJ2();
        } else {
            $terrainAdversaire = $partie->getTerrainJ1();
            $terrainJoueur = $partie->getTerrainJ2();
        }
        return $this->render('jeu/plateau.html.twig', [
            'partie' => $partie,
            'terrainAdversaire' => $terrainAdversaire,
            'terrainJoueur' => $terrainJoueur,
            'tCartes' => $carteRepository->findByArrayId()
        ]);
    }

    /**
     * @param CarteRepository $carteRepository
     * @param Game $partie
     * @return Response
     * @Route("/ajax/refresh/tchat/{partie}", name="jouer.refresh.tchat")
     */
    public function refreshTchat(ChatGameRepository $chatGameRepository, Game $partie)
    {

        return $this->render('jeu/tchat.html.twig', [
            'partie'    => $partie,
            'messages'=>$chatGameRepository->getMessages($partie),
            'player'   => $this->getUser()
        ]);
    }

    private function actionFinTour(Game $partie)
    {
        if ($this->getUser()->getId() == $partie->getJ2()->getId()) {
            $tour = $partie->getTour();
            $partie->setTour($tour + 1);
            $partie->setTourJoueur(1);
        } else {
            $partie->setTourJoueur(2);
        }
        $partie->setEtat(1);
        $des = ['de1' => [rand(1, 3), false, false], 'de2' => [rand(1, 3), false, false], 'de3' => [rand(1, 3), false, false]];
        $partie->setDe($des);
        $partie->setDateEnCours(new \DateTime('now'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($partie);
        $entityManager->flush();
    }

    //Compare els cartes des joueurs
    private function compareCarte(Carte $carteJoueur, Carte $carteAdv)
    {
        $partie = ['joueur' => $this->derniereCarte($carteJoueur), 'adv' => $this->derniereCarte($carteAdv)];
        $powers = ['vit' => 'def', 'def' => 'att', 'att' => 'vit'];
        if ($partie['joueur']['power'] == 'tout') {
            $result = $this->combatShogun($partie['joueur']['strong'], $partie['adv']['strong']);
            if ($result == 'shogun') {
                return 'joueur';
            } elseif ($result == 'carte') {
                return 'adv';
            } else {
                return 'egalite';
            }
        } elseif ($partie['adv']['power'] == 'tout') {
            $result = $this->combatShogun($partie['adv']['strong'], $partie['joueur']['strong']);
            if ($result == 'shogun') {
                return 'adv';
            } elseif ($result == 'carte') {
                return 'joueur';
            } else {
                return 'egalite';
            }
        } else {
            $reponse = '';
            while (empty($reponse)) {
                foreach ($powers as $fort => $faible) {
                    if ($partie['joueur']['power'] == $fort && $partie['adv']['power'] == $faible) {
                        $reponse = 'joueur';
                    } elseif ($partie['joueur']['power'] == $faible && $partie['adv']['power'] == $fort) {
                        $reponse = 'adv';
                    } elseif ($partie['joueur']['power'] == $partie['adv']['power']) {
                        if($partie['joueur']['strong'] > $partie['adv']['strong']){
                            $reponse = 'joueur';
                        } elseif ($partie['joueur']['strong'] < $partie['adv']['strong']){
                            $reponse = 'adv';
                        } else{
                            $reponse = 'egalite';
                        }
                    }
                }
            }
            return $reponse;
        }
    }

    private function combatShogun($shogun, $strong)
    {
        if ($this->shogun > $strong) {
            $this->shogun -= $strong;
            return 'shogun';
        } elseif ($this->shogun < $strong) {
            return 'carte';
        } else {
            return 'egalite ';
        }
    }

    private function derniereCarte(Carte $carte)
    {
        $joueur = ['strong' => $carte->getStrong(), 'power' => $carte->getPower()];
        return $joueur;
    }

    private function verifCartes($joueur){
        $combat = true;
        foreach ($joueur as $pile){
            if(!empty($pile)){
                $combat = false;
            }
        }
        return $combat;
    }

    private function verifDernierePile($joueur, $adv){
        if(!empty($joueur[11]) && empty($adv[1])){
            return true;
        } else{
            return false;
        }
    }

}
