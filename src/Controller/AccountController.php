<?php

namespace App\Controller;

use App\Repository\CredentialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
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
}
