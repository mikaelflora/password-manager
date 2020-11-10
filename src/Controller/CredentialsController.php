<?php

namespace App\Controller;

use App\Entity\Credentials;
use App\Form\CredentialsType;
use App\Repository\CredentialsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials")
 */
class CredentialsController extends AbstractController
{
    /**
     * @Route("/", name="credentials_index", methods={"GET"})
     */
    public function index(CredentialsRepository $credentialsRepository): Response
    {
        return $this->render('credentials/index.html.twig', [
            'credentials' => $credentialsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="credentials_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $credential = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credential);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($credential);
            $entityManager->flush();

            return $this->redirectToRoute('credentials_index');
        }

        return $this->render('credentials/new.html.twig', [
            'credential' => $credential,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="credentials_show", methods={"GET"})
     */
    public function show(Credentials $credential): Response
    {
        return $this->render('credentials/show.html.twig', [
            'credential' => $credential,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="credentials_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Credentials $credential): Response
    {
        $form = $this->createForm(CredentialsType::class, $credential);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('credentials_index');
        }

        return $this->render('credentials/edit.html.twig', [
            'credential' => $credential,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="credentials_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Credentials $credential): Response
    {
        if ($this->isCsrfTokenValid('delete'.$credential->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($credential);
            $entityManager->flush();
        }

        return $this->redirectToRoute('credentials_index');
    }
}
