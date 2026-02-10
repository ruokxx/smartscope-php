@foreach($posts as $post)
    @include('community.partials.post', ['post' => $post])
@endforeach
