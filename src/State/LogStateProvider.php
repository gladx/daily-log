<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Log;

class LogStateProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $itemProvider,
        private ProviderInterface $collectionProvider,
        private string $defaultTimeZone
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $collection = $this->collectionProvider->provide($operation, $uriVariables, $context);

            foreach ($collection as $entity) {
                $this->changeTimeZone($entity);
            }

            return $collection;
        }

        $item = $this->itemProvider->provide($operation, $uriVariables, $context);
        $this->changeTimeZone($item);

        return $item;
    }

    private function changeTimeZone(Log &$log)
    {
        $log->setCreatedAt($log->getCreatedAt()->setTimezone(new \DateTimeZone($this->defaultTimeZone)));
    }
}
