<?php

namespace VariableSign\Datatable\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

abstract class Datatable
{
    /**
     * A name to describe your records.
     *
     * @var string
     */
    protected $name = 'records';

    /**
     * The default sortable column.
     *
     * @var string
     */
    protected $sortColumn = 'id';

    /**
     * The default sort direction.
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

    /**
     * Set a custom placeholder text for the search input.
     *
     * @var string
     */
    protected $searchLanguage = null;

    private $searchable = [];
    private $search;
    private $sort;
    private $query;
    private $total;

    public function __construct()
    {
        $this->total = request('get_total') ? true : false;
        $this->search = request('search');
        $this->sort = request('sort_column') ?? $this->sortColumn;
        $this->perPage = request('per_page') ?? config('settings.paginate', $this->perPage);
        $this->perPage = ($this->perPage > $this->maxPerPage) ? $this->maxPerPage : $this->perPage;
        $this->sortDirection = request('sort_direction') ?? $this->sortDirection;
        $this->sortColumn = $this->sortColumns($this->sort) ?? $this->sortColumn;
        $this->searchable = $this->searchColumns();
        $this->query = $this->query();
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

        // Return total records only
        if ($this->total === true) {
            $totalRecords = $results->count();
            $lastPage = ceil($totalRecords / $this->perPage);

            return response()->json([
                'total' => $totalRecords,
                'last_page' => ($lastPage <= 0) ? 1 : $lastPage
            ]);
        } 
        
        // Paginate
        $allData = [
            'paginator' => $results->simplePaginate($this->perPage)->withQueryString(),
            'columns' => $this->headerColumns(),
            'language' => [
                'search' => $this->getSearchLang()
            ]
        ];

        // Return view with results
        if ($view !== null && $this->total === false) {
            return view($view, $data, $allData);
        }

        return $allData;
    }

    private function searchColumns(?bool $keys = false): array
    {
        $columns = collect($this->columns());

        $columns = $columns->filter(function ($value, $key) {
            $search = data_get($value, 'search', false);
            return $search;
        })->map(function ($item, $key) {
            if (is_array(data_get($item, 'search'))) {
                return data_get($item, 'search');
            }

            return data_get($item, 'name');
        });

        return $keys ? $columns->keys()->toArray() : $columns->flatten()->filter()->toArray();
    }

    private function sortColumns(?string $key = null): null|string|array
    {
        $columns = collect($this->columns());

        $columns = $columns->filter(function ($value, $key) {
            return data_get($value, 'sort', false);
        })->map(function ($item, $key) {
            return data_get($item, 'name');
        });

        $columns = $columns->filter()->toArray();

        return $key ? data_get($columns, $key) : $columns;
    }

    private function headerColumns(): array
    {
        $columns = collect($this->columns());

        $columns = $columns->map(function ($item, $key) {
            if ((is_array($item) && array_search($this->sort, $item, true)) || $this->sort == $key) {
                return array_merge($item, ['direction' => $this->sortDirection]);
            }

            return $item;
        });

        return $columns->toArray();
    }

    private function getSearchLang(): ?string
    {
        if ($this->searchLanguage != null) {
            return $this->searchLanguage;
        }

        $values = $this->searchColumns(true);
        $total = count($values);
        $last = array_pop($values);

        if ($total == 0) {
            return 'Search ' . $this->name . '...';
        }

        if ($total > 1) {
            $last = ' or ' . $last . '...';
        } else {
            $last = '...';
        }

        $name = 'Search ' . $this->name . ' by ';

        return $name . implode(', ', $values) . $last;
    }

    abstract protected function query(): Builder;

    abstract protected function columns(): array;
}
