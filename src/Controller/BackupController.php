<?php

namespace App\Controller;

use App\Repository\CredentialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
