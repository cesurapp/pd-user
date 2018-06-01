<?php

/**
 * This file is part of the pdAdmin package.
 *
 * @package     pdAdmin
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 pdAdmin
 * @license     LICENSE
 *
 * @link        http://pdadmin.ramazanapaydin.com
 */

namespace Pd\UserBundle\Controller;

use Pd\UserBundle\Form\RegisterType;
use Pd\UserBundle\Form\ResettingPasswordType;
use Pd\UserBundle\Form\ResettingType;
use Pd\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends Controller
{
    /**
     * Login.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        // Check Auth
        if ($this->checkAuth()) {
            return $this->redirectToRoute($this->getParameter('pd_user.login_redirect'));
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // Render
        return $this->render('@PdUser/Security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Registration.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        // Check Auth
        if ($this->checkAuth()) {
            return $this->redirectToRoute($this->getParameter('pd_user.login_redirect'));
        }

        // Check Disable Register
        if (!$this->getParameter('user_registration')) {
            $this->addFlash('error', $this->get('translator')->trans('user_registration_disable'));

            return $this->redirectToRoute('security_login');
        }

        // Build Form
        $user = $this->getParameter('pd_user.user_class');
        $user = new $user();
        if (!$user instanceof UserInterface)
            throw new InvalidArgumentException();

        $form = $this->createForm(RegisterType::class, $user, [
            'data_class' => $this->getParameter('pd_user.user_class'),
            'profile_class' => $this->getParameter('pd_user.profile_class')
        ]);

        // Handle Form Submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get Doctrine
            $em = $this->getDoctrine()->getManager();

            // Encode Password
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($password);

            // User Confirmation
            if ($this->getParameter('pd_user.email_confirmation')) {
                // Disable User
                $user->setEnabled(false);

                // Create Confirmation Token
                if (empty($user->getConfirmationToken()) || null === $user->getConfirmationToken()) {
                    $user->createConfirmationToken();
                }

                // Send Confirmation Email
                $emailBody = [
                    'confirmationUrl' => $this->generateUrl('security_register_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
                $this->sendEmail($user, 'Account Confirmation', $emailBody, 'Register');
            } else {
                // Send Welcome
                $this->sendWelcome($user);
            }

            // User Add Default Group
            if ($group = $this->getParameter('pd_user.default_group')) {
                $getGroup = $em->getRepository($this->getParameter('pd_user.group_class'))->findOneBy($group);
                if (null !== $getGroup) {
                    $user->addGroup($getGroup);
                }
            }

            // Save User
            $em->persist($user);
            $em->flush();

            // Register Success
            return $this->render('@PdUser/Registration/registerSuccess.html.twig', [
                'user' => $user,
            ]);
        }

        // Render
        return $this->render('@PdUser/Registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Registration Confirm Token.
     *
     * @param $token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerConfirmAction($token)
    {
        // Get Doctrine
        $em = $this->getDoctrine()->getManager();

        // Find User
        $user = $em->getRepository($this->getParameter('pd_user.user_class'))->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf($this->get('translator')->trans('security.token_notfound'), $token));
        }

        // Enabled User
        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        // Send Welcome
        $this->sendWelcome($user);

        // Update User
        $em->persist($user);
        $em->flush();

        // Register Success
        return $this->render('@PdUser/Registration/registerSuccess.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Resetting Request.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resettingAction(Request $request)
    {
        // Check Auth
        if ($this->checkAuth()) {
            return $this->redirectToRoute($this->getParameter('pd_user.login_redirect'));
        }

        // Build Form
        $form = $this->createForm(ResettingType::class);

        // Handle Form Submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get Doctrine
            $em = $this->getDoctrine()->getManager();

            // Find User
            $user = $em->getRepository($this->getParameter('pd_user.user_class'))->findOneBy(['email' => $form->get('username')->getData()]);
            if (null === $user) {
                $form->get('username')->addError(new FormError($this->get('translator')->trans('security.user_not_found')));
            } else {
                // Create TTL
                if ($user->isPasswordRequestNonExpired(7200)) {
                    $form->get('username')->addError(new FormError($this->get('translator')->trans('security.resetpw_wait_resendig')));
                } else {
                    // Create Confirmation Token
                    if (empty($user->getConfirmationToken()) || null === $user->getConfirmationToken()) {
                        $user->createConfirmationToken();
                        $user->setPasswordRequestedAt(new \DateTime());
                    }

                    // Send Resetting Email
                    $emailBody = [
                        'confirmationUrl' => $this->generateUrl('security_resetting_password', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL),
                    ];
                    $this->sendEmail($user, 'Account Password Resetting', $emailBody, 'Resetting');

                    // Update User
                    $em->persist($user);
                    $em->flush();

                    // Render
                    return $this->render('@PdUser/Resetting/resettingSuccess.html.twig', [
                        'sendEmail' => true,
                    ]);
                }
            }
        }

        // Render
        return $this->render('@PdUser/Resetting/resetting.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Reset Password Form.
     *
     * @param Request $request
     * @param $token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resettingPasswordAction(Request $request, $token)
    {
        // Get Doctrine
        $em = $this->getDoctrine()->getManager();

        // Find User
        $user = $em->getRepository($this->getParameter('pd_user.user_class'))->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf($this->get('translator')->trans('security.token_notfound'), $token));
        }

        // Build Form
        $form = $this->createForm(ResettingPasswordType::class, $user);

        // Handle Form Submit
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode Password & Set Token
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($password)
                ->setConfirmationToken(null)
                ->setPasswordRequestedAt(null);

            // Save User
            $em->persist($user);
            $em->flush();

            // Send Resetting Complete
            $this->sendEmail($user, 'Account Password Resetting', 'Password resetting completed.', 'Resetting_Completed');

            // Render Success
            return $this->render('@PdUser/Resetting/resettingSuccess.html.twig', [
                'sendEmail' => false,
            ]);
        }

        // Render
        return $this->render('@PdUser/Resetting/resettingPassword.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Check User Authorized.
     *
     * @return bool
     */
    private function checkAuth()
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY') || $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Send Welcome Email.
     *
     * @param UserInterface $user
     */
    private function sendWelcome(UserInterface $user)
    {
        if ($this->getParameter('pd_user.welcome_email'))
            $this->sendEmail($user, 'Registration', 'Welcome', 'Welcome');
    }

    /**
     * Send Mail.
     *
     * @param UserInterface   $user
     * @param string $subject
     * @param string $body
     * @param string $description
     *
     * @return bool
     */
    private function sendEmail(UserInterface $user, $subject = '', $body = '', $description = '')
    {
        if (is_array($body)) {
            $body['email'] = $user->getEmail();
            $body['fullname'] = $user->getProfile()->getFullName();
        } else {
            $body = [
                'email' => $user->getEmail(),
                'fullname' => $user->getProfile()->getFullName(),
                'content' => $body,
            ];
        }

        // Create Message
        $message = (new \Swift_Message())
            ->setDescription($description)
            ->setFrom($this->getParameter('mail_sender_address'), $this->getParameter('mail_sender_name'))
            ->setTo($user->getEmail())
            ->setSubject($subject)
            ->setBody(serialize($body), 'text/html');

        return (bool) $this->get('mailer')->send($message);
    }
}
