 <tr>
    <td>{!! str_repeat('&nbsp;', $depth*4) !!}{{ $data['callee'] }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['ct'] }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['ctp'] }}</td>

    <td class="nobr" style="text-align: right;">{{ $data['wt'] }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['wtp'] }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['ewt'] }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['ewtp'] }}</td>

    <td class="nobr" style="text-align: right;">{{ $data['cpu'] }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['cpup'] }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['ecpu'] }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['ecpup'] }}</td>

    <td class="nobr" style="text-align: right;">{{ size_format($data['mu'], 0) }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['mup'] }}</td>
    <td class="nobr" style="text-align: right;">{{ size_format($data['emu'], 0) }}</td>
    <td class="nobr" style="text-align: right;">{{ $data['emup'] }}</td>

    <td class="nobr" style="text-align: right;">{{ size_format($data['pmu'], 0) }}</td>
</tr>
@if ($depth < 25)
    @foreach($data['children'] as $data)
        @if ($data['wt'] > 100)
            @include('xhprof.inc.subcall', ['data' => $data, 'depth' => $depth+1])
        @endif
    @endforeach
@endif

<!-- /*
continue(strpos($name, 'mysqli::') === 0)
if (preg_match('/::.*{closure}/', $caller) || in_array($name, ['TinyFramework\Core\Pipeline::call', 'TinyFramework\Core\Container::get']) || strpos($name, 'TinyFramework\Core\Container::call') === 0)
*/ -->
