<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Purchase $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase->loadMissing('item','seller','user');
    }

    public function build()
    {
        return $this->subject('【取引完了】購入者から評価が送信されました')
            ->markdown('emails.transaction_completed', [
                'purchase' => $this->purchase,
                'seller'   => $this->purchase->seller,
                'buyer'    => $this->purchase->user,
                'item'     => $this->purchase->item,
            ]);
    }
}