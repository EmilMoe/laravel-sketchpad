<?php namespace davestewart\sketchpad\config;

use davestewart\sketchpad\objects\install\JSON;
use davestewart\sketchpad\config\SketchpadSettings;

/**
 * Class SketchpadConfig
 *
 * @package davestewart\sketchpad\objects
 */
class SketchpadConfig
{

	// -----------------------------------------------------------------------------------------------------------------
	// properties

		/**
		 * The base route to all Sketchpad calls
		 *
		 * @var string $route
		 */
		public $route   = '/sketchpad/';

		/**
		 * An array of root-relative paths to controller folders
		 *
		 * @var string[] $path
		 */
		public $controllers;

		/**
		 * Root-relative path to the user assets folder
		 *
		 * @var string $assets
		 */
		public $assets;

		/**
		 * Root-relative path to the user views folder
		 *
		 * @var string[] $path
		 */
		public $views;

		/**
		 * The settings file as a JSON class
		 *
		 * @var SketchpadSettings $settings
		 */
		public $settings;

		/**
		 * Admin settings as an object
		 *
		 * @var object $admin
		 */
		public $admin;


	// -----------------------------------------------------------------------------------------------------------------
	// instantiation

		public function __construct()
		{
		    $this->settings = new SketchpadSettings();
			$this->loadSettings();
			$this->loadAdmin();
		}

	// -----------------------------------------------------------------------------------------------------------------
	// methods

		protected function loadSettings()
		{
			if ($this->settings->exists())
			{
				$settings       = $this->settings;

				// values
				$this->route    = $settings->get('route');
				$this->assets   = $settings->get('paths.assets');
				$this->views    = $settings->get('paths.views');
				$controllers    = $settings->get('paths.controllers');

				// ensure route is bounded by slashes to prevent concatenation issue later
				$this->route    = '/' . ltrim('/' . trim($this->route, '/') . '/', '/');

				// paths
				foreach($controllers as $obj)
				{
					if($obj['enabled'])
					{
						$this->controllers[$obj['name']] = rtrim($obj['path'], '/') . '/';
					}
				}
			}
		}

		protected function loadAdmin ()
		{
			// settings
			$admin  = new JSON(storage_path('sketchpad/admin.json'));
			$data   = $admin->data;

			// session
			if (\Session::has('sketchpad.admin'))
			{
				$data = array_merge($data, \Session::get('sketchpad.admin'));
			}

			// save
			$this->admin = (object) $data;
		}

}