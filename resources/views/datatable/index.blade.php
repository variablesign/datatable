<turbo-frame id="datatable-frame">
    @hasrecords($paginator)
        <table 
            class="{{ data_get($table->getTableAttributes(), 'classAttribute', 'table table-list table-hover') }}"
            id="{{ data_get($table->getTableAttributes(), 'idAttribute') }}"
            {!! $table->formatAttributes(data_get($table->getTableAttributes(), 'attributes')) !!}
            data-language-search="{{ $table->getSearchLanguage() }}"
            data-searchable="{{ empty($table->getSearchable()) ? 'false' : 'true' }}"
        >     
            @include('datatable::datatable.header')

            <tbody>
                @forelse ($paginator as $row)
                    @php
                        $index = $paginator->firstItem() + $loop->index;
                    @endphp
                    <tr
                        class="{{ $table->modifyRow('class', $row, $index) }}"
                        id="{{ $table->modifyRow('id', $row, $index) }}"
                        {!! $table->modifyRow('attributes', $row, $index) !!}
                    >
                        @foreach ($table->getColumns() as $key => $column)
                            <td 
                                class="{{ is_null($column) ? 'table-action' : data_get($column, 'classAttribute') }}"
                                {!! $table->formatAttributes(data_get($column, 'attributes')) !!}
                            >
                                @if ($table->isIndexColumn($key))
                                    {{ $index }}
                                @else
                                    {{ $table->modifyColumn($key, $row, $index) }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="20">
                            @include('datatable::datatable.search.empty')
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @includeWhen($table->isPaginationStyle(), 'datatable::datatable.pagination.' . $table->getPaginationStyle())

    @else
        
        @if ($table->start())
            {!! $table->start() !!}
        @else
            @include('datatable::datatable.start')
        @endif

    @endhasrecords
</turbo-frame>