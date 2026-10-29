<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Application\OperationResult;
use Wcp360\Shared\CanonicalErrorCode;
use Wcp360\Shared\Uuid;

final readonly class ApiResponseEnvelope
{
    /** @param array<string,mixed> $data */
    private function __construct(
        public bool $ok,
        public Uuid $requestId,
        public Uuid $correlationId,
        public array $data,
        public ?CanonicalErrorCode $errorCode,
    ) {
    }

    public static function fromOperation(ApiRequestEnvelope $request, OperationResult $result): self
    {
        return new self(
            $result->ok,
            $request->requestId,
            $request->correlationId,
            $result->data,
            $result->errorCode,
        );
    }

    public static function failure(ApiRequestEnvelope $request, CanonicalErrorCode $code): self
    {
        return new self(false, $request->requestId, $request->correlationId, [], $code);
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'ok' => $this->ok,
            'request_id' => (string) $this->requestId,
            'correlation_id' => (string) $this->correlationId,
            'data' => $this->data,
            'error_code' => $this->errorCode?->value,
        ];
    }
}
