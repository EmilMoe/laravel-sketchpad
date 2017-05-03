<?php namespace davestewart\sketchpad\help\docs;

use davestewart\sketchpad\config\SketchpadConfig;
use davestewart\sketchpad\services\Sketchpad;
use Illuminate\View\FileViewFinder;
use Illuminate\View\View;
use Illuminate\Http\Request;

/**
 * Get to know Sketchpad's core functionality
 *
 * @package App\Http\Controllers
 */
class BasicsController
{

	public function index()
	{
		md('sketchpad::help/basics/index');
	}

	/**
	 * Run a method just by clicking on its label
	 *
	 * @group Execution
	 */
	public function runMethod()
	{
		list($s, $m) = explode(".", microtime(true));
		$date = date('H:i:s', $s) . '.' . $m;
		return view('sketchpad::help.basics.run', compact('date'));

	}

	/**
	 * Method parameters show in the UI as input fields, and re-call the method each time they're changed
	 *
	 * @param string $name
	 */
	public function parameters($name = 'World')
	{
?>
<p>The result of this call is:</p>
<pre>Hello, <?php echo $name ?>!</pre>
<p>Optional parameters are exposed as editable front-end inputs:</p>
<pre class="code php">
public function parameters($name = 'World')
{
    echo "Hello, $name!";
}
</pre>
<p>Update the parameter to automatically call the method again</p>
<?php
	}

	/**
	 * Your method's parameter types determine the parameter UI and the submitted values
	 *
	 * @param string $string    This is a string
	 * @param int    $number    This is a number
	 * @param bool   $boolean   This is a boolean
	 * @param mixed  $mixed     This could be any type
	 */
	public function typeCasting($string = 'hello', $number = 1, $boolean = true, $mixed = null)
	{
		return view('sketchpad::help/basics/typecasting', ['params' => func_get_args()]);
	}

	/**
	 * Declare a special optional parameter to test code before running it
	 *
	 * @param int $id
	 * @param bool $run
	 */
	public function testMode($id = 1, $run = false)
	{
		$mode = $run ? true : 'info';
		$status = !!$run
			? "Action taken for user $id !"
			: "Showing user $id";
		alert($status, $mode);
		echo view('sketchpad::help.basics.testmode');
	}

	/**
	 * Sketchpad catches framework exceptions, displays the output, and highlights the method until it's corrected and called again. If you're using Sketchpad Reload to watch the controller or related PHP files, the page will simply reload when the error is fixed.
	 */
	public function exceptions()
	{
		echo 'Foo is : ' . $foo;
	}

	/**
	 * The first line of DocBlock comments are shown in the method list and the page heading
	 *
	 * @group Organisation
	 */
	public function comments()
	{
?>
<p>This makes it easy to see what a method does before calling it:</p>
<pre class="code php">
/**
 * The first line of DocBlock comments are shown in the method list and the page heading
 *
 * This line will not be shown
 */
public function comments()
{

}
</pre>
<?php
	}

	/**
	 * Add index "pages" to controllers by providing an `index()` method and returning text, a view, markdown, etc
	 */
	public function indexPage()
	{
?>
<p>It's as simple as this:</p>
<pre class="code php">
class SomeController extends Controller
{
    public function index()
    {
        md('path.to.index'); // example uses markdown, but you could just as easily use Blade
    }
}</pre>
<p>When the controller is selected in the left hand menu, it will show an index page.</p>

<p>If you want to cheat, just save a markdown file in the same folder as the controller:</p>

<pre class="code php">
md(__DIR__ . '/some.md');
</pre>

<p>See the <a href="../output/markdown">markdown</a> example for more info about the <code>md()</code> method.</p>
<?php
	}

	/**
	 * Customise Sketchpad with user scripts and styles
	 *
	 * @group Advanced
	 */
	public function assets(SketchpadConfig $config)
	{
		$route = $config->route;
		$assets = $config->settings->get('paths.assets');
?>

		<p>Sketchpad allows you to add custom assets to the app by way of:</p>
		<ul>
			<li>Custom user asset files</li>
			<li>Loadable asset URLs</li>
		</ul>
		<p>During setup, two starter files were copied to your installation's <code>assets/</code> folder.</p>
		<pre>
<?php echo $assets . 'scripts.js'; ?>

<?php echo $assets . 'styles.css'; ?>
</pre>
		<p>These files are set to load with Sketchpad by default, along with any other URLs you add (for example <a href="https://momentjs.com/" target="_blank">Moment.js</a>):</p>
		<pre>
https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.0/moment.js
$assets/scripts.js
$assets/styles.css
</pre>

		<p>Note the special "user assets" route <code>$assets/</code> which loads the static file contents directly – they do not need to be in your app's <code>/public/</code> folder!</p>
		<p>Feel free to <a href="../tags/css">edit these files</a> or update asset URLs on the <a href="<?= $route; ?>settings">settings</a> page.</p>
<?php
	}

	/**
	 * Sketchpad makes a few classes and variables available to you
	 */
	public function variables(SketchpadConfig $config)
	{
		$route = $config->route;
		$views = $config->views;
		$fullroute = Sketchpad::$route;
		echo view('sketchpad::help.basics.variables', compact('route', 'fullroute', 'views'));
	}


}