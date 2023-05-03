<tr>
	<td>{{ $rule->id }}</td>
	<td><a href="{{ route('backend.rule.edit', $rule->id) }}">{{ $rule->title }}</a></td>
	<td>{{ $rule->created_at }}</td>
</tr>