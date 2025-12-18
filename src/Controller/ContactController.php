<?php
namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(
        Request $request,
        MailerInterface $mailer,
        EntityManagerInterface $em
    ): Response {
        // 👉 Crear entidad
        $contact = new Contact();

        // 👉 Formulario ligado a la entidad
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ GUARDAR EN BASE DE DATOS
            $em->persist($contact);
            $em->flush();

            // 📧 Enviar email (opcional)
            $email = (new Email())
                ->from($contact->getEmail())
                ->to('tucorreo@dominio.com')
                ->subject($contact->getSubject())
                ->text($contact->getMessage());

            $mailer->send($email);

            $this->addFlash('success', 'Mensaje enviado correctamente.');

            return $this->redirectToRoute('contact');
        }

        return $this->render('page/contact.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
