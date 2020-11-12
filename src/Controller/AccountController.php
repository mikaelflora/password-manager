<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\RemoveAccountType;
use App\Repository\CredentialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     * @param CredentialRepository $credentialRepository
     * @param Request $request
     * @return Response
     */
    public function index(CredentialRepository $credentialRepository, Request $request): Response
    {
        $credentials = $credentialRepository->findByUser($this->getUser(),['name' => 'ASC']);
        $credentialFocus = null;

        if ($credentials) {
            $credentialFocus = $credentials[0];
        }

        $id = $request->query->get('credential');

        if ($id) {
            $focus = $credentialRepository->findOneById($id);
            if ($focus) {
                $credentialFocus = $focus;
            }
        }

        return $this->render('account/index.html.twig', [
            'credentials' => $credentials,
            'credentialFocus' => $credentialFocus,
        ]);
    }

    /**
     * @Route("/account/password", name="account_password")
     */
    public function password(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newPassword = $encoder->encodePassword($user, $form->get('new_password')->getData());
            $oldPassword = $form->get('old_password')->getData();

            if ($encoder->isPasswordValid($user, $oldPassword)) {
                $user->setPassword($newPassword);

                $entityManager->flush();

                $this->addFlash(
                    'success',
                    "Password successful updated!"
                );
            } else {
                $this->addFlash(
                    'danger',
                    "Invalid password!"
                );
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/remove", name="account_remove")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function remove(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(RemoveAccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();

            if ($encoder->isPasswordValid($user, $password)) {
                $session = $this->get('session');
                $session = new Session();
                $session->invalidate();

                $entityManager->remove($this->getUser());
                $entityManager->flush();

                return $this->redirectToRoute('app_register');
            } else {
                $this->addFlash(
                    'danger',
                    "Invalid password!"
                );
            }
        }

        return $this->render('account/remove.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
