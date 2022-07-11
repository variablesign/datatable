<?php

namespace VariableSign\Datatable\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DatatableMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:datatable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new datatable class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Datatable';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/datatable.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../..'.$stub;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        if (str_ends_with($name, $this->type)) {
            $class = class_basename(Str::ucfirst($name));
        } else {
            $class = class_basename(Str::ucfirst($name.$this->type));
        }

        $namespaceModel = $this->option('model')
                        ? $this->qualifyModel($this->option('model'))
                        : $this->qualifyModel($this->guessModelName($name));

        $model = class_basename($namespaceModel);

        $modelClass = '\\' . $namespaceModel;

        $defaultColumn = class_exists($modelClass) ? (new $modelClass)->{'getKeyName'}() : 'id';

        $namespace = $this->getNamespace(
            Str::replaceFirst($this->rootNamespace(), 'App\\Datatables\\', $this->qualifyClass($this->getNameInput()))
        );

        $splitModelName = implode(' ', Str::ucsplit($model));
        $modelLowerPlural = Str::plural(Str::lower($splitModelName));

        $replace = [
            '{{ datatableNamespace }}' => $namespace,
            '{{datatableNamespace}}' => $namespace,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ modelLowerPlural }}' => $modelLowerPlural,
            '{{modelLowerPlural}}' => $modelLowerPlural,
            '{{ defaultColumn }}' => $defaultColumn,
            '{{defaultColumn}}' => $defaultColumn,
            '{{ class }}' => $class,
            '{{class}}' => $class,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '')->finish($this->type);

        return $this->laravel->basePath().'/app/Datatables/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Guess the model name from the Factory name or return a default model name.
     *
     * @param  string  $name
     * @return string
     */
    protected function guessModelName($name)
    {
        if (str_ends_with($name, $this->type)) {
            $name = substr($name, 0, -9);
        }

        $modelName = $this->qualifyModel(Str::after($name, $this->rootNamespace()));
        
        if (Str::contains($name, '\\')) {
            $modelName = $this->qualifyModel(Str::afterLast($name, '\\'));
        }

        if (class_exists($modelName)) {
            return $modelName;
        }

        if (is_dir(app_path('Models/'))) {
            return $this->rootNamespace().'Models\Model';
        }

        return $this->rootNamespace().'Model';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model.']
        ];
    }
}
