@php
    $pdfFontPath = storage_path('app/fonts/ipaexg.ttf');
@endphp

@font-face {
    font-family: 'AppJapanese';
    font-style: normal;
    font-weight: normal;
    src: url("file://{{ $pdfFontPath }}") format("truetype");
}

@font-face {
    font-family: 'AppJapanese';
    font-style: normal;
    font-weight: bold;
    src: url("file://{{ $pdfFontPath }}") format("truetype");
}

* {
    font-family: 'AppJapanese', sans-serif !important;
}
