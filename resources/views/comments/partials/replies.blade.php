@foreach ($comments as $child)
    <x-comments::comment :comment="$child" :post="$post" :depth="$depth" :max-depth="$maxDepth" />
@endforeach
