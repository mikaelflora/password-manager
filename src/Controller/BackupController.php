<?php

namespace App\Controller;

use App\Entity\Credential;
use App\Entity\User;
use App\Form\UploadFileType;
use App\Repository\CredentialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackupController extends AbstractController
{
    /**
     * @Route("/backup", name="backup")
     * @param CredentialRepository $credentialRepository
     * @return Response
     */
    public function index(CredentialRepository $credentialRepository): Response
    {
        $credentials = $credentialRepository->findByUser($this->getUser(),['name' => 'ASC']);

        if ($credentials != null) {
            // filename
            $filename = 'password-manager_' . $this->getUser()->getUsername() . date('_Y-m-d_H:i:s');
            // root path
            $publicPath = $this->getParameter('kernel.project_dir') . '/public/tmp/';
            // full qualified path file name
            $fqpfn = $publicPath . $filename . '.csv';

            if ($file = fopen($fqpfn, 'w')) {
                // header
                $header = ['NAME', 'LOGIN', 'PASSWORD', 'URL', 'NOTE'];
                fputcsv($file, $header, ";", '"');
                // data
                foreach ($credentials as $credential) {
                    // write line to filename
                    $line = [
                        $credential->getName(),
                        $credential->getLogin(),
                        $credential->getPassword(),
                        $credential->getUrl(),
                        $credential->getNote(),
                    ];
                    fputcsv($file, $line, ";", '"');
                }
            }
            fclose($file);
            // download file
            return $this->file($fqpfn);
        }

        return $this->redirectToRoute('account');
    }

    /**
     * @Route("/backup/recovery", name="backup_recovery")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param CredentialRepository $credentialRepository
     * @return Response
     */
    public function recovery(Request $request, EntityManagerInterface $entityManager, CredentialRepository $credentialRepository): Response
    {
        $user = new User();

        $form = $this->createForm(UploadFileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // purge
            $all = $credentialRepository->findByUser($this->getUser());

            foreach ($all as $credential) {
                $entityManager->remove($credential);
            }
            // populate DB
            $file = $form->get('file')->getData();

            $header = true;
            if ($fileOpen = fopen($file->getPathname(), 'r')) {
                while (($line = fgetcsv($fileOpen, 0, ';', '"')) !== FALSE) {
                    if ($header) {
                        $header = false;
                    } else {
                        $credential = new Credential();
                        $credential->setUser($this->getUser());
                        $credential->setName($line[0]);
                        $credential->setLogin($line[1]);
                        $credential->setPassword($line[2]);
                        $credential->setUrl($line[3]);
                        $credential->setNote($line[4]);

                        $entityManager->persist($credential);
                    }
                }
                $entityManager->flush();
            }
            fclose($fileOpen);
            return $this->redirectToRoute('credential_index');
        }

        return $this->render('backup/recovery.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
