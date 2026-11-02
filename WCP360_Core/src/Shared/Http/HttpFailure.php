<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/** Fixed presentation vocabulary drawn from the existing API Error Registry. */
#[Exclude]
final readonly class HttpFailure
{
    private function __construct(public int $status, public string $code, public string $message,
        public string $title, public string $explanation) {}

    public static function fromException(#[\SensitiveParameter] \Throwable $error): self
    {
        $failure = self::fromStatus($error instanceof HttpExceptionInterface ? $error->getStatusCode() : 500);

        return $error instanceof IdempotencyConflict
            ? new self(409, 'IDEMPOTENCY_CONFLICT', 'This key is already bound to another request.',
                'Demande déjà enregistrée', 'Cette référence a déjà servi à une autre demande. Consultez l’opération initiale avant de soumettre une nouvelle modification.')
            : $failure;
    }

    public static function mfaRequired(): self
    {
        return new self(403, 'MFA_REQUIRED', 'Complete the pending second-factor challenge.',
            'Vérification nécessaire', 'Terminez la vérification de votre identité pour poursuivre.');
    }

    public static function fromStatus(int $status): self
    {
        if ($status < 400 || $status > 599) $status = 500;
        [$code, $message, $title, $explanation] = match ($status) {
            400, 405, 413, 415, 422 => ['VALIDATION_FAILED', 'The request is invalid.',
                $status === 405 ? 'Méthode non autorisée' : 'Requête invalide',
                'Vérifiez les informations et le formulaire utilisés, puis revenez au panneau.'],
            401 => ['AUTHENTICATION_REQUIRED', 'Sign in to continue.', 'Connexion nécessaire',
                'Connectez-vous à votre espace pour poursuivre.'],
            403 => ['AUTHORIZATION_DENIED', 'The request is not permitted.', 'Accès refusé',
                'Cette demande n’est pas autorisée avec votre accès actuel.'],
            404 => ['RESOURCE_NOT_FOUND', 'The requested resource is unavailable.', 'Page introuvable',
                'Cette page n’est pas disponible. Revenez au panneau pour poursuivre.'],
            409 => ['RESOURCE_CONFLICT', 'The request conflicts with current state.', 'Modification en conflit',
                'L’état a changé. Consultez les informations actuelles avant de soumettre une nouvelle modification.'],
            429 => ['RATE_LIMITED', 'Too many requests. Try again later.', 'Trop de tentatives',
                'Patientez avant de renouveler votre demande.'],
            503 => ['SERVICE_UNAVAILABLE', 'The service is temporarily unavailable.', 'Service indisponible',
                'Consultez l’état de votre opération avant de soumettre à nouveau une modification.'],
            504 => ['OPERATION_TIMEOUT', 'The request deadline was exceeded.', 'Délai dépassé',
                'Le résultat doit être vérifié avant de soumettre à nouveau une modification.'],
            default => ['INTERNAL_ERROR', 'The request could not be completed. Review its outcome before resubmitting a change.',
                'Une erreur est survenue', 'La demande n’a pas pu être traitée. Consultez l’état de votre opération avant de soumettre à nouveau une modification.'],
        };

        return new self($status, $code, $message, $title, $explanation);
    }
}
