@if ($table->showHeader())
    <thead>
        <tr>
            @foreach ($table->getColumns() as $key => $column)

                @if (array_key_exists('sortable', $column ?? []))
                    <th 
                        class="text-nowrap h-14 {{ data_get($column, 'classAttribute') }}"
                        {!! $table->formatAttributes(data_get($column, 'attributes')) !!}
                    >
                        <a 
                            class="text-decoration-none fw-normal text-gray-400 dark:text-gray-300" 
                            href="#" 
                            data-href="{{ request()->fullUrlWithQuery([
                                'page' => 1, 
                                $table->request('sort_column', true) => $table->setSortColumn($key),
                                $table->request('sort_direction', true) => ($table->request('sort_direction') == 'asc') || (!$table->request('sort_direction') && data_get($column, 'direction') == 'asc') ? 'desc' : 'asc'
                            ]) }}" 
                            data-turbo-frame="datatable-frame"
                        >
                            {{ data_get($column, 'text') }}

                            @if ((($table->request('sort_column') == $table->setSortColumn($key)) && $table->request('sort_direction')) || (!$table->request('sort_column') && data_get($column, 'direction')))
                                <span class="d-inline-block ms-1 {{ ($table->request('sort_direction') == 'desc') || (!$table->request('sort_direction') && data_get($column, 'direction') == 'desc') ? 'rotate-180' : '' }}">↑</span>
                            @endif
                        </a>
                    </th>
                @else
                    <th 
                        class="text-nowrap h-14 {{ data_get($column, 'classAttribute') }}"
                        {!! $table->formatAttributes(data_get($column, 'attributes')) !!}
                    >
                        <span class="fw-normal text-gray-400 dark:text-gray-300">
                            {{ data_get($column, 'text') }}
                        </span>
                    </th>
                @endif

            @endforeach
        </tr>
    </thead>
@endif