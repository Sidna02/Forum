<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private LoggerInterface $logger;
    public function __construct(EmailVerifier $emailVerifier, LoggerInterface $logger)
    {
        $this->emailVerifier = $emailVerifier;
        $this->logger = $logger;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        try {
            if ($this->getUser()) {
                return $this->redirectToRoute('app_home');
            }
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('forum@carpour.com', 'Carpour Team'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                return $this->redirectToRoute('app_home');
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        } catch (Exception $e) {
            // Logging protocol example
            // Logs messages in a file in app/var/log
            $this->logger->error($e->getMessage());
            $entityManager =
            $this->addflash('registration_error', "An error has occured while you were trying to register. Please try again later.");
            return new Response("An error has occured while you were trying to register. Please try again later.");
        }
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
