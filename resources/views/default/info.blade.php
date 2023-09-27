@if (!$datatable->recordsNotFound($paginator))
    @if (!$datatable->getOption('skip_total'))  
        {{ $paginator->firstItem() ? number_format($paginator->firstItem()) : 0 }} — 
        {{ $paginator->firstItem() ? number_format((($paginator->firstItem() - 1) + $paginator->count())) : 0 }} of 
        {{ number_format($paginator->total()) }}
    @else
        {{ $paginator->firstItem() ? number_format($paginator->firstItem()) : 0 }} — 
        {{ $paginator->firstItem() ? number_format((($paginator->firstItem() - 1) + $paginator->perPage())) : 0 }}
    @endif
@endif