<?php

namespace VariableSign\DataTable;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\View\View;
use Illuminate\Database\Query\Builder as QueryBuilder;

abstract class DataTable
{
    private Collection $columns;

    private array $options;

    protected ?string $orderColumn = null;

    protected string $orderDirection = 'asc';

    protected ?int $perPage = null;

    protected ?array $perPageOptions = null;

    protected ?int $onEachSide = null;

    protected bool $skipTotal = false;

    protected bool $deepSearch = false;

    protected string $tableName = 'datatable';

    protected ?string $template = null;

    protected bool $showHeader = true;

    protected bool $showPageOptions = true;

    protected bool $showInfo = true;

    protected bool $showPagination = true;

    protected ?string $searchPlaceholder = null;

    public function __construct()
    {
        $this->perPageOptions = $this->perPageOptions ?? $this->config('per_page_options');
        $this->columns = $this->setColumns();
        $this->options = $this->setOptions();
    }

    private function config(?string $key = null, mixed $default = null): mixed
    {
        $key = $key ? 'datatable.' . $key : 'datatable';

        return config($key, $default);
    }

    public function request(string $key): ?string
    {
        $request = data_get($this->config('request_map'), $key);
        $request = e(strip_tags(request($request, '')));

        return empty($request) ? null : $request;
    }

    private function setColumns(): Collection
    {
        $columns = collect($this->columns());
        $columns = $columns->transform(function (object $item, int $key) {
            return [
                'name' => $item->name,
                'alias' => $item->alias,
                'title' => $item->title,
                'searchable' => $item->searchable,
                'sortable' => $item->sortable,
                'ordered' => $item->sortable && $this->setOrderColumn() === $item->alias ? true : false,
                'direction' => $this->setOrderColumn() === $item->alias ? $this->setOrderDirection() : 'asc',
                'edit' => $item->edit,
                'index' => $item->index,
                'checkbox' => [
                    'enabled' => $item->checkbox,
                    'attributes' => $item->checkboxAttributes
                ],
                'attributes' => $item->attributes,
                'responsive' => $this->config('breakpoints.' . $item->responsive)
            ];
        });

        return $columns->keyBy('alias');
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
            'table_id' => str($this->tableName)->slug()->toString() . '-table',
            'data_source' => $this->getDataSource(),
            'skip_total' => $this->skipTotal,
            'deep_search' => $this->deepSearch,
            'order_column' => $this->orderColumn,
            'order_direction' => $this->orderDirection,
            'per_page' => $this->setPerPage(),
            'per_page_options' => $this->perPageOptions,
            'on_each_side' => $this->onEachSide ?? $this->config('on_each_side'),
            'search_placeholder' => $this->getSearchPlaceholder($this->searchPlaceholder),
            'request' => [
                'query' => request()->all(),
                'map' =>  $this->config('request_map')
            ],
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

    private function setOrderColumn(): ?string
    {
        return $this->request('order_column') ?? $this->orderColumn;
    }

    private function setPerPage(): int
    {
        $perPage = is_numeric($this->request('per_page')) ? $this->request('per_page') : null;
        $perPage = array_key_exists($perPage, $this->perPageOptions) ? $perPage : null;

        return $perPage ?? $this->perPage ?? $this->config('per_page');
    }

    private function validateDirection(string $direction): string
    {
        return match ($direction) {
            'asc' => 'asc',
            'desc' => 'desc',
            default => 'asc'
        };
    }

    private function setOrderDirection(): string
    {
        $direction = $this->request('order_direction') ?? $this->orderDirection;

        return $this->validateDirection($direction);
    }

    private function getSearchKeywords(): array
    {
        $keywords = $this->deepSearch 
            ? explode(' ', $this->request('search') ?? '') 
            : [$this->request('search')];
        $keywords = array_filter($keywords);

        if (count($keywords) > 1) {
            array_unshift($keywords, $this->request('search'));
        }

        return $keywords;
    }

    private function getDataSource(): ?string
    {
        if ($this->dataSource() instanceof Builder) {
            return 'eloquent';
        }

        if ($this->dataSource() instanceof QueryBuilder) {
            return 'queryBuilder';
        }

        return null;
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
        $sortable = $this->getSortableColumns()->get($this->orderColumn);

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
            ->when($sortable, function ($query) use ($sortable) {
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
            }

            $data[$index] = [
                'model' => $model,
                'columns' => $items
            ];
        }

        return $data;
    }

    public function formatAttributes(?array $attributes = null, string $mergeClass = null): string 
    {
        $attributes = $attributes ?: [];
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

    public function rowAttributes(mixed $model, mixed $index): array
    {
        $attributes = [];

        if (is_callable([$this, 'rows'])) {
            $attributes = call_user_func([$this, 'rows'], $model, $index);
        }

        return $attributes;
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
        return $paginator->isNotEmpty() || ($paginator->isEmpty() && $this->request('search'));
    }

    public function recordsNotFound(Paginator $paginator): bool
    {
        return $paginator->isEmpty() && $this->request('search');
    }

    private function data(): array
    {
        $paginator = $this->paginator();

        return [
            'data' => $this->transformer($paginator),
            'columns' => $this->columns->values()->all(),
            'options' => $this->options,
            'datatable' => $this,
            'paginator' => $paginator
        ];
    }

    private function getRouteParameter(): string 
    {
        $class = get_called_class();
        $class = str($class)->after($this->config('directory') . '\\');
        $parts = explode('\\', $class);
        $total = count($parts);
        $name = $total > 1 ? array_pop($parts) : $class;
        $name = str($name)->kebab();
        $parts = array_map(function ($item) {
            return strtolower($item);
        }, $parts);
        $path = implode('.', $parts);

        return $total > 1 ? $path . '.' . $name : $name;
    }

    public function render(): ?string
    {
        $build = '';
        $attributes = collect($this->getOption('attributes'));
        $attributes = $attributes->map(function (string $item, string $key) {
            return __($item, [
                'id' => $this->getOption('table_id'),
                'url' => route($this->config('route.name'), $this->getRouteParameter()),
                'push_state' => $this->config('push_state') ? 'true' : 'false'
            ]);
        });

        foreach ($attributes as $key => $value) {
            $build .= $key . '="' . $value . '" ';
        }

        return trim($build);
    }

    public function api(): array
    {
        $data = $this->data();

        return [
            'has_records' => $this->hasRecords($data['paginator']),
            'not_found' => $this->recordsNotFound($data['paginator']),
            'options' => $this->getOption(),
            'html' => [
                'table' =>  view($this->getView('table'), $data)->render(),
                'info' => view($this->getView('info'), $data)->render(),
                'pagination' => view($this->getView('pagination'), $data)->render(),
                'length' => view($this->getView('length'), $data)->render(),
                'search' => view($this->getView('search'), $data)->render()
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

    protected function columns(): array
    {
        return [];
    }

    protected function dataSource(): mixed
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