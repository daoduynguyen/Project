<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReplyContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $replyContent; // Nội dung admin trả lời
    public $customerName; // Tên khách

    public function __construct($replyContent, $customerName)
    {
        $this->replyContent = $replyContent;
        $this->customerName = $customerName;
    }

    public function build()
    {
        return $this->subject('Phản hồi từ Holomia VR')
                    ->view('emails.reply_contact'); // Trỏ đến file giao diện mail
    }
}