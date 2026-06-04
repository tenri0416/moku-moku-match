@auth
    @if (!empty($shouldShowSatisfactionSurvey))
        @include('home._satisfaction-survey-modal-pc')
        @include('home._satisfaction-survey-modal-sp')
        @include('home._satisfaction-survey-modal-script')
    @endif
@endauth
