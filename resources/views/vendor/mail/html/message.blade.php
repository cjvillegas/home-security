@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <img src="{{ config("app.url") }}/images/stylelogo.png" class="logo" alt="Stylebyglobal Logo">
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            This message is auto-generated. Do not reply to this. If you have any concerns, please contact your administrators.
            <br><br>
            © {{ date('Y') }} Stylebyglobal - Production. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
