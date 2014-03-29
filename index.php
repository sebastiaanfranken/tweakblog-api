<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

spl_autoload_register(function($class) {
	$path = str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";

	if(file_exists($path)) {
		require_once($path);
	}
	else {
		trigger_error("File not found " . $path, E_USER_ERROR);
	}
});

try
{
	$tb = TweakblogAPI\TweakBlog::getTweakBlogs("sfranken");

	foreach($tb as $blog)
	{
		print '<li><a href="' . $blog->getUrl() . '">' . $blog->getTitle() . '</a></li>';
	}

	print "<pre>" . print_r($tb, true) . "</pre>";
}
catch(Exception $e)
{
	echo $e->getMessage();
}