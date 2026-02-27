<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    // Order ka data yahan receive hoga
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('ScentView - Your Order Confirmation')
                    ->view('emails.order_placed'); // Ye HTML design ka rasta hai
    }
}