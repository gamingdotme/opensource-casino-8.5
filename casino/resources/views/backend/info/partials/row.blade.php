<tr>
    <td><a href="{{ route('backend.info.edit', $info_item->id) }}">{{ $info_item->title }}</a></td>
    <td>{{ str_replace('|', ', ', $info_item->roles) }}</td>
    <td>{{ $info_item->days }}</td>
</tr>
