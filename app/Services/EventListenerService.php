<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class EventListenerService
{
    private MessageBrokerService $messageBroker;

    public function __construct()
    {
        $this->messageBroker = new MessageBrokerService();
    }

    public function startListening(): void
    {
        $this->messageBroker->consumeEvents(
            'orders_service_queue',
            'payetonkawa',
            ['customer.registered', 'product.updated'],
            function (array $data, string $routingKey) {
                $this->handleEvent($data, $routingKey);
            }
        );
    }

    private function handleEvent(array $data, string $routingKey): void
    {
        switch ($routingKey) {
            case 'customer.registered':
                $this->handleCustomerRegistered($data);
                break;
            case 'product.updated':
                $this->handleProductUpdated($data);
                break;
            default:
                Log::warning("Unknown event type: {$routingKey}");
        }
    }

    private function handleCustomerRegistered(array $data): void
    {
        Log::info("Orders service: New customer registered", [
            'customer_id' => $data['customer_id'],
            'email' => $data['email']
        ]);
    }

    private function handleProductUpdated(array $data): void
    {
        Log::info("Orders service: Product updated", [
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'price' => $data['price']
        ]);
    }
}
