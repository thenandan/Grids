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
            [$this->getNamespace($name), $this->getRepositoryClass()],
            $stub
        );
        return $this;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\').'\Grids';
    }

    /**
     * Get the name of the repository class.
     *
     * @return string
     */
    protected function getRepositoryClass()
    {
        return Str::studly(class_basename($this->argument('name')));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/Grids/'.str_replace('\\', '/', $name).'.php';
    }
}
