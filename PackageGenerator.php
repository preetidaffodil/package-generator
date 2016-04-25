<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use File;

class PackageGenerator extends GeneratorCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:package';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a Complete Package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $type = 'Package';

    /**
     * 
     */
    public function getStub() {
        return '';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $path = base_path();
        $vendorName = $this->ask('Name of the vendor? ');
        $packageName = $this->ask('Name of the package?');
        File::makeDirectory($path .= '/packages/' . $vendorName .'/'. $packageName, 0777, true);
        $composer = $this->files->get(__DIR__ . '/stubs/composer.stub');
        $name = $vendorName .'/'. $packageName;
        
        $composer = str_replace(
        '{{name}}', $name, $composer
        );
        File::put($path . '/composer.json', $composer);
        File::makeDirectory($path .= '/src', 0777);
        File::makeDirectory($path . '/Events', 0777);
        File::makeDirectory($path . '/Interfaces', 0777);
        File::makeDirectory($path . '/Providers', 0777);
        File::makeDirectory($path . '/Repositories', 0777);
        File::makeDirectory($path . '/database', 0777);
        File::makeDirectory($path .= '/resources', 0777);
        File::makeDirectory($path . '/assets', 0777);
        File::makeDirectory($path . '/views', 0777);
        File::makeDirectory($path .= '/Http', 0777);
        File::makeDirectory($path . '/Controllers', 0777);
        File::makeDirectory($path . '/Middlewares', 0777);
        File::makeDirectory($path . '/Requests', 0777);
        File::makeDirectory($path . 'routes.php', 0777);

        $nameSpaceArr = array();
        $nameSpace = 'packages\\' . ucfirst($name) . '\Controllers';
        $this->info("$name package craeted successfully");
        do {

            $source = $this->choice(
                    'Select any one would you like to create?', ['1' => 'Create Controllers',
                '2' => 'Create Models',
                '3' => 'Create Resources',
                '4' => 'Create Requests',
                '5' => 'Create Routes',
                '6' => 'Create Service Providers',
                '7' => 'Create Repositories',
                '8' => 'Create Database',
                '9' => 'Create Middlewares',
                '10' => 'Create Interfaces',
                '11' => 'Create Event',
                '0' => 'Exit',
                'S' => 'Save']
            );
            switch ($source) {
            case 1:
                $controllername = $this->ask("Name of the Controller?");
               $stub = $this->files->get(__DIR__ . '/stubs/controller.stub');
               $stub = str_replace(
                '{{dummynamespace}}', $nameSpace, $stub
            );
            $stub = str_replace(
                '{{dummycontrollername}}', $controllername, $stub
            );
            $nameSpaceArr[strtolower(str_replace('Controller', '', $controllername))] = $controllername;
            File::put($path . '/controllers/' . $controllername . '.php', $stub);
            
           
            default:
                echo 'exit';
                
        }
            $this->info("$source created successfully");
        } while ($this->confirm('Do you wish add another choice? [y|n]'));
        {
            $this->info("Exit successfully");
        }
        
    }

}
