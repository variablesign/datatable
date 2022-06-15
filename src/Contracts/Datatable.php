<?php

namespace Veriablesign\Datatable\Contracts;

use Illuminate\Database\Eloquent\Builder;

abstract class Datatable
{
    /**
     * The columns that are searchable or filterable.
     *
     * @var array<int, string>
     */
    protected $searchable = [];

    /**
     * The columns that are sortable or orderable.
     *
     * @var array<string, string>
     */
    protected $sortable = [];

    /**
     * The default sortable or orderable column.
     *
     * @var string
     */
    protected $sortColumn = 'id';

    /**
     * The default sortable or orderable direction.
     *
     * @var string
     */
    protected $sortDirection = 'asc';

    /**
     * The number of records to display per page.
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * The maximum number of records to display per page.
     *
     * @var int
     */
    protected $maxPerPage = 50;

    private $search;

    private $query;

    private $total;

    public function __construct()
    {
        $this->total = request('get_total') ? true : false;
        $this->search = request('search');
        $this->perPage = request('per_page') ?? config('settings.paginate', $this->perPage);
        $this->perPage = ($this->perPage > $this->maxPerPage) ? $this->maxPerPage : $this->perPage;
        $this->sortDirection = request('sort_direction') ?? $this->sortDirection;
        $this->sortColumn = $this->sortable[request('sort_column')] ?? $this->sortColumn;
        $this->query = $this->query();

        $this->boot();
    }

    final public function render(?string $view = null, array $data = []): mixed
    {
        $results = $this->query->when($this->search, function ($query) {
            return $query->where(function ($query) {
                foreach ($this->searchable ?? [] as $column) {
                    $query->orWhere($column,'like','%'.$this->search.'%');
                }
            });
        })
        ->orderBy($this->sortColumn, $this->sortDirection);

        // Return results only
        if ($view === null && $this->total === false) {
            return $results->simplePaginate($this->perPage)->withQueryString();
        }

        // Return view with results
        if ($view !== null && $this->total === false) {
            return view($view, $data, [
                'paginator' => $results->simplePaginate($this->perPage)->withQueryString()
            ]);
        }

        // Return total records only
        if ($this->total === true) {
            $totalRecords = $results->count();
            $lastPage = ceil($totalRecords / $this->perPage);

            return response()->json([
                'total' => $totalRecords,
                'last_page' => ($lastPage <= 0) ? 1 : $lastPage
            ]);
        } 
    }

    protected function boot(): void
    {
        //
    }

    abstract protected function query(): Builder;

    abstract protected function columns(): array;
}
