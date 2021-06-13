<ul>
    @foreach($dumps as $dump)
        <li>
            <a href="{{ route('xhprof.show', ['id' => $dump, 'key' => container('request')->get('key')]) }}">{{ $dump }}</a>
        </li>
    @endforeach
</ul>
