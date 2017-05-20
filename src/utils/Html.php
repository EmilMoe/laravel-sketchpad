<?php namespace davestewart\sketchpad\utils;

use davestewart\sketchpad\config\SketchpadConfig;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Pagination\Paginator;

\View::addExtension('html', 'html');
\View::addExtension('vue', 'vue');
\View::addExtension('md', 'md');

/**
 * Html class
 */
class Html
{

	// ------------------------------------------------------------------------------------------------
	// output functions

		/**
		 * Return a paragraph tag, with optional class
		 *
		 * @param                   $text
		 * @param   bool|string     $class
		 * @return  string
		 */
		public static function p($text, $class = null)
		{
			$attr = $class === true
				? ' class="note"'
				: (is_string($class)
					? ' class="' .$class. '"'
					: '');
			return "<p{$attr}>$text</p>";
		}

		/**
		 * Return preformatted text
		 *
		 * @param   string  $text
		 * @return  string
		 */
		public static function text($text)
		{
			$text = htmlentities($text);
			return "<pre>$text</pre>\n";
		}

		/**
		 * Return code with optional highlighting
		 *
		 * @param   string  $source
		 * @return  string
		 */
		public static function code($source)
		{
			if (class_exists($source))
			{
				@list ($class, $method, $comment) = func_get_args();
				return is_string($method)
					? Code::method($class, $method, $comment)
					: Code::classfile($class, $method);
			}
			else if (file_exists($source))
			{
				@list ($path, $start, $end, $undent) = func_get_args();
				return is_int($start)
					? Code::section($path, $start, $end, $undent)
					: Code::file($path, $start);
			}
			return Code::output($source);
		}

		/**
		 * Return a Bootstrap info / alert div
		 *
		 * @param   string  $html   The HTML or text to display
		 * @param   string  $class  An optional CSS class, can be info, success, warning, danger
		 * @param   string  $icon   An optional FontAwesome icon string
		 * @return  string
		 */
		public static function alert($html, $class = 'info', $icon = '')
		{
			if(is_bool($class))
			{
				$state  = !! $class;
				$class  = $state ? 'success' : 'danger';
				$icon   = $state ? 'check' : 'times';
			}
			if ($icon)
			{
				$html   = '<i class="fa fa-' .$icon. '" aria-hidden="true"></i> ' . $html;
			}
			return '<div class="alert alert-' .$class. '" role="alert">' .$html. '</div>';
		}

		/**
		 * Return a Font Awesome icon
		 *
		 * @param   string|bool     $name       A FontAwesome icon name, or a boolean for colored tick/cross
		 * @param   string          $color      An optional colour
		 * @return  string
		 */
		public static function icon($name, $color = '')
		{
			if (is_bool($name))
			{
				$color = $name ? 'success' : 'danger';
				$name = $name ? 'check' : 'times';
			}
			$class = "icon fa fa-$name";
			$style = '';
			if (preg_match('/(info|success|warning|danger)/', $color))
			{
				$class .= ' text-' . $color;
			}
			else if ($color !== '')
			{
				$style = ' style="color:' .$color. '"';
			}
			return '<i class="' .$class. '" ' .$style. '></i>';
		}

		/**
		 * print_r() passed arguments
		 *
		 * @return  string
		 */
		public static function pr()
		{
			$str = "\n" . '<pre class="code php">' . "\n";
			$args = func_get_args();
			$str .= print_r( count($args) === 1 ? $args[0] : $args, 1);
			return $str . "</pre>\n\n";
		}

		/**
		 * var_dump() passed arguments, with slightly nicer formatting than the default
		 *
		 * @return  string
		 */
		public static function vd()
		{
			$str = "\n" . '<pre class="code php">' . "\n";
			$args = func_get_args();
			ob_start();
			var_dump(count($args) === 1 ? $args[0] : $args);
			$output = ob_get_contents();
			ob_end_clean();
			$output = preg_replace('/\\]=>[\\r\\n]+\s+/', '] => ', $output);
			$output = preg_replace('/^(\s+)/m', '$1$1', $output);
			$str .= $output;
			$str .= "</pre>\n\n";
			return $str;
		}

		/**
		 * List an object's properties in a nicely formatted table
		 *
		 * @param   mixed       $values
		 * @param   string      $options
		 * @return  string
		 */
		public static function ls($values, $options = '')
		{
			$opts = new Options($options);
			$data =
			[
				'values'    => $values,
				'style'     => $opts->get('style', ''),
				'class'     => $opts->get('class', ''),
			];
			if($opts->pre === 1)
			{
				$data['class'] .= ' pre';
			}
			if($opts->wide)
			{
				$data['style'] .= ';width:100%;';
			}
			return view('sketchpad::html.list', $data);
		}

		/**
		 * List an array of objects in a nicely formatted table
		 *
		 * @param   mixed   $values
		 * @param   string  $params
		 * @return  string
		 */
		public static function tb($values, $params = '')
		{
			// source data
			$values = $values instanceof Paginator
				? $values->items()
				: ($values instanceof Arrayable
					? $values->toArray()
					: (array) $values);

			// defend against empty data set
			$empty  = empty($values);

			// pre-convert data
			if (!$empty)
			{
				$values = array_map(function ($value) {
					return $value instanceof Arrayable
						? $value->toArray()
						: (array) $value;
				}, $values);
			}
			else
			{
				$values = [['error' => '...']];
			}

			// parameters
			$params         = urldecode($params);
			$opts           = new Options($params);
			$keys           = array_keys((array) $values[0]);

			// options
			$data =
			[
				'values'    => array_values($values),
				'keys'      => $keys,
				'id'        => $opts->get('id', ''),
				'caption'   => $opts->get('caption'),
				'index'     => $opts->has('index'),
				'class'     => $opts->get('class', ''),
				'type'      => $opts->get('type', 'data'),
				'style'     => $opts->get('style', ''),
				'width'     => $opts->get('width', ''),
				'cols'      => (array) $opts->get('cols'),
				'pre'       => (array) $opts->get('pre'),
				'html'      => (array) $opts->get('html'),
				'icon'      => (array) $opts->get('icon'),
			];

			// populate options
			if($opts->pre === 1)
			{
				$data['class'] .= ' pre';
				$data['pre'] = [];
			}
			if($opts->type !== 'text')
			{
				$data['class'] .= ' table-bordered table-striped data';
			}
			if($opts->width)
			{
				$data['style'] .= ';' . self::getCss($opts->width);
			}
			if($opts->wide)
			{
				$data['style'] .= ';width:100%;';
			}
			if ($opts->keys)
			{
				$keys = array_values(array_filter((array) $opts->keys));
				if (in_array('*', $keys))
				{
					$src    = array_diff($keys, ['*']);
					$diff   = array_diff($data['keys'], $keys);
					$keys   = array_merge($src, $diff);
				}
				$data['keys'] = $keys;
			}
			$data['cols'] = array_pad(array_map(function($value)
			{
				return self::getCss($value);
			}, $data['cols']), count($data['keys']), '');

			// handle empty data set
			if($empty)
			{
				$data['caption']    = 'No data';
				$data['keys']       = ['error'];
				$data['class']      .= ' error ';
			};

			// output table
			return view('sketchpad::html.table', $data);
		}


	// ------------------------------------------------------------------------------------------------
	// file format functions

		/**
		 * Converts to, and instructs Sketchpad to format an object as JSON in the front end
		 *
		 * Note that you can also have objects formatted as JSON by just returning them
		 *
		 * @param   mixed   $data
		 * @return  string
		 */
		public static function json($data)
		{
			return '<div data-format="json">' .json_encode($data). '</div>';
		}

		/**
		 * Loads a Markdown file, and instructs Sketchpad to transform it in the front end
		 *
		 * @param   string  $path An absolute or relative file reference
		 * @param   array   $data
		 * @return  string
		 */
		public static function md($path, $data = [])
		{
			// find file
			$abspath = preg_match('%^(/|[a-z]:)%i', $path) === 1
				? $path
				: \View::getFinder()->find($path);

			// get contents
			$contents = file_get_contents($abspath);

			// update values
			$data['route'] = app(SketchpadConfig::class)->route;
			foreach ($data as $key => $value)
			{
				$contents = preg_replace('/\{\{\s*' .$key. '\s*\}\}/', $value, $contents);
			}

			// return
			return '<div data-format="markdown">' .$contents. '</div>';
		}

		/**
		 * Loads a Vue file and optionally injects data into it
		 *
		 * @param   string  $path
		 * @param   mixed   $data
		 * @return  string
		 */
		public static function vue($path, array $data = null)
		{
			$path   = \View::getFinder()->find($path);
			$str    = file_get_contents($path);
			if($data)
			{
				$tag1 = '<scr'.'ipt>';
				$tag2 = '</scr'.'ipt>';
				$json = json_encode($data);
				$str = str_replace($tag1, $tag1 . "(function () {\n\tvar \$data = $json;", $str);
				$str = str_replace($tag2, '}())' . $tag2, $str);
			}
			return $str;
		}


	// ------------------------------------------------------------------------------------------------
	// utilities

		protected static function getCss($value, $css = 'width')
		{
			if(preg_match('/^[\d\.]+$/', $value))
			{
				$value .= 'px';
			}
			return "$css:$value";
		}

		public static function getText($value)
		{
			return is_bool($value)
				? $value ? 'true' : 'false'
				: $value;
		}

}
