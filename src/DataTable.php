<?php

namespace VariableSign\DataTable;

use Illuminate\View\View;
use Illuminate\Support\Collection;
use VariableSign\DataTable\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder as QueryBuilder;

abstract class DataTable
{
    private Collection $columns;

    private Collection $setups;

    private array $options;

    private string $table;

    public array $data = [];

    protected ?string $orderColumn = null;

    private ?string $defaultOrderColumn = null;

    protected string $orderDirection = 'asc';

    protected ?int $perPage = null;

    protected ?array $perPageOptions = null;

    protected ?int $onEachSide = null;

    protected bool $skipTotal = false;

    protected ?bool $deepSearch = null;

    protected ?bool $saveState = null;

    protected ?array $saveStateFilter = null;

    protected ?string $storage = null;

    protected string $tableName = 'datatable';

    protected ?string $tableId = null;

    protected ?string $queryStringPrefix = null;

    protected ?bool $autoUpdate = null;

    protected ?bool $autoUpdateOnFilter = null;

    protected ?int $autoUpdateInterval = null;

    protected ?string $template = null;

    protected bool $showHeader = true;

    protected bool $showPageOptions = true;

    protected bool $showInfo = true;

    protected bool $showPagination = true;

    protected ?string $searchPlaceholder = null;

    public function __construct(string $table, bool $withColumns)
    {
        $this->table = $table;
        $this->data = $this->storage('data', []);
        $this->perPageOptions = $this->perPageOptions ?? $this->config('per_page_options');
        $this->autoUpdate = $this->autoUpdate ?? $this->config('auto_update');
        $this->autoUpdateInterval = ($this->autoUpdateInterval ?? $this->config('auto_update_interval')) * 1000;
        $this->defaultOrderColumn = $this->orderColumn;
        $this->columns = $this->setColumns($withColumns);
        $this->setups = $this->setSetups();
        $this->options = $this->setOptions();
    }

    private function storage(?string $key = null, mixed $default = null): mixed
    {
        return session('datatable.' . str($this->table)->replace('.', '_')->toString() . '.' . $key, $default);
    }

    private function config(?string $key = null, mixed $default = null): mixed
    {
        // $requestedKey = $key;
        $key = $key ? 'datatable.' . $key : 'datatable';

        // if ($this->queryStringPrefix && $requestedKey === 'request_map') {
        //     $requestMap = config($key, $default);
        //     $requestMap = array_map(function ($item) {
        //         return $this->queryStringPrefix . '_' . $item;
        //     }, $requestMap);

        //     return $requestMap;
        // }

        return config($key, $default);
    }

    private function getRequestMap(?string $key = null): string|array|null
    {
        $map = $this->config('request_map');

        if ($this->queryStringPrefix) {
            $map = array_map(function ($item) {
                return $this->queryStringPrefix . '_' . $item;
            }, $map);
        }

        return $key ? data_get($map, $key) : $map;
    }

    public function request(string $key): null|string|array
    {
        $request = data_get($this->getRequestMap(), $key);
        // $request = e(strip_tags(request($request, '')));
        return is_null($request) ? null : request()->get($request);

        // return empty($request) ? null : $request;
    }

    private function setColumns(bool $withColumns): Collection
    {
        if (!$withColumns) {
            return collect([]);
        }

        $columns = collect($this->columns());
        $columns = $columns->transform(function (object $item, int $key) {

            return [
                'name' => $item->name,
                'alias' => $item->alias,
                'title' => $item->title,
                'searchable' => $item->searchable,
                'sortable' => $item->sortable,
                'filterable' => $this->parseFilterableColumn($item),
                'ordered' => $item->sortable && $this->setOrderColumn() === $item->alias ? true : false,
                'direction' => $this->setOrderColumn() === $item->alias ? $this->setOrderDirection() : 'asc',
                'edit' => $item->edit,
                'index' => $item->index,
                'checkbox' => [
                    'enabled' => $item->checkbox,
                    'attributes' => $item->checkboxAttributes
                ],
                'attributes' => $item->attributes,
                'responsive' => $this->config('breakpoints.' . $item->responsive),
                'alignment' => $this->config('alignment.' . $item->alignment) ?? $this->config('alignment.left')
            ];
        });

        return $columns->keyBy('alias');
    }

    private function setSetups(): Collection
    {
        $setups = collect($this->setup());
        $setups = $setups->keyBy(function (mixed $item, int $key) {
            return get_class($item);
        });

        return $setups;
    }

    private function getColumn(?string $column, string $key = null): mixed
    {
        $key = $key ? $column . '.' . $key : $column;

        return data_get($this->columns, $key);
    }

    private function setOptions(): array
    {
        $this->orderColumn = $this->getColumn($this->setOrderColumn(), 'name');
        $this->orderDirection = $this->setOrderDirection();

        return [
            'template' => $this->template ?? $this->config('template'),
            'table_name' => $this->tableName,
            'table_id' => str($this->table)->replace('.', '-')->toString(),
            'data_source' => $this->getDataSource(),
            'skip_total' => $this->skipTotal,
            'deep_search' => $this->deepSearch ?? $this->config('deep_search'),
            'order_column' => $this->orderColumn,
            'order_direction' => $this->orderDirection,
            'per_page' => $this->setPerPage(),
            'filtered' => $this->getActiveFilterCount(),
            'auto_update_on_filter' => $this->autoUpdateOnFilter ?? $this->config('auto_update_on_filter'),
            'per_page_options' => $this->perPageOptions,
            'storage' => $this->storage ?? $this->config('storage'),
            'save_state' => $this->saveState ?? $this->config('save_state'),
            'save_state_filter' => $this->saveStateFilter ?? $this->config('save_state_filter'),
            'query_string_prefix' => $this->queryStringPrefix,
            'auto_update' => $this->autoUpdate,
            'auto_update_interval' => $this->autoUpdateInterval,
            'on_each_side' => $this->onEachSide ?? $this->config('on_each_side'),
            'search_placeholder' => $this->getSearchPlaceholder($this->searchPlaceholder),
            'request' => [
                'query' => request()->all(),
                'save' => $this->getSaveableRequest(),
                'map' =>  $this->getRequestMap()
            ],
            'data' => $this->data,
            'show_header' => $this->showHeader,
            'show_info' => $this->showInfo,
            'show_page_options' => $this->showPageOptions,
            'show_pagination' => $this->showPagination,
            'attributes' => $this->config('attributes')
        ];
    }

    public function getOption(string $key = null, mixed $default = null): mixed
    {
        return $key ? data_get($this->options, $key, $default) : $this->options;
    }

    public function getView(string $view): string
    {
        return 'datatable::' . $this->getOption('template', 'default') . '.' . $view;
    }

    private function getActiveFilterCount(): int
    {
        $filters = $this->request('filters') ?? [];
        $filters = array_map(function ($value) {
            return is_array($value) ? array_filter($value) : $value;
        }, $filters);

        return count(array_filter($filters, function ($value) {
            return !is_null($value);
        }));
    }

    private function setOrderColumn(): ?string
    {
        if (request()->has($this->getRequestMap('order_column'))) {
            return $this->request('order_column');
        }

        return $this->orderColumn;
    }

    private function setPerPage(): int
    {
        $perPage = is_numeric($this->request('per_page')) ? $this->request('per_page') : null;
        $perPage = array_key_exists($perPage, $this->perPageOptions) ? $perPage : null;

        return $perPage ?? $this->perPage ?? $this->config('per_page');
    }

    private function validateDirection(?string $direction): string
    {
        return match ($direction) {
            'asc' => 'asc',
            'desc' => 'desc',
            default => ''
        };
    }

    private function setOrderDirection(): string
    {
        $direction = $this->request('order_direction');

        if (!request()->has($this->getRequestMap('order_direction'))) {
            $direction = $this->orderDirection;
        }

        return $this->validateDirection($direction);
    }

    private function getSearchKeywords(): array
    {
        $keywords = $this->getOption('deep_search') 
            ? explode(' ', $this->request('search') ?? '') 
            : [$this->request('search')];
        $keywords = array_filter($keywords);

        if (count($keywords) > 1) {
            array_unshift($keywords, $this->request('search'));
        }

        return $keywords;
    }

    private function getDataSource(): string
    {
        if ($this->dataSource() instanceof Builder) {
            return 'eloquent';
        }

        if ($this->dataSource() instanceof QueryBuilder) {
            return 'queryBuilder';
        }
    }

    private function getSaveableRequest(): array
    {
        $filter = $this->saveStateFilter ?? $this->config('save_state_filter');
        
        $filtered = array_filter(request()->all(), function ($value, $key) use ($filter) {
            return !in_array(array_search($key, $this->getRequestMap()), $filter);
        }, ARRAY_FILTER_USE_BOTH);

        return collect($filtered)
            ->filter()
            ->mapWithKeys(function (mixed $item, mixed $key) {
                $flattened = [];

                if (is_array($item)) {
                    foreach ($item as $subKey => $subItem) {
                        $flattened["{$key}[{$subKey}]"] = $subItem;

                        if (is_array($subItem)) {
                            foreach ($subItem as $subItemKey => $subItemValue) {
                                $flattened["{$key}[{$subKey}][{$subItemKey}]"] = $subItemValue;
                            }
                            /*for ($i = 0; $i < count($subItem); $i++) { 
                                $flattened["{$key}[{$subKey}][{$i}]"] = $subItem[$i];
                            }*/
                        }
                    }
                }

                return [
                    $key => $item,
                    ...$flattened
                ];
            })
            ->filter(function (mixed $value, mixed $key) {
                return !is_array($value);
            })
            ->all();
    }

    private function getSearchableColumns(): Collection
    {
        return $this->columns->filter(function (mixed $value, string $key) {
                return $value['searchable'];
            });
    }

    private function getSortableColumns(): Collection
    {
        return $this->columns->filter(function (mixed $value, string $key) {
                return $value['sortable'];
            });
    }

    private function parseFilterableColumn(object $column): bool|object
    {
        if (is_callable($column->filterable)) {
            return call_user_func($column->filterable, new Filter, $column->name);
        }

        return false;
    }

    private function getFilterableColumns(): Collection
    {
        return $this->columns->filter(function (mixed $value, string $key) {
                return $value['filterable'];
            });
    }

    private function getSearchPlaceholder(?string $custom = null): ?string
    {
        $searchable = $this->getSearchableColumns()
            ->pluck('alias')
            ->map(function ($item, $key) {
                return str_replace('_', ' ', $item);
            })
            ->all();
  
        $total = count($searchable);
        $last = ($total <= 1) ?: array_pop($searchable);

        if ($total == 0) {
            return 'Search ' . $this->tableName . '...';
        }

        $last = $total > 1 ? ' or ' . $last . '...' : '...';
        $name = 'Search ' . $this->tableName . ' by ';

        return !is_null($custom) ? $custom : $name . implode(', ', $searchable) . $last;
    }

    private function queryBuilder(): Builder|QueryBuilder
    {
        $sortable = $this->getSortableColumns()
            ->keyBy('name')
            ->get($this->orderColumn);

        if (is_null($sortable) && $this->defaultOrderColumn) {
            $sortable = [
                'sortable' => [$this->defaultOrderColumn]
            ];
        }

        $requestFilters = $this->request('filters');

        return $this->dataSource()
            ->when($this->request('search'), function ($query) {
                $query->where(function ($query) {
                    foreach ($this->getSearchableColumns()->pluck('searchable')->flatten()->all() as $column) {
                        if (is_callable($column)) {
                            call_user_func($column, $query, $this->request('search'));
                        } else {
                            foreach ($this->getSearchKeywords() as $keyword) {
                                $query->orWhere($column, 'like', '%'. $keyword .'%');
                            }
                        }
                    }
                });
            })
            ->when($requestFilters, function ($query) use ($requestFilters) {
                foreach ($this->getFilterableColumns()->all() as $column) {
                    $value = data_get($requestFilters, $column['alias']);

                    if (!is_bool($column['filterable']) && !is_null($value)) {
                        $column['filterable']->getFilter($column['name'], $value, $query);
                    }
                }  
            })
            ->when($sortable && !empty($this->orderDirection), function ($query) use ($sortable) {
                if (is_callable($sortable['sortable'])) {
                    call_user_func($sortable['sortable'], $query, $this->orderDirection);
                } else if (is_array($sortable['sortable'])) {
                    foreach ($sortable['sortable'] as $column) {
                        $query->orderBy($column, $this->orderDirection);
                    }
                }
            });
    }

    private function transformer(Paginator $paginator): array
    {
        $data = [];

        for ($i = 0; $i < count($paginator->items()); $i++) { 
            $items = [];
            $index = ($paginator->firstItem() + $i);
            $model = $paginator[$i];

            foreach ($this->columns as $key => $column) {
                $value = data_get($model, $column['name']);

                if (is_callable($column['edit'])) {
                    $callbackValue = call_user_func($column['edit'], $value, $model, $index);
                    $items[$key]['value'] = $callbackValue instanceof View ? $callbackValue->render() : $callbackValue;
                } else {
                    $items[$key]['value'] = $column['index'] ? $index : $value;
                }

                if (is_callable($column['attributes'])) {
                    $items[$key]['attributes'] = call_user_func($column['attributes'], $value, $model, $index);
                } else {
                    $items[$key]['attributes'] = $column['attributes'];
                }
     
                if (is_callable($column['checkbox']['attributes'])) {
                    $items[$key]['checkbox']['attributes'] = call_user_func($column['checkbox']['attributes'], $value, $model, $index);
                } else {
                    $items[$key]['checkbox']['attributes'] = $column['checkbox']['attributes'];
                }

                $items[$key]['checkbox']['enabled'] = $column['checkbox']['enabled'];
                $items[$key]['responsive'] = $column['responsive'];
                $items[$key]['alignment'] = $column['alignment'];
            }

            $data[$index] = [
                'model' => $model,
                'columns' => $items
            ];
        }

        return $data;
    }

    public function formatAttributes(?array $attributes = null, string|array $mergeClass = null): string 
    {
        $attributes = $attributes ?: [];
        $mergeClass = is_array($mergeClass) ? implode(' ', array_filter($mergeClass)) : $mergeClass;
        $build = '';

        if ($mergeClass) {
            if (array_key_exists('class', $attributes)) {
                $attributes = collect($attributes)
                    ->map(function (string $value, string $key) use ($mergeClass) {
                        return $key === 'class' ? $value . ' ' . $mergeClass : $value;
                    })
                    ->all();
            } else {
                $attributes['class'] = $mergeClass;
            }
        }

        foreach ($attributes as $key => $value) {
            if ($value !== null) {
                $build .= $key . '="' . $value . '" ';
            }
        }

        return trim($build);
    }

    public function classAttributes(?array $classes = null): string 
    {
        $classes = $classes ?? [];
        $classes = array_filter($classes);
        $classes = count($classes) > 0 ? trim(implode(' ', $classes)) : null;

        return !is_null($classes) ? 'class="' . $classes . '"' : '';
    }

    public function tableAttributes(string $appendClasses = null): array
    {
        $tableSetup = data_get($this->setups, Table::class);

        if (is_array($tableSetup?->attributes)) {
            if (data_get($tableSetup->attributes, 'class') && $appendClasses) {
                return array_merge($tableSetup->attributes, [
                    'class' => $tableSetup->attributes['class'] . ' ' . $appendClasses
                ]);
            }

            return $tableSetup->attributes;
        }

        return $appendClasses ? ['class' => $appendClasses] : [];
    }

    public function rowAttributes(mixed $model, mixed $index): array
    {
        $rowSetup = data_get($this->setups, Row::class);

        if (is_callable($rowSetup?->attributes)) {
            return call_user_func($rowSetup->attributes, $model, $index);
        }

        return is_array($rowSetup?->attributes) ? $rowSetup->attributes : [];
    }

    public function getFilter(?string $column = null): array
    {
        $filterable = $this->getFilterableColumns();
        $filterable->transform(function (array $item, string $key) {
            $filter = $item['filterable'];
            
            return [
                'title' => $item['title'],
                'value' => data_get($this->request('filters'), $key, ''),
                'element' => is_object($filter) ? $filter->getElement() : null,
                'data' => is_object($filter) ? $filter->getDataSource() : null,
                'options' => is_object($filter) ? $filter->options : []
            ];
        });

        return $filterable->all();
    }

    public function getNextSortDirection(?string $direction, bool $ordered): string
    {
        if (!$ordered) {
            return 'asc';
        }

        return match ($direction) {
            '' => 'asc',
            'asc' => 'desc',
            'desc' => '',
            default => ''
        };
    }

    private function paginator(): Paginator
    {
        return $this->getOption('skip_total') 
            ? $this->queryBuilder()->simplePaginate(
                perPage: $this->getOption('per_page'), 
                pageName: $this->getOption('request.map.page')
            )
            : $this->queryBuilder()->paginate(
                perPage: $this->getOption('per_page'), 
                pageName: $this->getOption('request.map.page')
            );
    }

    public function hasRecords(Paginator $paginator): bool
    {
        return $paginator->isNotEmpty() 
            || ($paginator->isEmpty() && $this->request('search'))
            || ($paginator->isEmpty() && $this->request('filters'));
    }

    public function recordsNotFound(Paginator $paginator): bool
    {
        return ($paginator->isEmpty() && $this->request('search')) 
            || ($paginator->isEmpty() && $this->request('filters'));
    }

    private function outputData(): array
    {
        $paginator = $this->paginator();
        
        return [
            'data' => $this->transformer($paginator),
            'columns' => $this->columns->values()->all(),
            'filters' => $this->getFilter(),
            'options' => $this->options,
            'datatable' => $this,
            'paginator' => $paginator
        ];
    }

    public function id(bool $withHash = true): string
    {
        return $withHash ? '#' . $this->getOption('table_id') : $this->getOption('table_id');
    }

    public function render(): ?string
    {
        $build = '';
        $attributes = collect($this->getOption('attributes'));
        $attributes = $attributes->map(function (string $item, string $key) {
            return __($item, [
                'id' => $this->getOption('table_id'),
                'url' => route($this->config('route.name'), $this->table),
                'storage' => $this->getOption('storage'),
                'save_state' => $this->getOption('save_state') ? 'true' : 'false',
                'auto_update' => $this->getOption('auto_update') ? 'true' : 'false',
                'auto_update_on_filter' => $this->getOption('auto_update_on_filter') ? 'true' : 'false',
                'auto_update_interval' => $this->getOption('auto_update_interval'),
                'page' => $this->getOption('request.map.page'),
                'search' => $this->getOption('request.map.search'),
                'order_column' => $this->getOption('request.map.order_column'),
                'order_direction' => $this->getOption('request.map.order_direction'),
                'per_page' => $this->getOption('request.map.per_page'),
                'filters' => $this->getOption('request.map.filters')
            ]);
        });

        foreach ($attributes as $key => $value) {
            $build .= $key . '="' . $value . '" ';
        }

        return trim($build);
    }

    public function api(): array
    {
        $data = $this->outputData();

        return [
            'has_records' => $this->hasRecords($data['paginator']),
            'not_found' => $this->recordsNotFound($data['paginator']),
            'options' => $this->getOption(),
            'html' => [
                'table' =>  view($this->getView('table'), $data)->render(),
                'info' => view($this->getView('info'), $data)->render(),
                'pagination' => view($this->getView('pagination'), $data)->render(),
                'length' => view($this->getView('length'), $data)->render(),
                'search' => view($this->getView('search'), $data)->render(),
                'filter' => view($this->getView('filter'), $data)->render()
            ]
        ];
    }

    public function getNotFoundView()
    {
        $notFound = $this->notFound($this->request('search'));

        return $notFound instanceof View ? $notFound->render() : $notFound;
    }

    public function getEmptyStateView()
    {
        $emptyState = $this->emptyState();

        return $emptyState instanceof View ? $emptyState->render() : $emptyState;
    }

    protected function setup(): array
    {
        return [];
    }

    protected function columns(): array
    {
        return [];
    }

    protected function dataSource(): Builder|QueryBuilder
    {
        return Model::query();
    }

    protected function emptyState(): string|View
    {
        return view($this->getView('empty'));
    }

    protected function notFound(?string $keyword): string|View
    {
        return view($this->getView('not-found'), [
            'keyword' => $keyword,
            'table' => $this->tableName
        ]);
    }
}