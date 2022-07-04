<turbo-frame id="datatable-frame">
    @hasrecords($paginator)
        <table 
            class="table table-list table-hover" 
            data-language-search="{{ $table->getSearchLanguage() }}"
            data-searchable="{{ empty($table->getSearchable()) ? 'false' : 'true' }}"
        >     
            @include('datatable::datatable.partials.header', [
                'table' => $table
            ])

            <tbody>
                @forelse ($paginator as $row)
                    @php
                        $index = $paginator->firstItem() + $loop->index;
                    @endphp
                    <tr
                        class="{{ $table->modifyRow('class', $row) }}"
                        id="{{ $table->modifyRow('id', $row) }}"
                        {!! $table->modifyRow('attributes', $row) !!}
                    >
                        @foreach ($table->getColumns() as $key => $column)
                            <td 
                                class="{{ is_null($column) ? 'table-action' : data_get($column, 'classAttribute') }}"
                                {!! $table->formatAttributes(data_get($column, 'attributes')) !!}
                            >
                                @if ($table->isIndexColumn($key))
                                    {{ $index }}
                                @else
                                    {{ $table->modifyColumn($key, $row) }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="20">
                            @include('datatable::datatable.partials.query', [
                                'table' => $table
                            ])
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @include('datatable::datatable.partials.pagination', [
            'paginator' => $paginator,
            'table' => $table
        ])

    @else
        
        {!! $table->start() !!}

    @endhasrecords
</turbo-frame>