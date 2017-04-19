<?php use \davestewart\sketchpad\utils\Html; ?>
<table class="table table-bordered table-striped {{ $class }}" style="{{ $style }}">
	@if($label)
		<caption>{{ $label }}</caption>
	@endif
	<thead>
		<tr>
			@if($index)
			<th style="width:20px">#</th>
			@endif
			@foreach($keys as $i => $key)
			<th style="{{ $cols[$i] }}">{{ $key }}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		@foreach($values as $i => $obj)
		<tr>
			@if($index)
			<th>{{ $i + 1 }}</th>
			@endif
			@foreach($obj as $key => $value)
			<?php
				$obj    = ! is_scalar($value);
				$class  = $obj || in_array($key, $pre) ? ' class="pre"' : '';
				$value  = $obj ? print_r($value, true) : $value;
				$isHtml = in_array($key, $html);
			?>
			@if($isHtml)
			<td<?php echo $class ?>>{!! Html::getText($value) !!}</td>
			@else
			<td<?php echo $class ?>>{{ Html::getText($value) }}</td>
			@endif
			@endforeach
		</tr>
		@endforeach
	</tbody>
</table>