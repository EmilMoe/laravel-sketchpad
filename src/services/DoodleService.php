<?php namespace davestewart\doodle\services;

use App;
use davestewart\doodle\objects\AbstractService;
use davestewart\doodle\objects\DoodleConfig;
use davestewart\doodle\objects\file\Folder;
use davestewart\doodle\objects\reflection\Controller;
use davestewart\doodle\objects\route\ControllerReference;
use davestewart\doodle\objects\route\FolderReference;
use Illuminate\Support\Facades\Input;
use ReflectionMethod;
use Route;


/**
 * Class DoodleService
 *
 * @package davestewart\doodle\services
 */
class DoodleService extends AbstractService
{

	// -----------------------------------------------------------------------------------------------------------------
	// PROPERTIES

		/**
		 * @var DoodleConfig
		 */
		protected $config;

		/**
		 * The service that determines available routes, and matches routes to controllers when one is called
		 * 
		 * @var RouteService
		 */
		protected $router;
	

	// -----------------------------------------------------------------------------------------------------------------
	// INSTANTIATION
	
		public function init()
		{
			// config
			$config         = new DoodleConfig();
			$this->path     = base_path($config->path);
			$this->route    = $config->route;
			$this->config   = $config;

			// determine remaining controller routes
			$this->router   = new RouteService($this->route, $this->path, $config->namespace);

			// routing
			$parameters     =
			[
				'namespace'     => 'davestewart\doodle\controllers',
				'middleware'    => 'web',
			];

			// add main doodle routes
			Route::group($parameters, function ($router) use ($config)
			{
				Route::post ($config->route, 'DoodleController@create');
				Route::match(['GET', 'POST'], $config->route . '{params?}', 'DoodleController@call')->where('params', '.*');
			});

			// debug
			//dd($this->router->routes);
		}


	// -----------------------------------------------------------------------------------------------------------------
	// ACCESSORS

		/**
		 * Gets the core data for the main doodle view
		 *
		 * @param      $path
		 * @param null $controller
		 * @return array
		 */
		public function getData($path, $controller = null)
		{
			$data['route']      = $this->route;
			$data['theme']      = $this->config->theme;
			$data['assets']     = $this->config->assets;
			$data['routes']     = $this->router->routes;
			$data['folder']     = $this->getFolder($path);
			if($controller)
			{
				$data['controller'] = $controller;
			}
			return $data;
		}

		/**
		 * Gets folder data (subfolders and controllers) for an absolute file path
		 *
		 * @param   string $path
		 * @return  array
		 */
		public function getFolder($path = '', $recursive = false)
		{
			if($path == '')
			{
				$path = $this->path;
			}
			return file_exists($path)
						? new Folder($path, $recursive)
						: null;
		}

		/**
		 * Gets controller data for an absolute file path
		 *
		 * @param $path
		 * @return Controller|null
		 */
		public function getController($path)
		{
			return file_exists($path)
				? new Controller($path)
				: null;
		}

		/**
		 * Determines the route uri for an absolute file path
		 *
		 * @param $path
		 * @return string
		 */
		public function getRouteFromPath($path)
		{
			$path   = str_replace($this->path, '', $path);
			$path   = str_replace('Controller.php',  '', $path);
			$path   = $this->folderize(strtolower($path));
			return $this->route . $path;
		}


	// ------------------------------------------------------------------------------------------------
	// ROUTING METHODS

		public function call($uri = '')
		{
			// get variables
			$nav    = Input::get('nav', 0);
			$json   = Input::get('json', 0);
			$base   = $this->route;
			$ref    = $this->router->getRoute($base . $uri);

			// attempt to call controller
			if($ref instanceof ControllerReference)
			{
				// controller has method
				if($ref->method)
				{
					// test controller / method exists
					try
					{
						new ReflectionMethod($ref->class, $ref->method);
					}
					catch(\Exception $e)
					{
						if($e instanceof \ReflectionException)
						{
							$doodle = str_replace($base, '', $ref->route) . $ref->method . '/';
							$this->abort($doodle, 'method');
						}
					}
	
					// call and return the controller
					return $this->router->call($ref->class, $ref->method, $ref->params);
				}
				
				// just controller				
				$controller = $this->getController($ref->path)->process();
				if($json)
				{
					return $controller;
				}
				else
				{
					$data = $this->getData($ref->folder, $controller);
					$data['uri']        = $this->route . $uri . '/';
					return view('doodle::content.index', $data);
				}

			}

			// if folder, return the contents of that folder as json
			if($ref instanceof FolderReference)
			{
				if($nav || $json)
				{
					$data = $this->getFolder($ref->path);
					return $json
						? $data
						: view('doodle::nav.folder', compact('data'));
				}
				else
				{
					$data   = $this->getData($ref->path);
					$folder = $data['folder'];
					$folder->process();
					$data['controller'] = $folder->controllers[0];
					$data['uri']        = $this->route . $uri . '/';

					return view('doodle::content.index', $data);
				}
			}

			// otherwise, there's nothing to call, so 404
			$this->abort($uri, 'path');
		}


		public function create($path, $members, $options)
		{

		}


	// ------------------------------------------------------------------------------------------------
	// UTILITIES

		protected function abort($uri, $type = '')
		{
			App::abort(404, "The requested Doodle $type '$uri' does not exist");
		}


}