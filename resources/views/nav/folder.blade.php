<ul class="nav nav-pills nav-stacked">
	@foreach($routes as $route => $reference)
		@if($reference instanceof \davestewart\sketchpad\objects\route\ControllerReference)
			<?php
			$text   = str_replace('/', ' <span class="divider">&#9656;</span> ', str_replace('sketchpad/', '', trim($route, '/') ));
			$active = $reference->route === $uri ? 'active' : '';
			//pr($reference);
			?>
			<li class="{{ $active }}">
				<a
					class="controller"
					data-name="{{ $reference->class }}"
					href="/{{ $route }}">
					{!! $text !!}
					<!--<span class="badge badge-right">12</span>-->
				</a>
			</li>
		@endif
	@endforeach
</ul>
