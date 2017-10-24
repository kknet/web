<?php

namespace App\Events;

use App\AuthToken;
use App\Contracts\Mailable;
use App\Participant;
use Mailer\Messages\Simple;

class FreeTokenSignup extends Event implements Mailable
{
    private $participant;
    private $authToken;
    private $language;

    /**
     * Create a new event instance.
     *
     * @param Participant $participant
     * @param AuthToken $authToken
     * @param string $language
     */
    public function __construct(Participant $participant, AuthToken $authToken, $language = 'en')
    {
        $this->participant = $participant;
        $this->authToken = $authToken;
        $this->language = $language;
    }

    /**
     * Returns the message that is to be mailed
     * @return \Mailer\Contract\Messageable
     */
    public function getMessage(): \Mailer\Contract\Messageable
    {
        $message = new Simple();
        $message->setEvent('participant_signup'.$this->getLanguageSuffix($this->language));
        $message->setClientId($this->participant->id);
        $message->setEmail($this->participant->email);
        $message->setFirstName($this->participant->first_name);
        $message->setLastName($this->participant->last_name);
        $message->setExtras([
            'wallet' => $this->participant->wallet,
            'login_url' => route('auth', [
                'lang' => $this->language,
                'participant' => $this->participant->id,
                'token' => $this->authToken->key,
            ]),
            'free_tokens' => true,
        ]);

        return $message;
    }
}
