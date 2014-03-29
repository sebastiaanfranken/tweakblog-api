<?php
/**
 * A class to use the Tweakblog API comments
 * @author Thomas Pinna
 * @author Sebastiaan Franken
 * @package TweakblogAPI
 */

namespace TweakblogAPI;

use Exception;

class TweakBlogReaction
{
	/**
	 * The username
	 * @access private
	 */
	private $user;

	/**
	 * The message
	 * @access private
	 */
	private $message;

	/**
	 * This sets the user and message
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @access public
	 * @param string $user The username
	 * @param string $message The message
	 * @return void
	 */
	public function __construct($user, $message)
	{
		if(!is_string($user) || !is_string($message))
		{
			throw new Exception(__METHOD__ . " expects two strings but was given a " . gettype($user) . " and a " . gettype($message));
		}

		$this->user = $user;
		$this->message = $message;
	}

	/**
	 * The systemwide getter, only gets class properties
	 * @author Sebastiaan Franken
	 * @access private
	 * @param string $name The property that has to be gotten
	 * @return string
	 */
	private function get($name)
	{
		if(!property_exists($this, $name))
		{
			throw new Exception("Tried to get a property that doesn't exist in " . __METHOD__);
		}

		return $this->$name;
	}

	/**
	 * A wrapper around the getter above, this only gets the username
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @return string
	 * @access public
	 */
	public function getUsername()
	{
		return $this->get("user");
	}

	/**
	 * A wrapper around the getter above, this only gets the message
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @return string
	 * @access public
	 */
	public function getMessage()
	{
		return $this->get("message");
	}
}