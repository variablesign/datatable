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

    @endforeach
</div>