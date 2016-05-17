<?php namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use File;
use App;



class PackageGenerator extends GeneratorCommand 
{

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
     * Get the stub file for the generator.
     *
     * @return string
     * @author Preeti 04/05/2016
     */
    public function getStub() 
    {
        return '';
    }

    protected $nameSpaceArr = array();
    protected $vendorName;
    protected $packageName;
    protected $vendorPath = array();
    protected $path;
    protected $function = null;
    protected $arrController = array();
    protected $arrModel = array();
    protected $arrView = array();
    protected $arrFunction = array();
    protected $arrRequest = array();
    protected $arrProvider = array();
    protected $arrMiddleware = array();
    protected $arrEvent = array();
    protected $arrRepo = array();
    protected $arrInterface = array();
    

    /**
     * Execute the console command.
     *
     * @return mixed
     * @author Preeti 04/05/2016
     */
    public function handle() 
    {
        // Get base path
        $path = base_path();
        //Ask vendor and package name
        $vendorName = $this->ask("Name of vendor?");
        $packageName = $this->ask("Name of package?");  
        if (File::exists($path . '/packages/' . $vendorName . '/' . $packageName)) {
            $this->error('Package name  already exists');            
           exit;
        } 
        // Prepare array
        $this->vendorPath = [
            'vendor' => $vendorName,
            'package' => $packageName
        ];
        $arr = array();
        $fun = array();
        
        // Input choices 
        $count = 1;
        do {
            // Define choice
            $source = $this->choice(
                    'Select any one would you like to create?', [
                '1' => 'Create Controllers',
                '2' => 'Model',
                '3' => 'Create View',
                '4' => 'Create Requests',
                '5' => 'Create Service Providers',
                '6' => 'Create Middlewares',
                '7' => 'Create Repositories',
                '8' => 'Create Interfaces',
                '9' => 'Create Event',
                'S' => 'Save',
                'All' => 'All',
                '0' => 'Exit'
                    ]
            );
            $count++;
            // Check condition and prepare array according to choice
            switch ($source) {              
                case 1:
                    //Case for create controller
                    $functionname;
                    $controller = $this->ask('Name of Controller?');
                    // Ask to create routing
                    $routing = $this->choice(
                            'are you want to create routing?', [
                        '1' => 'Yes',
                        '2' => 'No'
                            ]
                    );
                    if ($routing == 'Yes') {
                        $routename = $this->ask('Enter the name of route prefix?');
                    } else {
                        $routename = null;
                    }
                    // Option for create which type of controller
                    $function = $this->choice('Select any one would you like to create?', [
                        '1' => 'Blank Controller',
                        '2' => 'All Function',
                        '3' => 'Self Define function'
                            ]
                    );
                    if ($function == 'Self Define function') {
                       $functions =  $this->ask('write function in comma seperated form');
                       $functionname = explode(',',$functions);
                       
                       
                    }
                    else{
                        $functionname = null;
                    }
                    $this->arrController[] = array('controller' => $controller,
                        'route' => $routing,
                        'routeName' => $routename,
                        'option' => $function,
                        'function' => $functionname
                    );
                    break;
                case 2:
                    // Case for create model
                    $model = $this->ask('Name of Model?');
                    $this->arrModel[] = array('model' => $model);
                    break;
                case 3:
                    // Case for create view
                    $view = $this->choice('Which view you want to create?', [
                        '1' => 'create',
                        '2' => 'index'
                    ]);
                    $this->arrView[] = array('view' => $view);
                    break;
                case 4:
                    // Case for create request
                    $requestName = $this->ask('Name of Request?');
                    $this->arrRequest[] = $requestName;
                    break;
                // Case for create service provider
                case 5:
                    $serviceProvider = $this->ask('Name of service provider?');
                    $this->arrProvider[] = array('provider' => $serviceProvider);
                    break;
                case 6:
                    // Case for create middleware
                    $middleware = $this->ask('name of middleware?');
                    $this->arrMiddleware[] = array('middleware' => $middleware);
                    break;
                case 7:
                    // Case for create repository
                    $repo = $this->ask('name of Repository?');
                    $this->arrRepo[] = array('repo' => $repo);
                    break;
                case 8:
                    // Case for create interface
                    $interface = $this->ask('name of Interface?');
                    $this->arrInterface[] = array('interface' => $interface);
                    break;
                case 9:
                    // Case for create event
                    $event = $this->ask('name of event?');
                    $this->arrEvent[] = array('event' => $event);
                    break;
                case 'S':
                    // Case for save all choices
                    S:                        
                    $this->output->progressStart($count);
                    for ($count = 1; $count < 9; $count++) {
                    sleep(1);
                    $this->output->progressAdvance();
                     }
                    $this->output->progressFinish();      
                    File::makeDirectory($path .= '/packages/' . $vendorName . '/' . $packageName, 0777, true);       
                    
                    $composer = $this->files->get(__DIR__ . '/stubs/composer.stub');
                    $vendorPath = $vendorName . '/' . $packageName;                    
                    $composer = str_replace(
                            '{{name}}', $vendorPath, $composer
                    );
                    // Make composer file
                    File::put($path . '/composer.json', $composer);
                    // Create directory 
                    File::makeDirectory($path .= '/src', 0777);  
                    // Call save function
                    $this->save($vendorPath);  
                    $this->info('After run this command you will keep entry on composer.json file into psr array' 
                            . 'on root directory in following format' 
                            . '{{vendorname\\packagename\\: packages/vendorname/packagename/src}}' 
                            .'and register service provider in app.php file into provider array in following ' 
                            . 'format{{vendorname\packagename\serviceprovidername::class}}' 
                            . 'after completed all entries you will must run composer update command '
                            );
                    exit;
                    break;
                // Case for all choices
                case 'All':
                    $all = $this->ask('Are you want to save all option');
                    if ($all == 'y') {
                        $this->info('All structure save successfully');
                        goto S;
                    } else {
                        $this->info('exit successfully');
                        exit;
                    }
                    break;
                // Case for exit from switch
                case 0:
                    $exit = $this->confirm('Do you wish to exit without save any changes? [y|n]');
                    if ($exit == 'true') {
                        $this->info('exit successfully without saved');
                        exit;
                    } else {
                        $this->info('Save successfully');
                        goto S;                        
                    }
                    break;
                // Default case
                default:
                    echo 'No one conditon match to your input';
            }
        } while ($this->confirm('Do you wish add another choice? [y|n]')); {
            
            $this->info("Exit successfully");
        }        
    }

    /**
     * calling  function
     * 
     * @param string $vendorPath
     * @return file
     * @author Preeti 04/05/2016
     */
    public function save($vendorPath) 
    {        
        // Get base path
        $path = base_path();
        // Make directory 
        File::makeDirectory($path . '/packages/' . $vendorPath .='/src/http', 0777, true);
        $filePath = $path . '/packages/' . $vendorPath . '/';
        // Calling function
        $this->createController($filePath);
        $this->createModel($filePath);
        $this->createView($filePath);
        $this->createRequest();
        $this->createProvider();
        $this->createMiddleware();
        $this->createEvent();
        $this->createRepository();
        $this->createInterface();
    }

    /**
     * Function for create controller
     * 
     * @param string $filePath
     * @return file
     * @author Preeti 04/05/2016
     */
    public function createController($filePath) 
    {
        //Set namespace for controller
        $nameSpace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) .'\Src' . '\Http' . '\Controller';
        $nameSpace = str_replace(' ', '', $nameSpace);
        // Set route path
        $pathForRoute = base_path() . '/packages/' . $this->vendorPath['vendor'] . '/' . $this->vendorPath['package'] . '/src/http';
        // Set controller path
        $pathForController = base_path() . '/packages/' . $this->vendorPath['vendor'] . '/' . $this->vendorPath['package'] . '/src/http/controller';
        // Make directory for controller
        File::makeDirectory($filePath .= 'controller', 0777, true);        
        foreach ($this->arrController as $value) {
            $val = $value['option'];
            $controller = ucfirst($value['controller']);
            switch ($val) {
                //If user select blank controller option
                case "Blank Controller" :
                    // Get stub file
                    $stubCntrl = $this->files->get(__DIR__ . '/stubs/blankController.stub');
                    // Set namespace 
                    $stubCntrl = str_replace(
                            '{{dummynamespace}}', $nameSpace, $stubCntrl
                    );
                    // Set controller name
                    $stubCntrl = str_replace(
                            '{{dummycontrollername}}', $controller, $stubCntrl
                    );
                    // Create controller file 
                    File::put($pathForController . '/' . $controller . '.php', $stubCntrl);
                    break;
                // If user choose all function
                case "All Function" :
                    // Get stub file
                    $stubCntrl = $this->files->get(__DIR__ . '/stubs/controller.stub');
                    // Set namespace
                    $stubCntrl = str_replace(
                            '{{dummynamespace}}', $nameSpace, $stubCntrl
                    );
                    // Set controller name
                    $stubCntrl = str_replace(
                            '{{dummycontrollername}}', $controller, $stubCntrl
                    );                   
                    // Create controller file
                    File::put($pathForController . '/' . $controller . '.php', $stubCntrl);
                    break;
                // Case for user want to create self define function
                case "Self Define function" :                     
                        foreach ($value['function'] as $funName) {     
                        // Check file exist or not
                        if (File::exists($pathForController . '/' . $value['controller'] . '.php')) {
                            // Get stub file
                            $funStub = $this->files->get(__DIR__ . '/stubs/function.stub');
                            $funName = str_replace(' ', '', $funName);
                            $funStub = str_replace(
                                    '{{dummyfunction}}', $funName, $funStub
                            );
                            // Append value if file exists
                            File::append($pathForController . '/' . $value['controller'] . '.php', "\n" . $funStub);
                        } else {
                            $customStub = $this->files->get(__DIR__ . '/stubs/customFunction.stub');
                            $customStub = str_replace(
                                    '{{dummynamespace}}', $nameSpace, $customStub
                            );
                            $customStub = str_replace(
                                    '{{dummycontrollername}}', ucfirst($value['controller']), $customStub
                            );
                            $customStub = str_replace(
                                    '{{dummyfunctionName}}', $funName, $customStub
                            );
                            // Create controller file
                            File::put($pathForController . '/' . $value['controller'] . '.php', $customStub);
                        }
                    }
                    // Append value into that file
                    File::append($pathForController . '/' . $value['controller'] . '.php', "\n" . '}');
                    break;
                default :                     
                    echo "wrong input";
                    break;
            }
            // Condition for route
            if ($value['route'] == 'Yes') {               
                // Get stub file
                $routenamespace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) .'\Src' . '\Http' . '\Controller'.' \ '.ucfirst($value['controller']);
                 $routenamespace = str_replace(' ', '', $routenamespace);
                $stub = $this->files->get(__DIR__ . '/stubs/routes.stub');
                $stub = str_replace(
                        '{{routename}}', $value['routeName'], $stub
                );
                $stub = str_replace(
                        '{{dummycontrollername}}', $routenamespace, $stub
                );
                // Check file exists
                if (File::exists($pathForRoute . '/routes.php')) {
                    $stub = str_replace(
                            '<?php', '', $stub
                    );
                    // Append route into existing route file
                    File::append($pathForRoute . '/routes.php', $stub);
                } else {
                    // Create route file
                    File::put($pathForRoute . '/routes.php', $stub);
                }
            }
        }
    }

    /**
     * Function for create model
     * 
     * @param string $filePath
     * @return file
     * @author Preeti 04/05/2016
     */
    public function createModel($filePath) 
    {
        // Define model namespace
        $modelnameSpace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) .'\Src'. '\Http' . '\Model';
        $modelnameSpace = str_replace(' ', '', $modelnameSpace);
        // Create directory for model
        File::makeDirectory($filePath .= 'model', 0777, true);
        foreach ($this->arrModel as $value) {
            $stub = $this->files->get(__DIR__ . '/stubs/model.stub');
            $stub = str_replace(
                    '{{dummynamespace}}', $modelnameSpace, $stub
            );
            $stub = str_replace(
                    '{{dummymodelname}}', ucfirst($value['model']), $stub
            );
            $nameSpaceArr[strtolower(str_replace('model', '', $value['model']))] = $value['model'];
            // Create model file
            File::put($filePath . '/' . ucfirst($value['model']) . '.php', $stub);
        }
    }

    /**
     * Function to create view
     * 
     * @param string $filePath
     * @return file
     * @author Preeti 04/05/2016
     */
    public function createView($filePath) 
    {
        // Create directory for view
        File::makeDirectory($filePath .= 'Views', 0777, true);
        foreach ($this->arrView as $value) {
            $value = $value['view'];
            switch ($value) {
                case "create" :
                    File::put($filePath . '/create.blade.php', $this->files->get(__DIR__ . '/stubs/create.stub'));
                    break;
                case "index" :
                    File::put($filePath . '/index.blade.php', $this->files->get(__DIR__ . '/stubs/listing.stub'));
                    break;
                default :
                    echo 'you have entered wrong input';
                    break;
            }
        }
    }

    /**
     * Function for  create request
     * 
     * @return file
     * @author Preeti 04/05/2016
     */
    public function createRequest() 
    {
        // Define path for request
        $pathForRequest = base_path() . '/packages/' . $this->vendorPath['vendor'] . '/' . $this->vendorPath['package'] . '/src';
        $nameSpace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) . '\Src' . '\Requests';
        $nameSpace = str_replace(' ', '', $nameSpace);
        // Create directory for request
        File::makeDirectory($pathForRequest .= '/requests', 0777, true);
        // Get stub file
        $requestStub = $this->files->get(__DIR__ . '/stubs/request.stub');
        foreach ($this->arrRequest as $requestName) {
            $name = str_replace(' ', '', $requestName);
            $finalStub = str_replace(
                    '{{requestnamespace}}', $nameSpace, $requestStub
            );
            $finalStub = str_replace(
                    '{{requestname}}', ucwords($name), $finalStub
            );
            // Create request file
            File::put($pathForRequest . '/' . ucwords($name) . '.php', $finalStub);
        }
    }

    /**
     * Function for create provider
     * 
     * @return file
     * @author Preeti 04/05/2016
     */
    public function createProvider() 
    {
        $pathForapp = base_path() . '/config/app.php';
        $pathForProvider = base_path() . '/packages/' . $this->vendorPath['vendor'] . '/' . $this->vendorPath['package'].'/src';
        $providernameSpace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']);
        $providernameSpace = str_replace(' ', '', $providernameSpace);         
        foreach ($this->arrProvider as $provider) {
            $providers = $providernameSpace . '\ ' . ucwords($provider['provider']) . '::class';
            $providers = str_replace(' ', '', $providers);
            $stubProvider = $this->files->get(__DIR__ . '/stubs/provider.stub');
            $stubProvider = str_replace(
                    '{{dummynamespace}}', $providernameSpace, $stubProvider
            );
            $stubProvider = str_replace(
                    '{{dummyprovidername}}', ucwords($provider['provider']), $stubProvider
            );
            
            // Create file for providers
            File::put($pathForProvider . '/' . ucfirst($provider['provider']) . '.php', $stubProvider);
            
        }
    }

    /**
     * Function for create middleware
     * 
     * @return file
     * @author Preeti 05/05/2016
     */
    public function createMiddleware()
    {
        $pathForMiddleware = base_path() . '/packages/' . $this->vendorPath['vendor'] . '/' . $this->vendorPath['package'] . '/src';
        $middlewarenameSpace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) . '\Src' . '\Middleware';
        $middlewarenameSpace = str_replace(' ', '', $middlewarenameSpace);
        // Craete directory for middleware
        File::makeDirectory($pathForMiddleware .= '/middlewares', 0777, true);
        foreach ($this->arrMiddleware as $middleware) {
            // Get stub file
            $stubMiddleware = $this->files->get(__DIR__ . '/stubs/middleware.stub');
            // Set namespace
            $stubMiddleware = str_replace(
                    '{{dummynamespace}}', $middlewarenameSpace, $stubMiddleware
            );
            $stubMiddleware = str_replace(
                    '{{dummyclassname}}', ucfirst($middleware['middleware']), $stubMiddleware
            );
            // Create middleware file 
            File::put($pathForMiddleware . '/' . ucwords($middleware['middleware']) . '.php', $stubMiddleware);
        }
    }

    /**
     * Function for create event
     * 
     * @return file
     * @author Preeti 05/05/2016
     */
    public function createEvent() 
    {
        $pathForEvent = base_path() . '/packages/' . $this->vendorPath['vendor'] . '/' . $this->vendorPath['package'] . '/src';
        $eventnameSpace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) . '\Src' . '\Event';
        $eventnameSpace = str_replace(' ', '', $eventnameSpace);
        // Create directory for event
        File::makeDirectory($pathForEvent .= '/events', 0777, true);
        foreach ($this->arrEvent as $event) {
            $stubEvent = $this->files->get(__DIR__ . '/stubs/event.stub');

            $stubEvent = str_replace(
                    '{{dummynamespace}}', $eventnameSpace, $stubEvent
            );
            $stubEvent = str_replace(
                    '{{dummyclassname}}', ucfirst($event['event']), $stubEvent
            );
            // Create event file
            File::put($pathForEvent . '/' . ucwords($event['event']) . '.php', $stubEvent);
        }
    }

    /**
     * Funcgtion for create repository
     * 
     * @return file
     * @author Preeti 05/05/2016
     */
    public function createRepository() 
    {
        $pathForRepo = base_path() . '/packages/' . $this->vendorPath['vendor'] . '/' . $this->vendorPath['package'] . '/src';
        $reponameSpace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) . '\Src' . '\Repository';
        ;
        $reponameSpace = str_replace(' ', '', $reponameSpace);
        $repoInterface = 'Packages\\' . ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) . '\Http' . '\Interface';
        ;
        $repoInterface = str_replace(' ', '', $repoInterface);
        // Create directory for repository
        File::makeDirectory($pathForRepo .= '/repositories', 0777, true);
        foreach ($this->arrRepo as $repo) {
            $stubRepo = $this->files->get(__DIR__ . '/stubs/repository.stub');

            $stubRepo = str_replace(
                    '{{dummynamespace}}', $reponameSpace, $stubRepo
            );
            $stubRepo = str_replace(
                    '{{dummyintrface}}', $repoInterface, $stubRepo
            );
            $stubRepo = str_replace(
                    '{{dummyclassname}}', ucfirst($repo['repo']), $stubRepo
            );
            // Create repositroy file
            File::put($pathForRepo . '/' . ucwords($repo['repo']) . '.php', $stubRepo);
        }
    }

    /**
     * Function for  create interface
     * 
     * @return file
     * @author Preeti 05/05/2016
     */
    public function createInterface() 
    {
        $pathForInterface = base_path() . '/packages/' . $this->vendorPath['vendor'] . '/' . $this->vendorPath['package'] . '/src';
        $interfacenameSpace = ucfirst($this->vendorPath['vendor']) . ' \ ' . ucfirst($this->vendorPath['package']) . '\Src' . '\Interface';
        $interfacenameSpace = str_replace(' ', '', $interfacenameSpace);
        // Create directory for interface
        File::makeDirectory($pathForInterface .= '/interface', 0777, true);
        foreach ($this->arrInterface as $interface) {
            // Get stub file
            $stubInterface = $this->files->get(__DIR__ . '/stubs/interface.stub');

            $stubInterface = str_replace(
                    '{{dummynamespace}}', $interfacenameSpace, $stubInterface
            );
            $stubInterface = str_replace(
                    '{{dummyclassname}}', ucfirst($interface['interface']), $stubInterface
            );
            // Create interface file
            File::put($pathForInterface . '/' . ucwords($interface['interface']) . '.php', $stubInterface);
        }
    }
   
}
