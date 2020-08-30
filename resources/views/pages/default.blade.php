<form class="cache_form">
    <input type="hidden" class="changed_through_ajax" name="xyz" value="0" />
</form>

<div class="container-fluid">
    <div class="card" style="margin-bottom: 0; box-shadow: none">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="grid">
                    {!! $grid !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ajax-loader" style="position: absolute !important; top: 25%; left: 50%; display: none">
    <img src="{{ asset('vendor/grids/loader.svg') }}" />
</div>
@section('grid_js')
    <script src="{{ asset('vendor/grids/grid.js') }}"></script>
@endsection
@section('grid_css')
    <link href="{{ asset('vendor/grids/grid.css') }}">
@endsection
