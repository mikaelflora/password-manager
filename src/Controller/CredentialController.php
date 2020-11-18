<?php

namespace App\Controller;

use App\Entity\Credential;
use App\Form\CredentialType;
use App\Repository\CredentialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/credential")
 */
class CredentialController extends AbstractController
{
    /**
     * @Route("/", name="credential_index", methods={"GET"})
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

        return $this->render('credential/index.html.twig', [
            'credentials' => $credentials,
            'credentialFocus' => $credentialFocus,
        ]);
    }

    /**
     * @Route("/new", name="credential_new", methods={"GET","POST"})
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $credential = new Credential();
        $form = $this->createForm(CredentialType::class, $credential);
        $form->handleRequest($request);
        $imageName = 'no-image.jpg';

        if ($form->isSubmitted() && $form->isValid()) {
            $credential->setUser($this->getUser());
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $credential->setImageFilename($newFilename);
            } else {
                $credential->setImageFilename('no-image.jpg');
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($credential);
            $entityManager->flush();

            return $this->redirectToRoute('credential_index');
        }

        return $this->render('credential/new.html.twig', [
            'credential' => $credential,
            'form' => $form->createView(),
            'image' => $imageName,
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
     * @param Request $request
     * @param Credential $credential
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function edit(Request $request, Credential $credential, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CredentialType::class, $credential);
        $form->handleRequest($request);
        $imageName = $credential->getImageFilename();

        if (!$imageName) {
            $imageName = 'no-image.jpg';
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $credential->setImageFilename($newFilename);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('credential_index');
        }

        return $this->render('credential/edit.html.twig', [
            'credential' => $credential,
            'form' => $form->createView(),
            'image' => $imageName,
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
