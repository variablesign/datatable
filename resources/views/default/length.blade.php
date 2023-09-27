@if (!$datatable->recordsNotFound($paginator))
    @foreach ($datatable->getOption('per_page_options') as $value => $text)
        <button type="button" data-datatable-per-page="{{ $value }}" @disabled($paginator->perPage() == $value)>
            {{ $text }}
        </button>  
    @endforeach
@endif