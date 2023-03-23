<?php

namespace App\Controller;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Form\ForgotPasswordFormType;
use App\Form\LoginFormType;
use App\Form\RegisterFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class, [
            '_username' => $lastUsername,
        ]);

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager, 
        SendMailService $mail,
        JWTService $jwt
    ): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            // Check if the user already exists (email & username)
            $existingUser = $entityManager->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
            ]);

            if ($existingUser) {
                $this->addFlash('error', 'Email already exists');

                return $this->redirectToRoute('register');
            }

            $user->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            $header = [
                "typ" => "jwt",
                "alg" => "HS256",
            ];

            $payload = [
                "user_id" => $user->getId(),
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter("app.jwtsecret"));

            $mail->send(
                "no-reply@snowtricks.com",
                $user->getEmail(),
                "Snowtricks - Confirm your account",
                "emails/confirmation.html.twig",
                compact('user', "token")
            );

            $this->addFlash('success', 'Registration successful, please check your email to verify your account');

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/register.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    #[Route(path: '/forgot-password', name: 'forgot_password')]
    public function forgotPassword(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
    ): Response
    {
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $username = $data["username"];

            $user = $userRepository->findOneBy([
                "username" => $username,
            ]);

            if( !$user ) {
                $this->addFlash('error', 'User not found');
                return $this->redirectToRoute('forgot_password');
            }

            $passwordReset = new PasswordReset();
            $passwordReset->setUserId($user->getId());
            $passwordReset->setToken(bin2hex(random_bytes(32)));
            $passwordReset->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($passwordReset);
            $entityManager->flush();

            $token = $passwordReset->getToken();

            $mail->send(
                "no-reply@snowtricks.com",
                $user->getEmail(),
                "Snowtricks - Reset your password",
                "emails/password_reset.html.twig",
                compact('user', "token")
            );

            $this->addFlash('success', 'An email has been sent to you to reset your password');

            return $this->redirectToRoute('login');
        }
        
        return $this->render('auth/forgot_password.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    #[Route(path: '/reset-password/{token}', name: 'reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        Request $request,
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response
    {
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $route = $request->headers->get('referer');
            $data = $form->getData();
            $username = $data["username"];
            $password = $data["password"];

            $user = $userRepository->findOneBy([
                "username" => $username,
            ]);

            if( !$user ) {
                $this->addFlash('error', 'User not found');
                return $this->redirect($route);
            }

            $passwordReset = $passwordResetRepository->findOneBy([
                "user_id" => $user->getId(),
                "token" => $request->attributes->get('token'),
            ]);

            if (!$passwordReset) {
                $this->addFlash('error', 'Invalid token');
                return $this->redirect($route);
            }

            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $entityManager->remove($passwordReset);
            $entityManager->flush();

            $this->addFlash('success', 'Password reset successful');

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/reset_password.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    #[Route(path: '/verify/{token}', name: 'emails.verify')]
    public function verifyEmail(string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter("app.jwtsecret"))) {
            $payload = $jwt->getPayload($token);

            $user = $userRepository->find($payload["user_id"]);
            
            if( $user->getEmailVerifiedAt() ) {
                $this->addFlash('error', 'Email already verified');

                return $this->redirectToRoute('login');
            }

            $user->setEmailVerifiedAt(new \DateTimeImmutable());
            $manager->flush($user);

            $this->addFlash('success', 'Email verified');
            return $this->redirectToRoute('login');
        }

        $this->addFlash('error', 'Invalid token or token expired');

        return $this->redirectToRoute('login');
    }
}
