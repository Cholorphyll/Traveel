<thead>
    <tr>
        <th scope="col">Url</th>
        <th scope="col">FAQ</th>
        <th scope="col">Descriptions</th>
        <th scope="col">Data Entry</th>
    </tr>
</thead>
<tbody>
    @if(!$getweb->isEmpty())   
        @foreach($getweb as $item)  
            <tr>
                <td>{{ $item->url }}</td>
                <td>
                    <input type="checkbox" class="update-checkbox" data-url-id="{{ $item->id }}" data-column="faq" {{ $item->faq == 1 ? 'checked' : '' }}>
                </td>
                <td>
                    <input type="checkbox" class="update-checkbox" data-url-id="{{ $item->id }}" data-column="description" {{ $item->description == 1 ? 'checked' : '' }}>
                </td>
                <td>
                    <input type="checkbox" class="update-checkbox" data-url-id="{{ $item->id }}" data-column="dataEntry" {{ $item->dataEntry == 1 ? 'checked' : '' }}>
                </td>
            </tr>
        @endforeach
    @else
        <td>Not available</td>
    @endif
</tbody>
