<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignedProperty extends Mailable
{
    use Queueable, SerializesModels;

    protected $mailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->_mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $url = route('propertyView', ['id' => $this->mailData->id]);
        return $this->view('emails.property.assigned-property')->with('_mailData', $this->_mailData);
    }
}
