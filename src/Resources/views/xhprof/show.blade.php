@extends('layout')

@section('title')
    XHPROF | @parent
@endsection

@section('head')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css">
        .nobr {
            white-space: nowrap;
        }

        .table {
            font-size: 0.75rem;
        }

        .table td, .table th {
            padding: 0.3rem;
        }

        .container {
            max-width: 95%;
        }
    </style>
@endsection

@section('navbar-outer', '')

@section('content')
    <div class="container">
        <h1>XHPROF</h1>
        <div class="row">
            <div class="col-12 col-md-4">
                <h2>Metadata:</h2>
                <p>
                    Start: {{ $xhprof['meta']['request_date'] ?? '-' }}<br>
                    End: {{ $xhprof['meta']['response_date'] ?? '-' }}<br>
                    Duration: {{ round($xhprof['meta']['request_duration'] ?? 0, 3) }} secs.
                </p>
            </div>
            <div class="col-12 col-md-4">
                <h2>Request:</h2>
                <p>
                    ID: {{ $xhprof['meta']['request_id'] ?? '-' }}<br>
                    {{ $xhprof['meta']['request_method'] ?? '-' }} {{ $xhprof['meta']['url'] ?? '-' }}<br>
                </p>
            </div>
            <div class="col-12 col-md-4">
                <h2>Response:</h2>
                <p>
                    ID: {{ $xhprof['meta']['response_id'] ?? '-' }}<br>
                    Code: {{ $xhprof['meta']['response_code'] ?? '-' }}
                </p>
            </div>
        </div>

        <h2>Main</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-light">
                <tr>
                    <th class="nobr" scope="col">Handler<br>&nbsp;</th>
                    <th class="nobr" scope="col" style="text-align: right;">Calls<br>&nbsp;</th>
                    <th class="nobr" scope="col" style="text-align: right;">Calls<br>(%)</th>

                    <th class="nobr" scope="col" style="text-align: right;">IWall<br>(µs)</th>
                    <th class="nobr" scope="col" style="text-align: right;">IWall<br>(%)</th>
                    <th class="nobr" scope="col" style="text-align: right;">EWall<br>(µs)</th>
                    <th class="nobr" scope="col" style="text-align: right;">EWall<br>(%)</th>

                    <th class="nobr" scope="col" style="text-align: right;">ICpu<br>(µs)</th>
                    <th class="nobr" scope="col" style="text-align: right;">ICpu<br>(%)</th>
                    <th class="nobr" scope="col" style="text-align: right;">ECpu<br>(µs)</th>
                    <th class="nobr" scope="col" style="text-align: right;">ECpu<br>(%)</th>

                    <th class="nobr" scope="col" style="text-align: right;">IMemUse<br>(B)</th>
                    <th class="nobr" scope="col" style="text-align: right;">IMemUse<br>(%)</th>
                    <th class="nobr" scope="col" style="text-align: right;">EMemUse<br>(B)</th>
                    <th class="nobr" scope="col" style="text-align: right;">EMemUse<br>(%)</th>

                    <th class="nobr" scope="col" style="text-align: right;">Peak<br>(B)</th>
                </tr>
                </thead>
                <tbody>
                    @include('xhprof.inc.subcall', ['data' => $xhprof['profile']['main()'], 'depth' => 0])
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
@endsection
