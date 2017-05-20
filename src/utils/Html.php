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
		 * Echo a paragraph tag, with optional class
		 *
		 * @param               $text
		 * @param bool|string   $class
		 */
		public static function p($text, $class = null)
		{
			$attr = $class === true
				? ' class="note"'
				: (is_string($class)
					? ' class="' .$class. '"'
					: '');
			echo "<p{$attr}>$text</p>";
		}

		/**
		 * Output preformatted text
		 *
		 * @param   string  $text
		 */
		public static function text($text)
		{
			echo "<pre>$text</pre>\n";
		}

		/**
		 * Output code with optional highlighting
		 *
		 * @param   string  $source
		 */
		public static function code($source)
		{
			if (class_exists($source))
			{
				@list ($class, $method, $comment) = func_get_args();
				is_string($method)
					? Code::method($class, $method, $comment)
					: Code::classfile($class, $method);
			}
			else if (file_exists($source))
			{
				@list ($path, $start, $end, $undent) = func_get_args();
				is_int($start)
					? Code::section($path, $start, $end, $undent)
					: Code::file($path, $start);
			}
			else
			{
				Code::output($source);
			}
		}

		/**
		 * Output a Bootstrap info / alert div
		 *
		 * @param   string  $html   The HTML or text to display
		 * @param   string  $class  An optional CSS class, can be info, success, warning, danger
		 * @param   string  $icon   An optional FontAwesome icon string
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
			echo '<div class="alert alert-' .$class. '" role="alert">' .$html. '</div>';
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
		 */
		public static function pr()
		{
			echo "\n" . '<pre class="code php">' . "\n";
			$args = func_get_args();
			print_r( count($args) === 1 ? $args[0] : $args);
			echo "</pre>\n\n";
		}

		/**
		 * print_r() and die
		 */
		public static function pd()
		{
			self::pr(func_get_args());
			exit;
		}

		/**
		 * var_dump() passed arguments, with slightly nicer formatting than the default
		 */
		public static function vd()
		{
			echo "\n" . '<pre class="code php">' . "\n";
			$args = func_get_args();
			ob_start();
			var_dump(count($args) === 1 ? $args[0] : $args);
			$output = ob_get_contents();
			ob_end_clean();
			$output = preg_replace('/\\]=>[\\r\\n]+\s+/', '] => ', $output);
			$output = preg_replace('/^(\s+)/m', '$1$1', $output);
			echo $output;
			echo "</pre>\n\n";

		}

		/**
		 * List an object's properties in a nicely formatted table
		 *
		 * @param        $values
		 * @param string $options
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
			echo view('sketchpad::html.list', $data);
		}

		/**
		 * List an array of objects in a nicely formatted table
		 *
		 * @param      $values
		 * @param string $params
		 */
		public static function tb($values, $params = '')
		{
			$values = $values instanceof Paginator
				? $values->items()
				: ($values instanceof Arrayable
					? $values->toArray()
					: (array) $values);
			if(empty($values))
			{
				alert('Warning: tb() $values is empty', false);
				return;
			};

			$params = urldecode($params);
			//pr($params);
			$opts   = new Options($params);
			$keys   = array_keys( (array) $values[0]);
			$options =
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
			if($opts->pre === 1)
			{
				$options['class'] .= ' pre';
				$options['pre'] = [];
			}
			if($opts->type !== 'text')
			{
				$options['class'] .= ' table-bordered table-striped data';
			}
			if($opts->width)
			{
				$options['style'] .= ';' . self::getCss($opts->width);
			}
			if($opts->wide)
			{
				$options['style'] .= ';width:100%;';
			}
			if ($opts->keys)
			{
				$keys = array_values(array_filter((array) $opts->keys));
				if (in_array('*', $keys))
				{
					$src    = array_diff($keys, ['*']);
					$diff   = array_diff($options['keys'], $keys);
					$keys   = array_merge($src, $diff);
				}
				$options['keys'] = $keys;
			}

			$options['cols'] = array_pad(array_map(function($value)
			{
				return self::getCss($value);
			}, $options['cols']), count($options['keys']), '');

			echo view('sketchpad::html.table', $options);
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
			echo '<div data-format="json">' .json_encode($data). '</div>';
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

			// echo
			echo '<div data-format="markdown">' .$contents. '</div>';
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
			echo $str;
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
