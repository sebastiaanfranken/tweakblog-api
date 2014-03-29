<?php
/**
 * A class to use the Tweakblog API
 * @author Thomas Pinna
 * @author Sebastiaan Franken
 * @package TweakblogAPI
 */

namespace TweakblogAPI;

class TweakBlog
{

	/*
	 * The blog URL
	 */
	private $url;

	/*
	 * The blog title
	 */
	private $title;

	/*
	 * The blog description
	 */
	private $description;

	/**
	 * The constructor, sets all properties
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @param string $url The tweakblog URL
	 * @access private
	 * @return void
	 */
	private function __construct($url)
	{
		if(!is_string($url))
		{
			throw new Exception(__METHOD__ . " expects a string but was given a " . gettype($url));
		}

		$this->url = $url;
		$this->title = "--";
		$this->description = "--";
	}

	public function __toString()
	{
		return $this->title;
	}

	/**
	 * The general system setter
	 * @author Sebastiaan Franken
	 * @param string $key The key to set, has to be a class property
	 * @param string $value The value to set the key to
	 * @access private
	 * @return TweakblogAPI\TweakBlog
	 */
	private function set($key, $value)
	{
		if(!property_exists($this, $key))
		{
			throw new Exception("Tried to set a property that doesn't exist in " . __METHOD__);
		}
		elseif(!is_string($value))
		{
			throw new Exception(__METHOD__ . " expects a string but was given a " . gettype($value));
		}
		else
		{
			$this->$key = $value;
		}

		return $this;
	}

	/**
	 * The general system getter
	 * @author Sebastiaan Franken
	 * @param string $key The key to get, has to be a class property
	 * @access private
	 * @return TweakblogAPI\TweakBlog
	 */
	private function get($key)
	{
		if(!property_exists($this, $key))
		{
			throw new Exception("Tried to get a property that doesn't exist in " . __METHOD__);
		}
		
		return $this->$key;
	}

	/**
	 * The title setter
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @param string $title The title to set
	 * @access public
	 * @return TweakblogAPI\TweakBlog
	 */
	public function setTitle($title)
	{
		$this->set("title", $title);
		return $this;
	}

	/**
	 * The title getter
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @access public
	 * @return string
	 */
	public function getTitle()
	{
		return $this->get("title");
	}

	/**
	 * The description setter
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @access public
	 * @param string $description The description value
	 * @return TweakblogAPI\TweakBlog
	 */
	public function setDescription($description)
	{
		$this->set("description", $description);
		return $this;
	}

	/**
	 * The description getter
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @access public
	 * @return string
	 */
	public function getDescription()
	{
		return $this->get("description");
	}

	/**
	 * A setter for the URL
	 * @author Sebastiaan Franken
	 * @access public
	 * @param string $url The URL to set
	 * @return TweakblogAPI\TweakBlog
	 */
	public function setUrl($url)
	{
		$this->set("url", $url);
		return $this;
	}

	/**
	 * A getter for the URL
	 * @author Sebastiaan Franken
	 * @access public
	 * @return string
	 */
	public function getUrl()
	{
		return $this->get("url");
	}

	/**
	 * Get the blog content 
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @access public
	 * @return mixed
	 */
	public function getBlog()
	{
		$loader = new DOMDocument();
		@$loader->loadHTMLFile($this->url);

		$xpath = new DOMXPath($loader);
		$nodes = $xpath->query("//*[@class='article']");
		$node = $nodes->item(0);

		$newdoc = new DOMDocument();
		$cloned = $node->cloneNode(true);
		$newdoc->appendChild($newdoc->importNode($cloned, true));

		return $newdoc->saveHTML();
	}

	/**
	 * This gets all reactions
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @access public
	 * @return array
	 */
	public function getReactions()
	{
		$loader = new DOMDocument();
		@$loader->loadHTMLFile($this->url);

		$xpath = new DOMXPath($loader);
		$nodes = $xpath->query("//*[@class='reactie']");

		$results = array();

		foreach($nodes as $item)
		{
			$newdoc = new DOMDocument();
			$cloned = $item->cloneNode(true);
			$newdoc->appendChild($newdoc->importNode($cloned, true));

			$xpath = new DOMXPath($newdoc);
			$node = $xpath->query("//*[@rel='nofollow']");
			$user = $node->item(0)->textContent;

			$node = $xpath->query("//*[@class='reactieContent']");
			$message = $node->item(0)->textContent;

			$results[] = new TweakBlogReaction($user, $message);
		}

		return $results;
	}

	/**
	 * This gets the content from the tweakblog provided
	 * @author Thomas Pinna
	 * @author Sebastiaan Franken
	 * @access public
	 * @static
	 * @param string $url The tweakblog URL. Only has to be the username (for example "sfranken")
	 * @return array
	 */
	public static function getTweakBlogs($url)
	{
		if(!is_string($url))
		{
			throw new Exception(__METHOD__ . " expects a string but was given a " . gettype($url));
		}

		$url = "http://" . $url . ".tweakblogs.net/feed/";
		$xml = simplexml_load_file($url);
		$xml->addAttribute("encoding", "UTF-8");

		$results = array();

		foreach($xml->channel->children() as $value)
		{
			if(isset($value->guid))
			{
				$url = (string)$value->guid;
				$title = (string)$value->title;
				$description = (string)$value->description;

				$temp = new TweakBlog($url);
				$temp->setTitle($title);
				$temp->setDescription($description);
				$results[] = $temp;
			}
		}

		return $results;
	}
}