<?php
/**
 * Created by PhpStorm.
 * User: Oriane
 * Date: 19/03/2019
 * Time: 18:53
 */

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ContactSimple
{

    /**
     * @var string | null
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=100)
     */
    private $sujet;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Length(min=10)
     */
    private $message;

    /**
     * @return string|null
     */
    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    /**
     * @param string|null $sujet
     */
    public function setSujet(?string $sujet): void
    {
        $this->sujet = $sujet;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

}