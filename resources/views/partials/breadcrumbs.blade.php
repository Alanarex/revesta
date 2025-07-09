<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $crumb)
            <li class="breadcrumb-item">
                @if (!empty($crumb['url']))
                    <a href="{{ $crumb['url'] }}" style="
    color: #9CC41B;">
                        {{ $crumb['label'] }}</a>
                @else
                    {{ $crumb['label'] }}
                @endif
            </li>
        @endforeach
    </ol>
</nav>
