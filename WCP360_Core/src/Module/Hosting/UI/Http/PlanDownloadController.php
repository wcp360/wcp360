<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\Hosting\Application\PreparedPlanFiles;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

final class PlanDownloadController extends AbstractController
{
    #[Route('/workbench/jobs/{id}/files/{artifact}', name: 'wcp_plan_file',
        requirements: ['artifact' => 'nginx|php-fpm|caddy|systemd'], methods: ['GET'])]
    public function download(string $id, string $artifact, PreparedPlanFiles $files): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'user') throw $this->createAccessDeniedException();
        $file = $files->download($actor->id, $id, $artifact);

        return new Response($file['content'], 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => HeaderUtils::makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file['name']),
            'Content-Length' => (string) strlen($file['content']),
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
