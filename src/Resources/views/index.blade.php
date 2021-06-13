<ul>
    @foreach($dumps as $dump)
        <li>
            <a href="{{ route('xhprof.show', ['id' => $dump]) }}">{{ $dump }}</a>
        </li>
    @endforeach
</ul>
