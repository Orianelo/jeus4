<?php
/**
 * Created by PhpStorm.
 * User: Oriane
 * Date: 17/02/2019
 * Time: 11:54
 */

namespace App\Controller;


use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route ("/admin/login", name="admin.login")
     * @param AuthenticationUtils $authenticationUtils
     * @param PlayerRepository $repo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAdmin(AuthenticationUtils $authenticationUtils, PlayerRepository $repo)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route ("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @param PlayerRepository $repo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils, PlayerRepository $repo)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/player/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){
        return $this->redirectToRoute('login');
    }
}