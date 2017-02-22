<?php namespace davestewart\sketchpad\services;

use Config;
use davestewart\sketchpad\config\SketchpadConfig;
use Illuminate\Http\Request;
use davestewart\sketchpad\objects\install\JSON;
use davestewart\sketchpad\config\Paths;
use davestewart\sketchpad\config\InstallerSettings;
use davestewart\sketchpad\objects\scanners\Finder;


/**
 * Checks setup is OK and advises what to do if not
 *
 * @package davestewart\sketchpad\services
 */
class Setup
{

	// -----------------------------------------------------------------------------------------------------------------
	// properties




    // ------------------------------------------------------------------------------------------------
	// instantiation

        public function __construct()
		{

		}


	// -----------------------------------------------------------------------------------------------------------------
	// public methods

		public function index()
		{
		    // variables
            $request = app(Request::class);
		    $path = 'sketchpad/setup';

            // redirect
			return $request->path() !== $path
                ? redirect($path)
                : $this->view();
		}


    // ------------------------------------------------------------------------------------------------
    // setup

        /**
         * Shows the setup form view
         *
         * @return mixed
         */
		public function view()
		{
		    // default variables
            $finder = new Finder();
            $finder->start();

            // config
            $paths  = app(Paths::class);
            $config = app(SketchpadConfig::class);

            // base name
            $basePath   = base_path() . '/';
            $temp       = explode('/', base_path());
            $baseName   = array_pop($temp) . '/';

            // view path
            $temp       = Config::get('view.paths');
            $viewPath   = substr($temp[0], strlen(base_path() . '/'));

			// variables
			$app    = app();
			$data   = app(Sketchpad::class)->getVariables();
			$vars   =
			[
                'assets' => $config->route . 'assets/',
				'settings' =>
				[
					'route'             => $config->route,
					'basepath'          => $basePath,
					'basename'          => $baseName,
                    'viewpath'          => $viewPath,
                    'storagepath'       => $paths->relative($config->settings->src),
                    'controllerpath'    => trim($paths->relative($finder->path), '/'),
					'namespace'         => method_exists($app, 'getNamespace')
                                            ? trim($app->getNamespace(), '\\')
                                            : 'App\\',
                    'namespaces'        => (new JSON('composer.json'))->get('autoload.psr-4')
				]
			];

			// return view
			return view('sketchpad::setup', array_merge($data, $vars));
		}


    // ------------------------------------------------------------------------------------------------
    // form

        public function saveData($input)
        {
            $settings = new InstallerSettings();
            return $settings->save($input);
		}

        public function loadData()
        {
            $settings = new InstallerSettings();
            return $settings;
		}


}
