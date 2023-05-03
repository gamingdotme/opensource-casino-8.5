<tr>
	<td>{{ $article->id }}</td>
	<td><a href="{{ route('backend.article.edit', $article->id) }}">{{ $article->title }}</a></td>
	<td>{{ $rule->created_at }}</td>
</tr>