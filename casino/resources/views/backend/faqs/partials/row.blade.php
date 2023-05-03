<tr>
	<td>{{ $faq->id }}</td>
	<td><a href="{{ route('backend.faq.edit', $faq->id) }}">{{ $faq->question }}</a></td>
	<td>{{ $faq->rank }}</td>
</tr>
