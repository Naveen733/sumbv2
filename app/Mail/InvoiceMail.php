<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $mydata, $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mydata, $data)
    {
        $this->mydata = $mydata;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.invoice')
            ->from('noreply@set-up-my-business.com.au', 'SUMB Invoice Details')
            ->with(['test' => $this->data])
            ->subject($this->data['subject'])
            ->attachData($this->mydata->output(), $this->data['file_name']);
    }
}
