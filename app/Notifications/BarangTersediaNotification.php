<?php

namespace App\Notifications;

use App\Models\BkuItem;
use App\Models\Kebutuhan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BarangTersediaNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Kebutuhan $kebutuhan,
        private BkuItem $bkuItem,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Barang Tersedia',
            'message' => "Barang \"{$this->kebutuhan->nama_barang}\" yang Anda ajukan sudah tersedia. Silakan konfirmasi penerimaan.",
            'kebutuhan_id' => $this->kebutuhan->id,
            'bku_item_id' => $this->bkuItem->id,
            'kode_rekening' => $this->bkuItem->kode_rekening,
            'pengeluaran' => $this->bkuItem->pengeluaran,
        ];
    }
}
