<h3>View variables</h3>
<p>Currently, only one variable is exposed to the view:</p>

<table class="table">
	<tbody>
	<tr>
		<td style="width:180px"><code>$route</code></td>
		<td>The base route the front end is accessed on (currently <code>{{ $route }}</code>) useful to access assets or routes.</td>
	</tr>
	</tbody>
</table>



<h3>Route variables</h3>
<p>The <code>Sketchpad</code> service has various values pertaining to the called route:</p>

<table class="table">
	<tbody>
		<tr>
			<td style="width:180px"><code>Sketchpad::$route</code></td>
			<td>The currently-called controller route (currently <code>{{ $fullroute }}</code>)</td>
		</tr>
		<tr>
			<td><code>Sketchpad::$params</code></td>
			<td>The same parameters passed to the current method, but as an associative array</td>
		</tr>
		<tr>
			<td><code>Sketchpad::$form</code></td>
			<td>Any <a href="../output/forms">form data</a> passed from the front end. Use this in place of <code>Request::all()</code></td>
		</tr>
	</tbody>
</table>

<p>To access them, reference the class statically:</p>
<pre class="code php">
use davestewart\sketchpad\services\Sketchpad;

public function test()
{
    if (Sketchpad::$form) { ... }
}
</pre>



<h3>Config variables</h3>
<p>The <code>SketchpadConfig</code> class provides access to current configuration values:</p>
<table class="table">
	<tbody>
	<tr>
		<td style="width:180px"><code>$config->route</code></td>
		<td>The base route the front end is accessed on (currently <code>{{ $route }}</code>)</td>
	</tr>
	<tr>
		<td style="width:150px"><code>$config->controllers</code></td>
		<td>The configured list of controller folder paths</td>
	</tr>
	<tr>
		<td style="width:150px"><code>$config->assets</code></td>
		<td>The configured user assets folder</td>
	</tr>
	<tr>
		<td style="width:150px"><code>$config->views</code></td>
		<td>The configured user views folder</td>
	</tr>
	<tr>
		<td style="width:150px"><code>$config->settings</code></td>
		<td>A wrapper for the front end settings, automatically configured in <code>storage/sketchpad/sketchpad.json</code></td>
	</tr>
	<tr>
		<td style="width:150px"><code>$config->admin</code></td>
		<td>The admin settings, manually configured in <code>storage/sketchpad/admin.json</code></td>
	</tr>
	</tbody>
</table>

<p>To access them, resolve the instance via dependency injection or <code>app()</code>:</p>
<pre class="code php">
use davestewart\sketchpad\config\SketchpadConfig;

public function test(SketchpadConfig $config)
{
    // alternatively: $config = app(SketchpadConfig::class);
    if ($config->admin->settings) { ... }
}
</pre>
