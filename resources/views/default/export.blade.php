<form action="{{ request()->fullUrlWithQuery([$datatable->getOption('request.map.export') => 'xlsx']) }}">
    <button type="submit">
        Export
    </button>  
</form>