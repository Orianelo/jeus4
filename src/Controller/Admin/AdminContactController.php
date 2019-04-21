<?php
/**
 * Created by PhpStorm.
 * User: Oriane
 * Date: 19/03/2019
 * Time: 19:03
 */

namespace App\Controller\Admin;


use App\Entity\ContactSimple;
use App\Entity\Player;
use App\Form\ContactSimpleType;
use App\Repository\PlayerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class AdminContactController extends AbstractController
{
    /**
     * @Route("/admin/contact/all", name="admin.contact.all")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function contactAll(Request $request, \Swift_Mailer $mailer, Environment $renderer, PlayerRepository $playerRepository){
        $contact = new ContactSimple();
        $form= $this->createForm(ContactSimpleType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            foreach ($playerRepository->findAll() as $player){
                $message = (new \Swift_Message($contact->getSujet()))
                    ->setFrom('cybernativelejeu@gmail.com')
                    ->setTo($player->getEmail())
                    ->setReplyTo($player->getEmail())
                    ->setBody(
                        $this->renderView(
                        // templates/emails/registration.html.twig
                            'emails/registration.html.twig',
                            ['name' => $player->getUsername(), 'contact'    => $contact]
                        ),
                        'text/html'
                    )
                ;
                $mailer->send($message);
            }
            $this->addFlash('success', 'Mails envoyés');
            return $this->redirectToRoute('admin.contact.all');
        }
        return $this->render('admin/contact/contactall.html.twig', [
            'form'  => $form->createView()
        ]);
    }
}