<!DOCTYPE html>
<html>
    <head>
        <title>Fullstack Blog</title>
        <link rel="stylesheet" href="/css/app.css">
        <script>

            (function () {
                window.Laravel = {
                    csrfToken: '{{ csrf_token() }}'
                };
            })();

        </script>
    </head>
    <body>
        <div id="app">
        @if(Auth::check())
            <mainapp :user="{{Auth::user()}}" :permission="{{Auth::user()->role->permission}}"></mainapp>
        @else
            <mainapp :user="false"></mainapp>
        @endif
        </div>
    </body>
    <script src="{{mix('/js/all.js')}}"></script>
</html>
