# kohana-hooks
This module lets you register, observer and trigger custom hooks/events in your classes. It offers a functionality like WordPress hooks. Basically you can register a hook in your code and attach a handler with that hook. You can trigger the hook/event in your code where you want to.

## Overview
I was working on a big project where we had to perform several operations from different modules after user is created. Every developer would inject their code in my class and eventually user class became a mess and I ended up writing this class/module.

## Getting Started
kohana-hooks works like any other Kohana Module. Just place the hook folder in modules folder and make sure you load this module in Kohana::module function.

## Example Usage
- Register a hook
	```
	Hooks::register('after_user_create', array('Email', 'send'));
	```
- Trigger a hook
	```
	Hooks::trigger('after_user_create', array('name'=>$user->name, 'email'=>$user->email));
	```
- Alternatively, you can load all the hooks from a file saved in hook/hooks/ folder. You don't need to manually register and observe hooks if you use this method
	```
	Hooks::load('my_hook_file');
	```

## Detailed Usage
Consider the class below

	class User
	{
		function __construct()
		{
			Hook::register('pre_user_create', array($this, 'pre_user_create'));
			Hook::register('post_user_create', array($this, 'post_user_create'));
		}

		public function create()
		{
			Hook::trigger('pre_user_create');

			// Code for user create goes here.

			Hook::trigger('post_user_create', array($user->id));
		}

		public function pre_user_create()
		{
			Log::add('Creating new user');
		}

		public function post_user_create($user_id)
		{
			Log::add('New user created, ID is: '.$user_id);
		}
	}

In the contructor of above class, we registered two hooks ```pre_user_create``` and ```post_user_create``` with ```Hook::register```. The first parameter tells the name of hook and the second parameter is the function we want to execute when this hook is triggered.

Now see the ```create``` method, for a programmer, the code is self explanatory. We triggered the hooks before and after creating user.

**One thing important:** The parameters you want to pass when triggering the hook, must be an array.

----------

If you're super sensitive about your code like me and don't want anyone to edit your class. You can use the alternate method. Take a look at the following example

	class User
	{
		function __construct()
		{
			Hook::load('user');
		}

		public function create()
		{
			Hook::trigger('pre_user_create');

			// Code for user create goes here.

			Hook::trigger('post_user_create', array($user->id));
		}
	}

Noticed the ```load``` method in User's constructor? This function will load and register the hooks from file ```hooks/user.php```. It uses the Kohana's cascading filesystem, so you can put your hook files in ```application/hooks/```.

The content of ```hooks/user.php``` would be

	<?php defined('SYSPATH') or die('No direct script access.');
	
	return array
	(
		'pre_user_create' => array('SomeClass', 'SomeMethod'),
		'post_user_create' => function ($user_id) {
			Log::add('New user created, ID is: '.$user_id);
		}

	);