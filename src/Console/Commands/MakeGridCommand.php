<?php

namespace TheNandan\Grids\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;

class MakeGridCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:grid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new grid class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Grids';

    /**
     * @return bool|void|null
     *
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $this->type = Str::studly(class_basename($this->argument('name')));
        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/grid.stub';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace', 'GridClass'],
            [$this->getNamespace($name), $this->getGridClass()],
            $stub
        );
        return $this;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Grids';
    }

    /**
     * Get the name of the repository class.
     *
     * @return string
     */
    protected function getGridClass()
    {
        return Str::studly(class_basename($this->argument('name')));
    }
}
