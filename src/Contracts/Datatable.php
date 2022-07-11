<?php

namespace VariableSign\Datatable\Contracts;

use Illuminate\Database\Eloquent\Builder;
use stdClass;

abstract class Datatable
{
    protected array $columns = [];

    protected array $editedColumns = [];

    protected mixed $classAttribute;

    protected mixed $idAttribute;

    protected mixed $attributes;

    protected ?array $tableAttributes;

    protected ?string $text;

    protected ?string $columnName;

    protected mixed $column;

    protected mixed $searchable;

    protected array $searchColumns;

    protected bool $sortable;

    protected ?string $indexColumn;

    protected array $sortColumns;
    
    protected ?object $rowAttributes;

    private array $paginationStyles = [
        'default',
        'minimal',
        'simple',
        'advanced'
    ];

    protected Builder $query;

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
     * @var null|int
     */
    protected $perPage = null;

    /**
     * Option for number of records to display per page.
     *
     * @var null|array
     */
    protected $perPageOptions = null;

    /**
     * Set a custom placeholder text for the search input.
     *
     * @var null|string
     */
    protected $searchLanguage = null;

    /**
     * Shows the table header.
     *
     * @var bool
     */
    protected $showHeader = true;

    /**
     * Shows the table pagination.
     *
     * @var bool
     */
    protected $showPagination = true;

    /**
     * Pagination style.
     *
     * @var null|string
     */
    protected $paginationStyle = null;

    /**
     * Instantiate a new datatable instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->boot();
        $this->query = $this->query();
        $this->perPage = $this->request('per_page') ?? $this->perPage ?? $this->config('per_page', 10);
        $this->sortDirection = $this->request('sort_direction') ?? $this->sortDirection;
        $this->sortColumn = $this->getSortable()[$this->request('sort_column')] ?? $this->sortColumn;
    }

    protected function addColumn(string $name, ?callable $callable = null): self
    {
        $this->columnName = null;
        $this->text = null;
        $this->searchable = false;
        $this->sortable = false;
        $this->classAttribute = null;
        $this->attributes = null;

        $callable = is_callable($callable) ? call_user_func($callable, $this) : null;
        $options = [
            'name' => $callable->columnName ?? null,
            'text' => $callable->text ?? null,
            'searchable' => $callable->searchable ?? null,
            'sortable' => $callable->sortable ?? null,
            'classAttribute' => $callable->classAttribute ?? null,
            'attributes' => $callable->attributes ?? null
        ];

        if ($name == $this->sortColumn) {
            $options['direction'] = $this->sortDirection;
        }
        
        $options = array_filter($options);
        $this->column = $name;
        $this->columns[$name] = empty($options) ? null : $options;

        return $this;
    }

    protected function editColumn(mixed $value = null): self
    {
        $this->editedColumns[$this->column] = $value;

        return $this;
    }

    protected function rowAttributes(callable $callable): self
    {
        $this->rowAttributes = $callable;

        return $this;
    }

    protected function tableAttributes(callable $callable): self
    {
        $this->idAttribute = null;
        $this->classAttribute = null;
        $this->attributes = null;

        $callable = is_callable($callable) ? call_user_func($callable, $this) : null;
        $options = [
            'idAttribute' => $callable->idAttribute ?? null,
            'classAttribute' => $callable->classAttribute ?? null,
            'attributes' => $callable->attributes ?? null
        ];
        
        $this->tableAttributes = array_filter($options);

        return $this;
    }

    protected function indexColumn(): self
    {
        $this->indexColumn = $this->column;

        return $this;
    }

    protected function name(?string $name = null): self
    {
        $this->columnName = $name;

        return $this;
    }

    protected function text(?string $text = null): self
    {
        $this->text = $text;

        return $this;
    }

    protected function setClassAttribute(null|string|callable $class = null): self
    {
        $this->classAttribute = $class;

        return $this;
    }

    protected function setIdAttribute(null|string|callable $id = null): self
    {
        $this->idAttribute = $id;

        return $this;
    }

    protected function setAttributes(null|array|callable $attributes = null): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function formatAttributes(?array $attributes = null): ?string
    {
        $html = null;
        foreach ($attributes ?? [] as $key => $value) {
            $html .= $key . '="' . $value . '" ';
        }

        return trim($html);
    }

    protected function searchable(bool|array $columns = true): self
    {
        $this->searchable = $columns;

        return $this;
    }

    protected function sortable(): self
    {
        $this->sortable = true;

        return $this;
    }

    public function getSearchable(): array
    {
        $columns = collect($this->columns);
        $columns = $columns->filter()->map(function ($item, $key) {
            if (is_array($item)) {
                if (array_key_exists('searchable', $item)) {
                    if (is_bool($item['searchable'])) {
                        return $key;
                    }

                    if (is_array($item['searchable'])) {
                        return $item['searchable'];
                    }              
                }
            }
        });

        return $columns->filter()->flatten()->all();
    }

    public function getSortable(): array
    {
        $columns = collect($this->columns);
        $columns = $columns->filter()->transform(function ($item, $key) {
            if (is_array($item)) {
                if (array_key_exists('sortable', $item)) {
                    if (array_key_exists('name', $item)) {
                        return $item['name'];
                    }

                    return $key;
                }
            }
        });

        return $columns->filter()->flip()->all();
    }

    public function getNameable(): array
    {
        $columns = collect($this->columns);
        $columns = $columns->filter()->transform(function ($item, $key) {
            if (is_array($item)) {
                if (array_key_exists('name', $item)) {
                    return $item['name'];
                }
            }
        });

        return $columns->filter()->all();
    }

    public function getSearchLanguage(): ?string
    {
        if ($this->searchLanguage != null) {
            return $this->searchLanguage;
        }

        $columns = collect($this->columns);
        $columns = $columns->filter()
            ->map(function ($item, $key) {
                if (is_array($item)) {
                    if (array_key_exists('searchable', $item)) {
                        if (is_bool($item['searchable'])) {
                            return $key;
                        }

                        if (is_array($item['searchable'])) {
                            return $item['searchable'];
                        }              
                    }
                }
            })
            ->filter()
            ->keys()
            ->map(function ($item, $key) {
                $item = data_get($this->getNameable(), $item) ?? $item;
                $item = str($item)->afterLast('.');
                return str_replace(['_', '-'], ' ', $item);
            })
            ->all();

        $total = count($columns);
        $last = ($total <= 1) ?: array_pop($columns);

        if ($total == 0) {
            return 'Search ' . $this->name . '...';
        }

        if ($total > 1) {
            $last = ' or ' . $last . '...';
        } else {
            $last = '...';
        }

        $name = 'Search ' . $this->name . ' by ';

        return $name . implode(', ', $columns) . $last;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTableAttributes(): ?array
    {
        return $this->tableAttributes ?? null;
    }

    public function isTextPaginationControl(): bool
    {
        return data_get($this->config('text_pagination_controls'), 'enable', false);
    }

    public function getTextPaginationControl(string $key): ?string
    {
        return data_get($this->config('text_pagination_controls'), $key);
    }

    public function isPaginationStyle(): bool
    {
        return in_array($this->config('pagination_style'), $this->paginationStyles);
    }

    public function getPaginationStyle(): string
    {
        return $this->paginationStyle ?? $this->config('pagination_style');
    }

    public function getPerPageOptions(): array
    {
        return $this->perPageOptions ?? $this->config('per_page_options', []);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setSortColumn(?string $key): string
    {
        return array_search($key, $this->getSortable());
    }

    public function isIndexColumn(?string $column): bool
    {
        if ($this->indexColumn ?? false) {
            return ($this->indexColumn == $column);
        }
        return false;
    }

    public function showHeader(): bool
    {
        return $this->showHeader;
    }

    public function showPagination(): bool
    {
        return $this->showPagination;
    }

    public function getEditedColumns(): array
    {
        return $this->editedColumns;
    }

    public function modifyColumn(string $key, $model, $index): mixed
    {
        if (array_key_exists($key, $this->getEditedColumns())) {
            $value = $this->getEditedColumns()[$key];
            $value = is_callable($value) ? call_user_func($value, $model, $index) : $value;

            return $value;
        }

        $key = str($key)->afterLast('.');

        return data_get($model, $key, '—');
    }

    public function modifyRow(string $key, $model, $index): mixed
    {
        $this->rowAttributes = $this->rowAttributes ?? null;
        $rowAttributes = $this->rowAttributes ? call_user_func($this->rowAttributes, $this) : null;
        $options = [
            'id' => $rowAttributes->idAttribute ?? null,
            'class' => $rowAttributes->classAttribute ?? null,
            'attributes' => $rowAttributes->attributes ?? null
        ];

        if (is_callable($rowAttributes->idAttribute ?? null)) {
            $options['id'] = call_user_func($rowAttributes->idAttribute, $model, $index);
        }

        if (is_callable($rowAttributes->classAttribute ?? null)) {
            $options['class'] = call_user_func($rowAttributes->classAttribute, $model, $index);
        }

        if (is_callable($rowAttributes->attributes ?? null)) {
            $options['attributes'] = call_user_func($rowAttributes->attributes, $model, $index);
        }

        $options = array_map(function ($value) {
            return is_array($value) ? $this->formatAttributes($value) : $value;
        }, $options);
        
        return data_get($options, $key);
    }

    final public function render(?string $view = null, array $data = []): mixed
    {
        $query = $this->query->when($this->request('search'), function ($query) {
            return $query->where(function ($query) {
                $terms = explode(' ', $this->request('search'));
                foreach ($this->getSearchable() ?? [] as $column) {
                    $query->orWhereIn($column, $terms);
                }

                foreach ($this->getSearchable() ?? [] as $column) {
                    $query->orWhere($column,'like','%'. $this->request('search') .'%');
                }
            });
        })
        ->when($this->sortColumn, function ($query) {
            return $query->orderBy($this->sortColumn, $this->sortDirection);
        });

        // Return only total records
        if ($this->request('get_total_records') ? true : false) {
            $totalRecords = $query->count();
            $lastPage = ceil($totalRecords / $this->perPage);

            return response()->json([
                'total' => $totalRecords,
                'last_page' => ($lastPage <= 0) ? 1 : $lastPage
            ]);
        } 

        $results = [
            'paginator' => $query->simplePaginate($this->perPage)->withQueryString(),
            'table' => $this
        ];

        // Return with custom view
        if ($view != null) {
            return view($view, $data, $results);
        } 

        return view('datatable::datatable.index', $data, $results);
    }

    public function request(string $key, bool $keyOnly = false): ?string
    {
        $request = data_get($this->config('request_map'), $key);

        if ($keyOnly) {
            return $request;
        }

        $request = e(strip_tags(request($request)));
        return empty($request) ? null : $request;
    }

    protected function config(?string $key = null, mixed $default = null): mixed
    {
        $key = $key ? 'datatable.' . $key : 'datatable';

        return config($key, $default);
    }

    abstract public function start();

    abstract public function query(): Builder;

    abstract public function boot();
}
