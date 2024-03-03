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
                <label class="block mb-2">{{ $filter['title'] }}</label>

                @if ($filter['element']['range'])
                    <input type="date" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}][start]" placeholder="{{ $filter['data']['start'] }}" data-datatable-filter/>
                    <input type="date" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}][end]" placeholder="{{ $filter['data']['end'] }}" data-datatable-filter/>
                @else   
                    <input type="date" name="{{ $datatable->getOption('request.map.filters') }}[{{ $column }}]" placeholder="{{ $filter['data']['default'] }}" value="{{ $filter['value'] }}" data-datatable-filter/>
                @endif
            </div>
        @endif

    @endforeach
</div>