<tr>
	<td>
		<a href="{{ route('backend.category.edit', $category->id) }}">
			<span class=" @if ($base) text-blue @else text-green @endif ">{{ $category->title }}</span>
		</a>
	</td>
	<td>{{ $category->position }}</td>
	<td>@if(!$base)/{{ $category->parentOne->href }}/@endif{{ $category->href }}</td>
	<td>
		{{ $category->games()->count() }}
	</td>
</tr>