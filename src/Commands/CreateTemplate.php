<?php

namespace Cyclops1101\PageObjectManager\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

class CreateTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:template {name? : The name of the template} {--block}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new template';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = ucwords($this->getNameArgument());
        $path = $this->getPath($name);

        // Make directory if it does not exist
        $this->makeDirectory($path);

        // Write file
        if ($this->files->exists($path)) {
            $this->info($path . ' template already exists');
        } else {
            $this->files->put($path, $this->buildClass($name));
            $this->info('Created ' . $path);
        }
//        $this->register();
    }

    public function getNameArgument()
    {
        if (!$this->argument('name')) {
            return $this->ask('Please provide a name for your template');
        }

        return $this->argument('name');
    }

    public function getPath($name)
    {
        return app_path('Nova/Templates/' . $name . '.php');
    }

    public function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    public function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        if ($this->option('block')) {
            $stub = str_replace("'page'", "'block'", $stub);
        }
        return str_replace('DummyTemplate', $name, $stub);
    }

    public function getStub()
    {
        return __DIR__ . '/../Stubs/Template.php';
    }

    //TODO: figure out how to write to the config file
//    public function register()
//    {
//        $page = $this->toHuman($this->argument('name'));
//        $pages = config($this->configKey());
//        $pages = array_merge($pages, [$page => "App\\Nova\\Template\\{$this->argument("name")}"]);
//        $this->info(print_r($pages));
//
//        Config::write($this->configKey(), implode(",", $pages));
//        $this->info(print_r(config($this->configKey())));
//    }
//
//    public function configKey()
//    {
//        return "page-object-manager." . ($this->option('block') ? 'blocks' : 'pages');
//    }
//
//    public function toHuman($string)
//    {
//        return trim(preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $string));
//    }

}
