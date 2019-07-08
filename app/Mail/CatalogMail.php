<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CatalogMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $attachmentEn;
    protected $attachmentFr;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($attachmentEn = null, $attachmentFr = null)
    {
        $this->attachmentEn = $attachmentEn;
        $this->attachmentFr = $attachmentFr;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('emails.catalog');
        if($this->attachmentEn) {
            $email->attachData($this->attachmentEn->output(), 'catalog_en.pdf');
        }
        if($this->attachmentFr) {
            $email->attachData($this->attachmentFr->output(), 'catalog_fr.pdf');
        }
        return $email;
    }
}
