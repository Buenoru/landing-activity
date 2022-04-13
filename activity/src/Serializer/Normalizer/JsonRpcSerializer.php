<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use InvalidArgumentException;
use JsonException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

use const JSON_THROW_ON_ERROR;

class JsonRpcSerializer implements SerializerInterface
{

    public function __construct(
        private \Symfony\Component\Serializer\SerializerInterface $serializer,
        private DenormalizerInterface $denormalizer
    ) {
    }


    /**
     * @throws ExceptionInterface
     * @throws JsonException
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = json_decode($encodedEnvelope['body'], true, flags: JSON_THROW_ON_ERROR);
        if (!($class = $body['method'] ?? null) || !class_exists($class)) {
            throw new InvalidArgumentException('JsonRPC method tot found');
        }

        $message = $this->denormalizer->denormalize($body['params'], $class);

        return new Envelope($message, unserialize($encodedEnvelope['headers']['stamps']));
    }


    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        $body = [
            'jsonrpc' => '2.0',
//            'id' => (string)Uuid::v4(), // В данном проекте ответ присылать не нужно, в id нет необходимости.
            'method' => get_class($message),
            'params' => get_object_vars($message),
        ];


        $stamps = [];
        foreach ($envelope->all() as $stmps) {
            $stamps = array_merge($stamps, $stmps);
        }


        return [
            'body' => $this->serializer->serialize($body, JsonEncoder::FORMAT),
            'headers' => [
                'stamps' => serialize($stamps)
            ],
        ];
    }
}
