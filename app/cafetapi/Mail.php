<?php
namespace cafetapi;

use Exception;
use InvalidArgumentException;
use cafetapi\config\Config;

/**
 * Load a mail in order to send it
 *
 * @author Damien
 *        
 * @since API 0.1.0 (2018)
 */
class Mail
{
    const HEADER = 'MIME-Version: 1.0' . "\n" . 'Content-type: text/html; charset=UTF-8' . "\n";

    private $sender = "";
    private $replyto = "";
    private $recipient;
    private $name;
    private $subject = "";
    private $message;
    private $additionalHeaders;
    
    private $vars = [];

    /**
     * Constructs the object
     *
     * @param string $template
     *            the template to load
     * @param string $recipient
     *            the recicient of the mail
     * @param string $subject
     *            (optional) the subject of the mail
     * @throws InvalidArgumentException if the template doesn't exist or if recipient is null
     * @since API 0.1.0 (2018)
     */
    public final function __construct(string $template, string $recipient, string $subject = null)
    {
        if (! isset($recipient))
            throw new InvalidArgumentException("The recipient haven't been set");
        if (! isset($template) || ! file_exists(MAILS_DIR . $template . '.html')) {
            throw new InvalidArgumentException("'" . $template . '.html' . "' isn't a valid template");
        }

        $lines = file(CONTENT_DIR . 'mails' . DIRECTORY_SEPARATOR . $template . '.html');

        // Load application confs
        $this->sender = (string) Config::email_sender;
        $this->replyto = (string) Config::email_contact;
        $this->name = (string) Config::email_name;
//         $this->subject = (string) Config::email_default_subject;

        // Load conf and message from html comments
        $commentLines = array();

        for ($i = 0; $i < count($lines); $i ++) {
            if (strpos($lines[$i], '<!--CONFIG>') !== false) {
                do {
                    $commentLines[] = $lines[$i];
                    $lines[$i] = '';
                    $i ++;
                } while (strpos($lines[$i], '<CONFIG-->') === false && $i < count($lines));

                if (strpos($lines[$i], '<CONFIG-->') !== false) {
                    $commentLines[] = $lines[$i];
                    $lines[$i] = '';
                }

                break;
            }
        }

        $this->message = implode('', $lines);
        $templateConf = json_decode(str_replace('<!--CONFIG>', '', str_replace('<CONFIG-->', '', implode('', $commentLines))), true);

        if (count($commentLines) > 0 && $templateConf) {
            if (isset($templateConf['sender']))
                $this->sender = (string) $templateConf['sender'];
            if (isset($templateConf['replyto']))
                $this->replyto = (string) $templateConf['replyto'];
            if (isset($templateConf['name']))
                $this->name = (string) $templateConf['name'];
            if (isset($templateConf['subject']))
                $this->subject = (string) $templateConf['subject'];
            if (isset($templateConf['headers']) && is_array($templateConf['headers']))
                $this->additionalHeaders = $templateConf['headers'];
        } elseif (count($commentLines) > 0) {
            throw new Exception('Unable to decode JSON from the configuration comment');
        }

        // finalise initialisation with object arguments
        if (isset($subject)) $this->subject = (string) $subject;
        $this->recipient = $recipient;
    }

    /**
     * Get the sender email
     *
     * @return string the email
     * @since API 0.1.0 (2018)
     */
    public final function getSender(): string
    {
        return $this->sender;
    }

    /**
     * Get the Reply-to email
     *
     * @return string the email
     * @since API 0.1.0 (2018)
     */
    public final function getReplyto(): string
    {
        return $this->replyto;
    }

    /**
     * Get the recipient email
     *
     * @return string the email
     * @since API 0.1.0 (2018)
     */
    public final function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * Get sender name set
     *
     * @return string the name
     * @since API 0.1.0 (2018)
     */
    public final function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the subject of the mail
     *
     * @return string the subject
     * @since API 0.1.0 (2018)
     */
    public final function getSubject(): string
    {
        return $this->subject;
    }

    /**
     *
     * @return mixed
     * @since API 0.1.0 (2018)
     */
    public final function getHeader(): string
    {
        $header = self::HEADER;

        if (isset($this->name)) {
            if (isset($this->sender))
                $header .= 'From: ' . $this->name . ' <' . $this->sender . '>' . "\n";
            if (isset($this->replyto))
                $header .= 'Reply-To: ' . $this->name . ' <' . $this->replyto . '>' . "\n";
        } else {
            if (isset($this->sender))
                $header .= 'From: ' . $this->sender . "\n";
            if (isset($this->replyto))
                $header .= 'Reply-To: ' . $this->replyto . "\n";
        }

        if (isset($this->additionalHeaders) && is_array($this->additionalHeaders))
            foreach ($this->additionalHeaders as $additionalHeader)
                $header .= $additionalHeader . "\n";

        return $header;
    }

    /**
     * Sets the sender email
     *
     * @param string $sender
     *            the email to set
     * @since API 0.1.0 (2018)
     */
    public final function setSender(string $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Sets the Reply-to email
     *
     * @param string $replyto
     *            the email to set
     * @since API 0.1.0 (2018)
     */
    public final function setReplyto(string $replyto)
    {
        $this->replyto = $replyto;
    }

    /**
     * Set the recipient email
     *
     * @param string $recipient
     *            the email to set
     * @since API 0.1.0 (2018)
     */
    public final function setRecipient(string $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Sets the sender name
     *
     * @param string $name
     *            the name to set
     * @since API 0.1.0 (2018)
     */
    public final function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Sets the mail subject
     *
     * @param string $subject
     * @since API 0.1.0 (2018)
     */
    public final function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Sets an email var
     *
     * @param string $varName
     *            the {name}
     * @param mixed $value
     *            the value to set
     * @since API 0.1.0 (2018)
     */
    public final function setVar(string $varName, $value)
    {
        $this->vars['{' . $varName . '}'] = $value;
    }

    /**
     * Adds an header entry
     *
     * @param string $header
     *            the additional header
     * @since API 0.1.0 (2018)
     */
    public final function addHeader(string $header)
    {
        $this->additionalHeaders[] = $header;
    }

    public final function send()
    {
        mail($this->recipient, $this->subject, $this->__toString(), $this->getHeader());
    }

    public final function __toString()
    {
        return str_replace(array_keys($this->vars), array_values($this->vars), $this->message);
    }
}

