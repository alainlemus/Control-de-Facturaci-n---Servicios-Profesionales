@if($pdfUrl)
    <iframe src="{{ $pdfUrl }}" class="w-full" style="height: 70vh" frameborder="0"></iframe>
@else
    <p>El archivo PDF no está disponible.</p>
@endif
