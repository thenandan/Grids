<form class="cache_form">
    <input type="hidden" class="changed_through_ajax" name="xyz" value="0" />
</form>

<div class="container-fluid">
    <div class="card">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="grid">
                    {!! $grid !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ajax-loader" style="position: absolute !important; top: 25%; left: 50%">
    <img src="{{ asset('img/loader.svg') }}" />
</div>
@section('grid_js')
    <script src="{{ asset('grid/grid.js') }}"></script>
@endsection
@section('grid_css')
    <link href="{{ asset('grid/grid.css') }}">
@endsection
