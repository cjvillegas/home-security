<?php

namespace App\Mail;

use App\Models\QcRemake;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QcRemakeCheckerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $qcRemake;

    public $verifications;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(QcRemake $qcRemake)
    {
        $this->qcRemake = $qcRemake;
        $this->verifications = QcRemake::VERIFICATION_CHECKLISTS;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.qc-remake-checker-mail');
    }
}
