<?php namespace Jinggo\Minify\Facades;

use Illuminate\Support\Facades\Facade;

class Minify extends Facade {

	protected static function getFacadeAccessor() { return 'minify'; }
	
}