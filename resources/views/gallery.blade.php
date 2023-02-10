@foreach ($images as $image)
    <img src="{{ url('/assets/' . $image )}}" alt="Image" style="height:271px; max-height: 336px; max-width:336px; width: 263px;">
@endforeach