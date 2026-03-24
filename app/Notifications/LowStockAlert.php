<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $lowStockItems;
    protected $outOfStockItems;

    /**
     * Create a new notification instance.
     */
    public function __construct($lowStockItems, $outOfStockItems)
    {
        $this->lowStockItems = $lowStockItems;
        $this->outOfStockItems = $outOfStockItems;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'stock_alert',
            'title' => 'تنبيه المخزون اليومي',
            'low_count' => count($this->lowStockItems),
            'empty_count' => count($this->outOfStockItems),
            'items' => array_merge($this->lowStockItems, $this->outOfStockItems),
            'message' => $this->formatMessage(),
        ];
    }

    protected function formatMessage()
    {
        $emptyNames = array_column($this->outOfStockItems, 'name');
        $lowNames = array_column($this->lowStockItems, 'name');
        
        $msg = "";
        if (!empty($emptyNames)) {
            $msg .= "نفدت: " . implode('، ', $emptyNames) . ". ";
        }
        
        if (!empty($lowNames)) {
            $msg .= "رصيد منخفض: " . implode('، ', $lowNames) . ".";
        }
        
        return $msg ?: "تقرير المخزون اليومي: جميع الأصناف متوفرة.";
    }
}
