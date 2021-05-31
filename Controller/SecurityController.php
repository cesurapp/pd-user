<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Controller;

use Pd\UserBundle\Configuration\ConfigInterface;
use Pd\UserBundle\Event\UserEvent;
use Pd\UserBundle\Model\GroupInterface;
use Pd\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private MailerInterface $mailer,
        private EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * Login.
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Check Auth
        if ($this->checkAuth()) {
            return $this->redirectToRoute($this->getParameter('login_redirect'));
        }

        // Render
        return $this->render($this->getParameter('template_path') . '/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'user_registration' => $this->getParameter('user_registration'),
        ]);
    }

    /**
     * Registration.
     */
    public function register(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        // Check Auth
        if ($this->checkAuth()) {
            return $this->redirectToRoute($this->getParameter('login_redirect'));
        }

        // Check Disable Register
        if (!$this->getParameter('user_registration')) {
            $this->addFlash('error', $this->translator->trans('security.registration_disable'));

            return $this->redirectToRoute('security_login');
        }

        // Create User
        $user = $this->getParameter('user_class');
        $user = new $user();
        if (!$user instanceof UserInterface) {
            throw new InvalidArgumentException();
        }

        // Dispatch Register Event
        if ($response = $this->dispatcher->dispatch(new UserEvent($user), UserEvent::REGISTER_BEFORE)->getResponse()) {
            return $response;
        }

        // Create Form
        $form = $this->createForm($this->getParameter('register_type'), $user);

        // Handle Form Submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get Doctrine
            $em = $this->getDoctrine()->getManager();

            // Encode Password
            $password = $hasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($password);

            // User Confirmation
            if ($this->getParameter('email_confirmation')) {
                // Disable User
                $user->setActive(false);

                // Create Confirmation Token
                if (empty($user->getConfirmationToken()) || null === $user->getConfirmationToken()) {
                    $user->createConfirmationToken();
                }

                // Send Confirmation Email
                $emailBody = [
                    'confirmationUrl' => $this->generateUrl('security_register_confirm',
                        ['token' => $user->getConfirmationToken()],
                        UrlGeneratorInterface::ABSOLUTE_URL),
                ];
                $this->sendEmail($user, 'email.account_confirmation', 'register', $emailBody);
            } elseif ($this->getParameter('welcome_email')) {
                // Send Welcome
                $this->sendEmail($user, 'email.registration_complete', 'welcome');
            }

            // User Add Default Group
            if ($group = $this->getParameter('default_group')) {
                $getGroup = $em->getRepository($this->getParameter('group_class'))->find($group);
                if ($getGroup instanceof GroupInterface) {
                    $user->addGroup($getGroup);
                }
            }

            // Save User
            $em->persist($user);
            $em->flush();

            // Dispatch Register Event
            if ($response = $this->dispatcher->dispatch(new UserEvent($user), UserEvent::REGISTER)->getResponse()) {
                return $response;
            }

            // Register Success
            return $this->render($this->getParameter('template_path') . '/registration/registerSuccess.html.twig', [
                'user' => $user,
            ]);
        }

        // Render
        return $this->render($this->getParameter('template_path') . '/registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Registration Confirm Token.
     */
    public function registerConfirm(MailerInterface $mailer, string $token): Response
    {
        // Get Doctrine
        $em = $this->getDoctrine()->getManager();

        // Find User
        $user = $em->getRepository($this->getParameter('user_class'))->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf($this->translator->trans('security.token_notfound'), $token));
        }

        // Enabled User
        $user->setConfirmationToken(null);
        $user->setActive(true);

        // Send Welcome
        if ($this->getParameter('welcome_email')) {
            $this->sendEmail($user, 'email.registration_complete', 'welcome');
        }

        // Update User
        $em->persist($user);
        $em->flush();

        // Dispatch Register Event
        if ($response = $this->dispatcher->dispatch(new UserEvent($user), UserEvent::REGISTER_CONFIRM)->getResponse()) {
            return $response;
        }

        // Register Success
        return $this->render($this->getParameter('template_path') . '/registration/registerSuccess.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Resetting Request.
     */
    public function resetting(Request $request): Response
    {
        // Check Auth
        if ($this->checkAuth()) {
            return $this->redirectToRoute($this->getParameter('login_redirect'));
        }

        // Build Form
        $form = $this->createForm($this->getParameter('resetting_type'));

        // Handle Form Submit
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get Doctrine
            $em = $this->getDoctrine()->getManager();

            // Find User
            $user = $em->getRepository($this->getParameter('user_class'))->findOneBy(['email' => $form->get('username')->getData()]);
            if (null === $user) {
                $form->get('username')->addError(new FormError($this->translator->trans('security.user_not_found')));
            } else {
                // Create TTL
                if ($user->isPasswordRequestNonExpired($this->getParameter('resetting_request_time'))) {
                    $form->get('username')->addError(new FormError($this->translator->trans('security.resetpw_wait_resendig', ['%s' => ($this->getParameter('resetting_request_time') / 3600)])));
                } else {
                    // Create Confirmation Token
                    if (empty($user->getConfirmationToken()) || null === $user->getConfirmationToken()) {
                        $user->createConfirmationToken();
                        $user->setPasswordRequestedAt(new \DateTime());
                    }

                    // Send Resetting Email
                    $emailBody = [
                        'confirmationUrl' => $this->generateUrl('security_resetting_password',
                            ['token' => $user->getConfirmationToken()],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        ),
                    ];
                    $this->sendEmail($user, 'email.account_password_resetting', 'resetting', $emailBody);

                    // Update User
                    $em->persist($user);
                    $em->flush();

                    // Dispatch Register Event
                    if ($response = $this->dispatcher->dispatch(new UserEvent($user), UserEvent::RESETTING)->getResponse()) {
                        return $response;
                    }

                    // Render
                    return $this->render($this->getParameter('template_path') . '/resetting/resettingSuccess.html.twig', [
                        'sendEmail' => true,
                    ]);
                }
            }
        }

        // Render
        return $this->render($this->getParameter('template_path') . '/resetting/resetting.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Reset Password Form.
     */
    public function resettingPassword(Request $request, UserPasswordEncoderInterface $encoder, string $token): Response
    {
        // Get Doctrine
        $em = $this->getDoctrine()->getManager();

        // Find User
        $user = $em->getRepository($this->getParameter('user_class'))->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf($this->translator->trans('security.token_notfound'), $token));
        }

        // Build Form
        $form = $this->createForm($this->getParameter('resetting_password_type'), $user);

        // Handle Form Submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode Password & Set Token
            $password = $encoder->encodePassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($password)
                ->setConfirmationToken(null)
                ->setPasswordRequestedAt(null);

            // Save User
            $em->persist($user);
            $em->flush();

            // Dispatch Register Event
            if ($response = $this->dispatcher->dispatch(new UserEvent($user), UserEvent::RESETTING_COMPLETE)->getResponse()) {
                return $response;
            }

            // Send Resetting Complete
            $this->sendEmail($user, 'email.password_resetting_completed', 'resetting-complete');

            // Render Success
            return $this->render($this->getParameter('template_path') . '/resetting/resettingSuccess.html.twig', [
                'sendEmail' => false,
            ]);
        }

        // Render
        return $this->render($this->getParameter('template_path') . '/resetting/resettingPassword.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Check User Authorized.
     */
    private function checkAuth(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Send Mail.
     */
    private function sendEmail(UserInterface $user, string $subject, string $templateId, array $data = []): void
    {
        // Create Email
        $email = (new Email())
            ->from(new Address($this->getParameter('mail_sender_address'), $this->getParameter('mail_sender_name')))
            ->to($user->getEmail())
            ->subject($this->translator->trans($subject))
            ->html($this->renderView($this->getParameter('template_path') . "/email/{$templateId}.html.twig", array_merge(['user' => $user], $data)));

        // Send
        $this->mailer->send($email);
    }

    /**
     * Override Parameters
     */
    protected function getParameter(string $name)
    {
        return $this->has('app.params') ? $this->get('app.params')->get($name) : parent::getParameter($name);
    }

    /**
     * Add Custom Services
     */
    public static function getSubscribedServices()
    {
        return array_merge([
            'app.params' => '?' . ConfigInterface::class,
        ], parent::getSubscribedServices());
    }
}
