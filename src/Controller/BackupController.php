<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadFileType;
use App\Repository\CredentialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackupController extends AbstractController
{
    /**
     * @Route("/backup", name="backup")
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
                $line = "NAME;LOGIN;PASSWORD;URL";
                fwrite($file, $line . "\r\n");
                // data
                foreach ($credentials as $credential) {
                    // write line to filename
                    $line = [
                        $credential->getName(),
                        $credential->getLogin(),
                        $credential->getPassword(),
                        $credential->getUrl(),
                    ];
                    $line = implode(';', $line) . "\r\n";
                    fwrite($file, $line);
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
     * @return Response
     */
    public function recovery(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UploadFileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            dd($file);
        }

//        dd($user);

        return $this->render('backup/recovery.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
