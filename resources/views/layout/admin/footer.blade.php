<footer class="main-footer hidden-print">
    {{-- <div class="pull-right hidden-xs">
        <b>Version</b> 0.1.0
    </div> --}}
    <strong>Copyright &copy; {{ date('Y') }} <a href="{{config('app.url')}}">{{--{{config('app.name')}}--}}</a>.</strong> All rights reserved.
</footer>
// With Blade Templates
{!! Khill\Lavacharts\Laravel\LavachartsFacade::renderAll() !!}
