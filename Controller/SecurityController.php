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

use Pd\MailerBundle\PdMailerBundle;
use Pd\UserBundle\Configuration\ConfigInterface;
use Pd\UserBundle\Event\UserEvent;
use Pd\UserBundle\Form\ResettingPasswordType;
use Pd\UserBundle\Model\GroupInterface;
use Pd\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
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
        return $this->render($this->getParameter('template_path').'/Security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Registration.
     *
     * @return RedirectResponse|Response
     */
    public function register(Request $request, EventDispatcherInterface $dispatcher, TranslatorInterface $translator,
                             UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {
        // Check Auth
        if ($this->checkAuth()) {
            return $this->redirectToRoute($this->getParameter('login_redirect'));
        }

        // Check Disable Register
        if (!$this->getParameter('user_registration')) {
            $this->addFlash('error', $translator->trans('security.registration_disable'));

            return $this->redirectToRoute('security_login');
        }

        // Create User
        $user = $this->getParameter('user_class');
        $user = new $user();
        if (!$user instanceof UserInterface) {
            throw new InvalidArgumentException();
        }

        // Dispatch Register Event
        if ($response = $dispatcher->dispatch(new UserEvent($user), UserEvent::REGISTER_BEFORE)->getResponse()) {
            return $response;
        }

        // Create Form
        $form = $this->createForm($this->getParameter('register_type'), $user, [
            'profile_class' => $this->getParameter('profile_class'),
        ]);

        // Handle Form Submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get Doctrine
            $em = $this->getDoctrine()->getManager();

            // Encode Password
            $password = $encoder->encodePassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($password);

            // User Confirmation
            if ($this->getParameter('email_confirmation')) {
                // Disable User
                $user->setEnabled(false);

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
                $this->sendEmail($user, $mailer, 'Account Confirmation', $emailBody, 'register');
            } elseif ($this->getParameter('welcome_email')) {
                // Send Welcome
                $this->sendEmail($user, $mailer, 'Registration Complete', 'Welcome', 'welcome');
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
            if ($response = $dispatcher->dispatch(new UserEvent($user), UserEvent::REGISTER)->getResponse()) {
                return $response;
            }

            // Register Success
            return $this->render($this->getParameter('template_path').'/Registration/registerSuccess.html.twig', [
                'user' => $user,
            ]);
        }

        // Render
        return $this->render($this->getParameter('template_path').'/Registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Registration Confirm Token.
     *
     * @return Response
     */
    public function registerConfirm(MailerInterface $mailer, EventDispatcherInterface $dispatcher, TranslatorInterface $translator, $token): Response
    {
        // Get Doctrine
        $em = $this->getDoctrine()->getManager();

        // Find User
        $user = $em->getRepository($this->getParameter('user_class'))->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf($translator->trans('security.token_notfound'), $token));
        }

        // Enabled User
        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        // Send Welcome
        if ($this->getParameter('welcome_email')) {
            $this->sendEmail($user, $mailer, 'Registration Complete', 'Welcome', 'welcome');
        }

        // Update User
        $em->persist($user);
        $em->flush();

        // Dispatch Register Event
        if ($response = $dispatcher->dispatch(new UserEvent($user), UserEvent::REGISTER_CONFIRM)->getResponse()) {
            return $response;
        }

        // Register Success
        return $this->render($this->getParameter('template_path').'/Registration/registerSuccess.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Resetting Request.
     *
     * @return RedirectResponse|Response
     */
    public function resetting(Request $request, EventDispatcherInterface $dispatcher, MailerInterface $mailer, TranslatorInterface $translator)
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
                $form->get('username')->addError(new FormError($translator->trans('security.user_not_found')));
            } else {
                // Create TTL
                if ($user->isPasswordRequestNonExpired($this->getParameter('resetting_request_time'))) {
                    $form->get('username')->addError(new FormError($translator->trans('security.resetpw_wait_resendig', ['%s' => ($this->getParameter('resetting_request_time') / 3600)])));
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
                            UrlGeneratorInterface::ABSOLUTE_URL),
                    ];
                    $this->sendEmail($user, $mailer, 'Account Password Resetting', $emailBody, 'resetting');

                    // Update User
                    $em->persist($user);
                    $em->flush();

                    // Dispatch Register Event
                    if ($response = $dispatcher->dispatch(new UserEvent($user), UserEvent::RESETTING)->getResponse()) {
                        return $response;
                    }

                    // Render
                    return $this->render($this->getParameter('template_path').'/Resetting/resettingSuccess.html.twig', [
                        'sendEmail' => true,
                    ]);
                }
            }
        }

        // Render
        return $this->render($this->getParameter('template_path').'/Resetting/resetting.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Reset Password Form.
     *
     * @return Response
     */
    public function resettingPassword(Request $request, UserPasswordEncoderInterface $encoder, EventDispatcherInterface $dispatcher, MailerInterface $mailer, TranslatorInterface $translator, $token): Response
    {
        // Get Doctrine
        $em = $this->getDoctrine()->getManager();

        // Find User
        $user = $em->getRepository($this->getParameter('user_class'))->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf($translator->trans('security.token_notfound'), $token));
        }

        // Build Form
        $form = $this->createForm(ResettingPasswordType::class, $user);

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
            if ($response = $dispatcher->dispatch(new UserEvent($user), UserEvent::RESETTING_COMPLETE)->getResponse()) {
                return $response;
            }

            // Send Resetting Complete
            $this->sendEmail($user, $mailer, 'Account Password Resetting', 'Password resetting completed.', 'resetting-complete');

            // Render Success
            return $this->render($this->getParameter('template_path').'/Resetting/resettingSuccess.html.twig', [
                'sendEmail' => false,
            ]);
        }

        // Render
        return $this->render($this->getParameter('template_path').'/Resetting/resettingPassword.html.twig', [
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
     *
     * @param $subject
     * @param $body
     * @param $templateId
     */
    private function sendEmail(UserInterface $user, MailerInterface $mailer, $subject, $body, $templateId): void
    {
        // Convert Array
        if (!\is_array($body)) {
            $body = ['content' => $body];
        }
        $body['email'] = $user->getEmail();
        $body['fullName'] = $user->getProfile()->getFullName();

        // Create Email
        $email = new Email();

        if (class_exists(PdMailerBundle::class) && $this->getParameter('pd_mailer.template_active')) {
            // Create Message
            $email
                ->from($this->getParameter('mail_sender_address'), $this->getParameter('mail_sender_name'))
                ->to($user->getEmail())
                ->subject($subject)
                ->html($body)
                ->getHeaders()->addTextHeader('template', $templateId);
        } else {
            $email
                ->from($this->getParameter('mail_sender_address'), $this->getParameter('mail_sender_name'))
                ->to($user->getEmail())
                ->subject($subject)
                ->html($this->renderView("@PdUser/Email/{$templateId}.html.twig", $body));
        }

        // Send
        $mailer->send($email);
    }

    /**
     * Override Parameters
     *
     * @return mixed
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
            'app.params' => '?'.ConfigInterface::class,
        ], parent::getSubscribedServices());
    }
}
