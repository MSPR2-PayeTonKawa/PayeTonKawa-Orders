<?php

namespace App\Events;

use App\Services\MessageBrokerService;

class OrderCreatedEvent
{
    private MessageBrokerService $messageBroker;

    public function __construct()
    {
        $this->messageBroker = new MessageBrokerService();
    }

    public function publish(array $orderData): void
    {
        $this->messageBroker->publishEvent(
            'payetonkawa',
            'order.created',
            [
                'order_id' => $orderData['id'],
                'customer_id' => $orderData['customer_id'],
                'total_amount' => $orderData['total_amount'],
                'items' => $orderData['items'] ?? [],
                'status' => $orderData['status'] ?? 'pending',
                'created_at' => now()->toISOString(),
                'event_type' => 'order_created'
            ]
        );
    }
}
