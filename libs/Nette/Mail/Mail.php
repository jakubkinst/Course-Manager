<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004, 2011 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 * @package Nette\Mail
 */



/**
 * Mail provides functionality to compose and send both text and MIME-compliant multipart email messages.
 *
 * @author     David Grudl
 *
 * @property   string $from
 * @property   string $subject
 * @property   string $returnPath
 * @property   int $priority
 * @property   string $htmlBody
 */
class Mail extends MailMimePart
{
	/** Priority */
	const HIGH = 1,
		NORMAL = 3,
		LOW = 5;

	/** @var IMailer */
	public static $defaultMailer = 'SendmailMailer';

	/** @var array */
	public static $defaultHeaders = array(
		'MIME-Version' => '1.0',
		'X-Mailer' => 'Nette Framework',
	);

	/** @var IMailer */
	private $mailer;

	/** @var array */
	private $attachments = array();

	/** @var array */
	private $inlines = array();

	/** @var mixed */
	private $html;

	/** @var string */
	private $basePath;



	public function __construct()
	{
		foreach (self::$defaultHeaders as $name => $value) {
			$this->setHeader($name, $value);
		}
		$this->setHeader('Date', date('r'));
	}



	/**
	 * Sets the sender of the message.
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 * @return Mail  provides a fluent interface
	 */
	public function setFrom($email, $name = NULL)
	{
		$this->setHeader('From', $this->formatEmail($email, $name));
		return $this;
	}



	/**
	 * Returns the sender of the message.
	 * @return array
	 */
	public function getFrom()
	{
		return $this->getHeader('From');
	}



	/**
	 * Adds the reply-to address.
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 * @return Mail  provides a fluent interface
	 */
	public function addReplyTo($email, $name = NULL)
	{
		$this->setHeader('Reply-To', $this->formatEmail($email, $name), TRUE);
		return $this;
	}



	/**
	 * Sets the subject of the message.
	 * @param  string
	 * @return Mail  provides a fluent interface
	 */
	public function setSubject($subject)
	{
		$this->setHeader('Subject', $subject);
		return $this;
	}



	/**
	 * Returns the subject of the message.
	 * @return string
	 */
	public function getSubject()
	{
		return $this->getHeader('Subject');
	}



	/**
	 * Adds email recipient.
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 * @return Mail  provides a fluent interface
	 */
	public function addTo($email, $name = NULL) // addRecipient()
	{
		$this->setHeader('To', $this->formatEmail($email, $name), TRUE);
		return $this;
	}



	/**
	 * Adds carbon copy email recipient.
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 * @return Mail  provides a fluent interface
	 */
	public function addCc($email, $name = NULL)
	{
		$this->setHeader('Cc', $this->formatEmail($email, $name), TRUE);
		return $this;
	}



	/**
	 * Adds blind carbon copy email recipient.
	 * @param  string  email or format "John Doe" <doe@example.com>
	 * @param  string
	 * @return Mail  provides a fluent interface
	 */
	public function addBcc($email, $name = NULL)
	{
		$this->setHeader('Bcc', $this->formatEmail($email, $name), TRUE);
		return $this;
	}



	/**
	 * Formats recipient email.
	 * @param  string
	 * @param  string
	 * @return array
	 */
	private function formatEmail($email, $name)
	{
		if (!$name && preg_match('#^(.+) +<(.*)>$#', $email, $matches)) {
			return array($matches[2] => $matches[1]);
		} else {
			return array($email => $name);
		}
	}



	/**
	 * Sets the Return-Path header of the message.
	 * @param  string  email
	 * @return Mail  provides a fluent interface
	 */
	public function setReturnPath($email)
	{
		$this->setHeader('Return-Path', $email);
		return $this;
	}



	/**
	 * Returns the Return-Path header.
	 * @return string
	 */
	public function getReturnPath()
	{
		return $this->getHeader('From');
	}



	/**
	 * Sets email priority.
	 * @param  int
	 * @return Mail  provides a fluent interface
	 */
	public function setPriority($priority)
	{
		$this->setHeader('X-Priority', (int) $priority);
		return $this;
	}



	/**
	 * Returns email priority.
	 * @return int
	 */
	public function getPriority()
	{
		return $this->getHeader('X-Priority');
	}



	/**
	 * Sets HTML body.
	 * @param  string|ITemplate
	 * @param  mixed base-path or FALSE to disable parsing
	 * @return Mail  provides a fluent interface
	 */
	public function setHtmlBody($html, $basePath = NULL)
	{
		$this->html = $html;
		$this->basePath = $basePath;
		return $this;
	}



	/**
	 * Gets HTML body.
	 * @return mixed
	 */
	public function getHtmlBody()
	{
		return $this->html;
	}



	/**
	 * Adds embedded file.
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return MailMimePart
	 */
	public function addEmbeddedFile($file, $content = NULL, $contentType = NULL)
	{
		$hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
		return $this->inlines[$file] = $this->createAttachment($file, $content, $contentType, 'inline')
			->setHeader('Content-ID', '<' . String::random() . "@$hostname>");
	}



	/**
	 * Adds attachment.
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return MailMimePart
	 */
	public function addAttachment($file, $content = NULL, $contentType = NULL)
	{
		return $this->attachments[] = $this->createAttachment($file, $content, $contentType, 'attachment');
	}



	/**
	 * Creates file MIME part.
	 * @return MailMimePart
	 */
	private function createAttachment($file, $content, $contentType, $disposition)
	{
		$part = new MailMimePart;
		if ($content === NULL) {
			$content = file_get_contents($file);
			if ($content === FALSE) {
				throw new FileNotFoundException("Unable to read file '$file'.");
			}
		} else {
			$content = (string) $content;
		}
		$part->setBody($content);
		$part->setContentType($contentType ? $contentType : Tools::detectMimeTypeFromString($content));
		$part->setEncoding(preg_match('#(multipart|message)/#A', $contentType) ? self::ENCODING_8BIT : self::ENCODING_BASE64);
		$part->setHeader('Content-Disposition', $disposition . '; filename="' . String::fixEncoding(basename($file)) . '"');
		return $part;
	}



	/********************* building and sending ****************d*g**/



	/**
	 * Sends email.
	 * @return void
	 */
	public function send()
	{
		$this->getMailer()->send($this->build());
	}



	/**
	 * Sets the mailer.
	 * @param  IMailer
	 * @return Mail  provides a fluent interface
	 */
	public function setMailer(IMailer $mailer)
	{
		$this->mailer = $mailer;
		return $this;
	}



	/**
	 * Returns mailer.
	 * @return IMailer
	 */
	public function getMailer()
	{
		if ($this->mailer === NULL) {
			if (is_string(self::$defaultMailer) && $a = strrpos(self::$defaultMailer, '\\')) self::$defaultMailer = substr(self::$defaultMailer, $a + 1); // fix namespace
			$this->mailer = is_object(self::$defaultMailer) ? self::$defaultMailer : new self::$defaultMailer;
		}
		return $this->mailer;
	}



	/**
	 * Returns encoded message.
	 * @return string
	 */
	public function generateMessage()
	{
		if ($this->getHeader('Message-ID')) {
			return parent::generateMessage();
		} else {
			return $this->build()->generateMessage();
		}
	}



	/**
	 * Builds email. Does not modify itself, but returns a new object.
	 * @return Mail
	 */
	protected function build()
	{
		$mail = clone $this;
		$hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
		$mail->setHeader('Message-ID', '<' . String::random() . "@$hostname>");

		$mail->buildHtml();
		$mail->buildText();

		$cursor = $mail;
		if ($mail->attachments) {
			$tmp = $cursor->setContentType('multipart/mixed');
			$cursor = $cursor->addPart();
			foreach ($mail->attachments as $value) {
				$tmp->addPart($value);
			}
		}

		if ($mail->html != NULL) { // intentionally ==
			$tmp = $cursor->setContentType('multipart/alternative');
			$cursor = $cursor->addPart();
			$alt = $tmp->addPart();
			if ($mail->inlines) {
				$tmp = $alt->setContentType('multipart/related');
				$alt = $alt->addPart();
				foreach ($mail->inlines as $name => $value) {
					$tmp->addPart($value);
				}
			}
			$alt->setContentType('text/html', 'UTF-8')
				->setEncoding(preg_match('#[\x80-\xFF]#', $mail->html) ? self::ENCODING_8BIT : self::ENCODING_7BIT)
				->setBody($mail->html);
		}

		$text = $mail->getBody();
		$mail->setBody(NULL);
		$cursor->setContentType('text/plain', 'UTF-8')
			->setEncoding(preg_match('#[\x80-\xFF]#', $text) ? self::ENCODING_8BIT : self::ENCODING_7BIT)
			->setBody($text);

		return $mail;
	}



	/**
	 * Builds HTML content.
	 * @return void
	 */
	protected function buildHtml()
	{
		if ($this->html instanceof ITemplate) {
			$this->html->mail = $this;
			if ($this->basePath === NULL && $this->html instanceof IFileTemplate) {
				$this->basePath = dirname($this->html->getFile());
			}
			$this->html = $this->html->__toString(TRUE);
		}

		if ($this->basePath !== FALSE) {
			$cids = array();
			$matches = String::matchAll($this->html, '#(src\s*=\s*|background\s*=\s*|url\()(["\'])(?![a-z]+:|[/\\#])(.+?)\\2#i', PREG_OFFSET_CAPTURE);
			foreach (array_reverse($matches) as $m)	{
				$file = rtrim($this->basePath, '/\\') . '/' . $m[3][0];
				$cid = isset($cids[$file]) ? $cids[$file] : $cids[$file] = substr($this->addEmbeddedFile($file)->getHeader("Content-ID"), 1, -1);
				$this->html = substr_replace($this->html, "{$m[1][0]}{$m[2][0]}cid:$cid{$m[2][0]}", $m[0][1], strlen($m[0][0]));
			}
		}

		if (!$this->getSubject() && $matches = String::match($this->html, '#<title>(.+?)</title>#is')) {
			$this->setSubject(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'));
		}
	}



	/**
	 * Builds text content.
	 * @return void
	 */
	protected function buildText()
	{
		$text = $this->getBody();
		if ($text instanceof ITemplate) {
			$text->mail = $this;
			$this->setBody($text->__toString(TRUE));

		} elseif ($text == NULL && $this->html != NULL) { // intentionally ==
			$text = String::replace($this->html, array(
				'#<(style|script|head).*</\\1>#Uis' => '',
				'#<t[dh][ >]#i' => " $0",
				'#[ \t\r\n]+#' => ' ',
				'#<(/?p|/?h\d|li|br|/tr)[ >/]#i' => "\n$0",
			));
			$text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
			$this->setBody(trim($text));
		}
	}

}
