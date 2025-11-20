<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
   #[Route('/contact', name: 'contact')]
   public function contact(Request $request, MailerInterface $mailer): Response
   {
       $form = $this->createForm(ContactType::class);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $data = $form->getData();

           $email = (new Email())
               ->from($data['email'])
               ->to('tucorreo@dominio.com')
               ->subject($data['subject'])
               ->text($data['message']);

           $mailer->send($email);

           $this->addFlash('success', 'Mensaje enviado correctamente.');
           return $this->redirectToRoute('contact');
       }

       return $this->render('page/contact.html.twig', [
           'contactForm' => $form->createView(),
       ]);
   }
}
