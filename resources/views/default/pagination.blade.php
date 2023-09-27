@if (!$datatable->recordsNotFound($paginator))

    {{-- First page --}} 
    @if (!$datatable->getOption('skip_total'))  
        <button type="button" data-datatable-page-index="1" @disabled($paginator->onFirstPage())>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
            </svg>      
        </button>  
    @endif

    {{-- Previous page --}} 
    <button type="button" data-datatable-page-index="{{ $paginator->currentPage() - 1 }}" @disabled($paginator->onFirstPage())>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>      
    </button>  

    {{-- Pagination Elements --}}
    @if (!$datatable->getOption('skip_total'))  
        @foreach ($paginator->onEachSide($datatable->getOption('on_each_side'))->links()->getData()['elements'] as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <button type="button" disabled>
                    {{ $element }}
                </button>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button type="button" data-datatable-page-index="{{ $page }}" disabled>
                            {{ $page }}
                        </button>
                    @else
                        <button type="button" data-datatable-page-index="{{ $page }}">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach
    @endif

    {{-- Next page --}} 
    <button type="button" data-datatable-page-index="{{ $paginator->currentPage() + 1 }}" @disabled(!$paginator->hasMorePages())>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>      
    </button>  

    {{-- Last page --}} 
    @if (!$datatable->getOption('skip_total'))  
        <button type="button" data-datatable-page-index="{{ $paginator->lastPage() }}" @disabled(!$paginator->hasMorePages())>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
            </svg>      
        </button>  
    @endif
    
@endif