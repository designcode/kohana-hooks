<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This module lets you register, observer and trigger custom hooks/events in your classes. It offers a functionality like WordPress hooks. Basically you can register a hook in your code and attach a handler with that hook. You can trigger the hook/event in your code where you want to.
 *
 * @category   Base
 * @author     Abdullah Ibrahim
 * @copyright  (c) 2013 designcode.me
 */
class Hook {

	/**
	 * @var  Variable that holds the events names
	 */
	static private $hooks = array();

	/**
	 * @var  Variable that holds the handlers of registered events
	 */
	static private $handlers = array();

	/**
	 * Loads hooks/events from file and register. Uses the Kohana Cascading Filesystem
	 *
	 * @param 	string		$file		Name of the file to load events from
	 */
	static public function load($file)
	{
		if( ! $file)
			return FALSE;

		// Loading event file from Kohana Cascading Filesystem
		$hook_file = Kohana::find_file('hooks', $file);

		// Getting the file content into array
		$hooks = Kohana::load($hook_file);

		// Checking if $hooks is array
		if(Arr::is_array($hooks))
		{
			foreach($hooks as $hook_name=>$handler)
			{
				// Registering hook
				self::register($hook_name);

				// Adding the handler to the event (if any)
				if( $handler )
					self::observe($hook_name, $handler);
			}
		}
	}

	/**
	 * Register a new hook
	 *
	 * @param 	string		$hook_name		Name of the hook that needs to be registered
	 * @return  bool
	 */
	static public function register($hook_name)
	{
		// Pushing the event into $events array
		return array_push(self::$hooks, $hook_name);
	}

	/**
	 * Attach a handler against a hook
	 *
	 * @param 	string		$hook_name		Name of hook to observe. Make sure the hook  is registered with register method before observing
	 * @param 	callable	$handler		Handler of the hook, must be a valid PHP callback
	 */
	static public function observe($hook_name, $handler)
	{
		if( ! in_array($hook_name, self::$hooks))
		{
			// Throwing exception if the event was not registered
			throw new Kohana_Exception($hook_name.' is not a registered hook.');
		}
		else if( ! is_callable($handler))
		{
			// If the handler provided was not a valid callable, we'll throw exception
			throw new Kohana_Exception('Handler provided is not valid callable.');
		}
		else
		{
			// Everything is ok, fill the $handler in $handlers array
			if( ! isset(self::$handlers[$hook_name]))
				self::$handlers[$hook_name] = array();
			
			array_push(self::$handlers[$hook_name], $handler);
		}
	}

	/**
	 * Triggers a registered hook/event
	 *
	 * @param 	string		$hook_name		Hook name that needs to fired/triggered
	 * @param 	array		$params			Optional extra parameters, will be passed to $handler when hook is triggered
	 * @return  array
	 */
	static public function trigger($hook_name, &$params=array())
	{
		$response = array();

		if( ! in_array($hook_name, self::$hooks))
		{
			// Throwing exception if the hook was not registered
			throw new Kohana_Exception($hook_name.' is not a registered event.');
		}
		else if( isset(self::$handlers[$hook_name]) AND Arr::is_array(self::$handlers[$hook_name]) )
		{
			foreach(self::$handlers[$hook_name] as $handler)
			{
				// Calling the handlers that were attached to hooks
				$response[$hook_name] = call_user_func_array($handler, $params);
			}
		}

		return $response;
	}

} // End of Hook