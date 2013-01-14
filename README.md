# kohana-hooks
This module lets you register, observer and trigger custom hooks/events in your classes. It offers a functionality like WordPress hooks. Basically you can register a hook in your code and attach a handler with that hook. You can trigger the hook/event in your code where you want to.

## Overview
I was working on a large size project where we had to perform several operations after user is created. Every developer would inject their code in my class and eventually user class became a mess and I ended up writing this class/module.

## Getting Started
kohana-hooks works like any other Kohana Module. Just place the hook folder in modules folder and make sure you load this module in Kohana::module function.

## Example Usage
- Register a hook
	```php
	Hooks::register('after_user_create');
	```
- Observe a hook
	```php
	Hooks::observe('after_user_create', array('Email', 'send'));
	```
- Trigger a hook
	```php
	Hooks::trigger('after_user_create', array('name'=>$user->name, 'email'=>$user->email));
	```