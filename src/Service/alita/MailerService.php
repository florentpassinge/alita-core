<?php

declare(strict_types = 1);

namespace App\Service\alita;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailerService.
 */
class MailerService
{
    private \Swift_Mailer $mailer;

    private \Swift_Message $message;

    private string $emailFrom;

    private string $emailFromName;

    private ?string $emailSubtitution;

    private Environment $template;

    private array $aTo = [];

    private array $aCc = [];

    private array $aBcc = [];

    private array $aFrom = [];

    private array $aReplyTo = [];

    private ?string $alternativeText;

    private ?string $subject;

    private ?string $body;

    private array $aAttachment = [];

    private string $charset = 'utf-8';

    private ?string $templateView;

    private array $aParameters = [];

    private EntrypointLookupInterface $encoreEntrypoint;

    public function __construct(
        \Swift_Mailer $mailer,
        string $emailFrom,
        string $emailFromName,
        ?string $emailSubtitution,
        Environment $template,
        EntrypointLookupInterface $encoreEntrypoint
    ) {
        $this->emailFrom        = $emailFrom;
        $this->emailFromName    = $emailFromName;
        $this->emailSubtitution = $emailSubtitution;
        $this->encoreEntrypoint = $encoreEntrypoint;
        $this->mailer           = $mailer;
        $this->template         = $template;

        $this->message = new \Swift_Message();
    }

    public function cleanRecipients(): self
    {
        $this->aTo  = [];
        $this->aCc  = [];
        $this->aBcc = [];

        return $this;
    }

    public function reset(): self
    {
        $this->message = new \Swift_Message();

        $this->cleanRecipients();

        $this->aFrom       = [];
        $this->aReplyTo    = [];
        $this->aAttachment = [];

        $this->alternativeText = null;
        $this->subject         = null;
        $this->body            = null;

        return $this;
    }

    public function addTo(string $address, ?string $name = null, $expend = true): self
    {
        $this->checkMail($address);

        $current           = ($expend ? $this->getTo() : []);
        $current[$address] = $name;

        return $this->setTo($current);
    }

    public function getTo(): array
    {
        return $this->aTo;
    }

    public function setTo(array $aTo): self
    {
        $this->aTo = $aTo;

        return $this;
    }

    public function addCc(string $address, ?string $name = null, $expend = true): self
    {
        $this->checkMail($address);

        $current           = ($expend ? $this->getCc() : []);
        $current[$address] = $name;

        return $this->setCc($current);
    }

    public function getCc(): array
    {
        return $this->aCc;
    }

    public function setCc(array $aCc): self
    {
        $this->aCc = $aCc;

        return $this;
    }

    public function addBcc(string $address, ?string $name = null, $expend = true): self
    {
        $this->checkMail($address);

        $current           = ($expend ? $this->getBcc() : []);
        $current[$address] = $name;

        return $this->setBcc($current);
    }

    public function getBcc(): array
    {
        return $this->aBcc;
    }

    public function setBcc(array $aBcc): self
    {
        $this->aBcc = $aBcc;

        return $this;
    }

    public function addFrom(string $address, ?string $name = null, $expend = true): self
    {
        $this->checkMail($address);

        $current           = ($expend ? $this->getFrom() : []);
        $current[$address] = $name;

        return $this->setFrom($current);
    }

    public function getFrom(): array
    {
        return $this->aFrom;
    }

    public function setFrom(array $aFrom): self
    {
        $this->aFrom = $aFrom;

        return $this;
    }

    public function addReplyTo(string $address, ?string $name = null, bool $expend = true): self
    {
        $this->checkMail($address);

        $current           = ($expend ? $this->getReplyTo() : []);
        $current[$address] = $name;

        return $this->setReplyTo($current);
    }

    public function getReplyTo(): array
    {
        return $this->aReplyTo;
    }

    public function setReplyTo(array $aReplyTo): self
    {
        $this->aReplyTo = $aReplyTo;

        return $this;
    }

    public function getPart(): string
    {
        return $this->alternativeText;
    }

    public function setPart(string $message): self
    {
        $message               = preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($message))));
        $this->alternativeText = $message;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $text = null, ?string $template = null, array $aParameters = []): self
    {
        if (null !== $text) {
            $this->body = $text;
        } else {
            $this->templateView = $template;
            $this->aParameters  = $aParameters;
        }

        return $this;
    }

    public function addAttachment(string $file, ?string $filename = null): self
    {
        $attachment = new \Swift_Attachment($file);

        if (null !== $filename) {
            $attachment->setFilename($filename);
        }

        $attachment->setContentType(mime_content_type($file));
        $this->aAttachment[] = $attachment;

        return $this;
    }

    public function getAttachements(): array
    {
        return $this->aAttachment;
    }

    public function send(): void
    {
        $this->preSend();

        $this->message
            ->setSubject($this->getSubject())
            ->setCharset($this->charset);

        $this
            ->generateRecipients()
            ->generateSender()
            ->generateBody()
            ->generateAttachments();

        $this->mailer->send($this->message);
    }

    public function generateRecipients(): self
    {
        if (null === $this->emailSubtitution) {
            $this->message
                ->setTo($this->getTo())
                ->setCc($this->getCc())
                ->setBcc($this->getBcc());
        } else {
            $aTo  = $this->getTo();
            $aCc  = $this->getCc();
            $aBcc = $this->getBcc();

            $text = '<hr>
                Alternative mode : <br>
                List of recipients emails : <br>
                <ul>';
            foreach (['to' => $aTo, 'cc' => $aCc, 'bcc' => $aBcc] as $key => $arrayData) {
                if (0 !== count($arrayData)) {
                    $text .= '<li> '.$key.' :
                        <ul>';
                    foreach (array_keys($arrayData) as $email) {
                        $text .= '<li>'.$email.'</li>';
                    }
                    $text .= '</ul>
                    </li>';
                }
            }
            $text .= '</ul>';

            if (null !== $this->body) {
                $this->body .= $text;
            } else {
                $this->aParameters['emailSubtitution'] = ['aTo' => $aTo, 'aCc' => $aCc, 'aBcc' => $aBcc];
            }
            $this->message->addTo($this->emailSubtitution);
        }

        return $this;
    }

    public function generateSender(): self
    {
        $this->message
            ->setFrom($this->getFrom())
            ->setReplyTo($this->getReplyTo())
            ->setSender($this->emailFrom, $this->emailFromName);

        return $this;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generateBody(): self
    {
        if (null !== $this->getBody()) {
            $this->setPart($this->getBody());
            $this->message
                ->setBody($this->getBody(), 'text/html')
                ->addPart($this->getPart(), 'text/plain');
        } else {
            $this->encoreEntrypoint->reset();

            $body = $this->template->render($this->templateView, $this->aParameters);
            $this->setPart($body);

            $this->message
                ->setBody($body, 'text/html')
                ->addPart($this->getPart(), 'text/plain');

            $this->encoreEntrypoint->reset();
        }

        return $this;
    }

    public function generateAttachments(): self
    {
        $attachments = $this->getAttachements();
        foreach ($attachments as $attachment) {
            $this->message->attach($attachment);
        }

        return $this;
    }

    public function countRecipients(): int
    {
        return count($this->getTo()) + count($this->getCc()) + count($this->getBcc());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function preSend(): void
    {
        $error = 'field %s not found';
        if (null === $this->subject) {
            throw new InvalidArgumentException(sprintf($error, 'subject'));
        }

        if (0 === $this->countRecipients()) {
            throw new InvalidArgumentException(sprintf($error, 'to, cc or bcc'));
        }

        if (
            null === $this->body
            && null === $this->templateView
        ) {
            throw new InvalidArgumentException(sprintf($error, 'body'));
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function checkMail(string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('Error : %s is not a valid mail', $address));
        }
    }
}
