<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\DTO\ContactDTO;
use App\Form\ContactType;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO;

        // TODO supprimer
        $data->name = 'toto';
        $data->email = 'toto@toto.com';
        $data->subject = 'toto à un ordinateur';
        $data->message = 'toto envoie un mail';


        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $email = (new TemplatedEmail())
                ->from($data->service)
                ->to('you@example.com')
                ->subject($data->subject)
                ->htmlTemplate('emails/contact.html.twig')
                ->context(['data' => $data]);
            try {
                $mailer->send($email);
                $this->addFlash('success', 'E-mail envoyé avec succès !');
                return $this->redirectToRoute('contact');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'E-mail non-envoyé !');

            }
        }

        return $this->render('contact/contact.html.twig', [ 
            'form' => $form
        ]);
    }
}
