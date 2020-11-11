<?php

namespace App\Controller;

use App\Entity\Credential;
use App\Form\CredentialType;
use App\Repository\CredentialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credential")
 */
class CredentialController extends AbstractController
{
    /**
     * @Route("/", name="credential_index", methods={"GET"})
     */
    public function index(CredentialRepository $credentialRepository): Response
    {
        return $this->render('credential/index.html.twig', [
            'credentials' => $credentialRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="credential_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $credential = new Credential();
        $form = $this->createForm(CredentialType::class, $credential);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($credential);
            $entityManager->flush();

            return $this->redirectToRoute('credential_index');
        }

        return $this->render('credential/new.html.twig', [
            'credential' => $credential,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="credential_show", methods={"GET"})
     */
    public function show(Credential $credential): Response
    {
        return $this->render('credential/show.html.twig', [
            'credential' => $credential,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="credential_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Credential $credential): Response
    {
        $form = $this->createForm(CredentialType::class, $credential);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('credential_index');
        }

        return $this->render('credential/edit.html.twig', [
            'credential' => $credential,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="credential_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Credential $credential): Response
    {
        if ($this->isCsrfTokenValid('delete'.$credential->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($credential);
            $entityManager->flush();
        }

        return $this->redirectToRoute('credential_index');
    }
}
