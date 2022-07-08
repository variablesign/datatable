@if ($table->showPagination())
    <div 
        class="d-flex align-items-center px-2" 
        id="table-paginator-data" 
        data-paginator-url="{{ request()->fullUrlWithQuery([
            $table->request('get_total_records', true) => 'true'
        ]) }}"
    >
        <!-- Pagination Total -->
        <span>
            <div class="btn-group dropup">
                <button 
                    type="button" 
                    class="btn dropdown-toggle p-2 hover:bg-gray-50 dark:hover:bg-dark-600" 
                    {!! !empty($table->getPerPageOptions()) ? 'data-bs-toggle="dropdown"' : '' !!}
                >
                    {{ $paginator->firstItem() ? $paginator->firstItem() : 0 }} — {{ $paginator->firstItem() ? (($paginator->firstItem() - 1) + $paginator->count()) : 0 }} of 
                    <span class="ms-1" data-paginator-total>
                        <svg class="w-7 h-7 animate-spin" viewBox="0 0 24 24" fill="none">
                            <g stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" opacity="0.3"></circle>
                                <path d="M12 2c5.5 0 10 4.5 10 10"></path>
                            </g>
                        </svg>
                    </span>
                </button>
                @if (!empty($table->getPerPageOptions()))
                    <ul class="dropdown-menu">
                        @foreach ($table->getPerPageOptions() as $key => $value)
                            <li>
                                <button 
                                    class="dropdown-item" 
                                    type="button" 
                                    data-href="{{ request()->fullUrlWithQuery([
                                        'page' => 1, 
                                        $table->request('per_page', true) => $key
                                    ]) }}" 
                                    data-turbo-frame="datatable-frame" 
                                    @disabled($key == $paginator->perPage())
                                >{{ $value }}</button>
                            </li>                        
                        @endforeach
                    </ul>
                @endif
            </div>
        </span>
        
        <!-- Pagination Page Count -->
        <span class="ms-auto me-4">
            <div class="btn-group dropup">
                <button type="button" class="btn dropdown-toggle p-2 hover:bg-gray-50 dark:hover:bg-dark-600" data-bs-toggle="dropdown">
                    {{ $paginator->currentPage() }} of 
                    <span class="ms-1" data-paginator-last>
                        <svg class="w-7 h-7 animate-spin" viewBox="0 0 24 24" fill="none">
                            <g stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" opacity="0.3"></circle>
                                <path d="M12 2c5.5 0 10 4.5 10 10"></path>
                            </g>
                        </svg>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button class="dropdown-item" type="button" data-href="{{ $paginator->url(1) }}" data-turbo-frame="datatable-frame" @disabled($paginator->onFirstPage())>First Page</button>
                    </li>
                    <li>
                        <button class="dropdown-item" type="button" data-href="{{ $paginator->url(1) }}" data-turbo-frame="datatable-frame" data-paginator-last-page @disabled(!$paginator->hasMorePages())>Last Page</button>
                    </li>
                </ul>
            </div>
        </span>

        <!-- Pagination Links -->
        <span class="d-flex">
            @if (!$paginator->onFirstPage())
                <button type="button" class="btn p-2 me-1 hover:bg-gray-50 dark:hover:bg-dark-600" data-href="{{ $paginator->previousPageUrl() }}" data-turbo-frame="datatable-frame">
                    <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none">
                        <path d="M4.75 11.98h14.5M11.25 18.25 4.75 12l6.5-6.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>    
            @endif
            @if ($paginator->hasMorePages())
                <button type="button" class="btn p-2 me-1 hover:bg-gray-50 dark:hover:bg-dark-600" data-href="{{ $paginator->nextPageUrl() }}" data-turbo-frame="datatable-frame">
                    <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none">
                        <path d="M4.75 11.98h14.5M12.75 5.75l6.5 6.25-6.5 6.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            @endif
        </span>
    </div>
@endif