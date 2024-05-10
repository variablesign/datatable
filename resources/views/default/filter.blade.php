<div>
    @foreach ($filters as $column => $filter)        

        <!-- Select -->
        @if ($filter['element']['type'] == 'select')      
            <div>
                <label>{{ $filter['title'] }}</label>
                <select name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}]" data-datatable-filter>
                    @foreach ($filter['data'] as $value => $label)  
                        <option value="{{ $value }}" @selected($value == $filter['value'])>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Date -->
        @if ($filter['element']['type'] == 'date')      
            <div>
                <label>{{ $filter['title'] }}</label>

                @if ($filter['element']['range'])
                    <input type="date" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}][start]" placeholder="{{ $filter['data']['start'] }}" data-datatable-filter/>
                    <input type="date" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}][end]" placeholder="{{ $filter['data']['end'] }}" data-datatable-filter/>
                @else   
                    <input type="date" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}]" placeholder="{{ $filter['data']['default'] }}" value="{{ $filter['value'] }}" data-datatable-filter/>
                @endif
            </div>
        @endif

        <!-- Text -->
        @if ($filter['element']['type'] == 'text')      
            <div>
                <label>{{ $filter['title'] }}</label>
                <div>
                    @if ($filter['element']['operators'])
                        <select name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}][operator]" data-datatable-filter>
                            @foreach ($filter['data'] as $value => $label)  
                                <option value="{{ $value }}" @selected($value == data_get($filter, 'value.operator'))>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}][value]" placeholder="{{ $filter['title'] }}" value="{{ data_get($filter, 'value.value') }}" data-datatable-filter/>
                    @else
                        <input type="text" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}]" placeholder="{{ $filter['title'] }}" value="{{ data_get($filter, 'value.value') }}" data-datatable-filter/>
                    @endif
                </div>
            </div>
        @endif

        <!-- Number -->
        @if ($filter['element']['type'] == 'number')      
            <div>
                <label>{{ $filter['title'] }}</label>
                <div class="flex gap-4">
                    <input type="number" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}][min]" placeholder="{{ $filter['data']['min'] }}" value="{{ data_get($filter, 'value.min') }}" data-datatable-filter/>
                    <input type="number" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}][max]" placeholder="{{ $filter['data']['min'] }}" value="{{ data_get($filter, 'value.max') }}" data-datatable-filter/>
                </div>
            </div>
        @endif

    @endforeach
</div>