<?php
namespace Lib;

use Bootstrap\Bootstrap;
/**
 * Description of Response
 *
 * @author Himel
 */
class Response {
    
    protected $config;
    protected $layout;
    protected $request;

    
    protected static $masterView;
    protected static $masterViewModel;
    protected static $view;
    protected static $viewModel;
    protected static $jsonData;
    
    public function __construct(Request $req) {
        $this->layout = new Layout($this);
        $this->config = new Config();
        $this->request  = $req;
    }
    
    /**
     * Get config object
     * @return object
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Get layout object
     * @return object
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function setFlashMessage($name,$message)
    {
        $this->request->setSessionData($name, $message);
        return $this->layout;
    }
    
    /**
     * Bind data to layout object
     * @param string $key
     * @param mixed $value
     */
    public function setLayoutData($key,$value)
    {
        $this->layout->add($key,$value);
    }
    
    /**
     * Set Master View and Data
     * @param type $view
     * @param type $data
     */
    public function setMasterView($view,$data)
    {
        self::$masterView = $view;
        self::$masterViewModel = $data;
    }
    
    /**
     * Set Sub View of a Master View and Data for Sub View
     * @param type $view
     * @param type $data
     */
    public function setSubView($view,$data)
    {
        self::$view = $view;
        self::$viewModel = $data;
    }

    public function setJsonData($json)
    {
        self::$jsonData = json_encode($json);
    }

    /**
     * Render view
     * @throws \Exception
     */        
    public function render()
    {

        if(!empty(self::$view)){
            
            ob_start();
            
            if(!empty(self::$view)){
             
                if(!file_exists('views/'.self::$view.'.php')){
                    throw new \Exception("Sub View not found",404);
                }
                     
                
                $this->layout->setData(self::$viewModel);
                $this->layout->config = $this->config;
                
                if(isset(self::$viewModel) && is_array(self::$viewModel)){
                    if(!empty(self::$viewModel)){
                        foreach(self::$viewModel as $key=>$value){
                            $this->layout->add($key,$value); 
                        }
                    }
                    self::$viewModel['config'] = $this->config;
                    self::$viewModel['layout'] = $this->layout;
                    extract(self::$viewModel);
                }

                include('views/'.self::$view.'.php');
                $this->layout->setView(ob_get_clean());
                
                if(isset(self::$masterViewModel) && is_array(self::$masterViewModel)){
                    if(!empty(self::$masterViewModel)){
                        foreach(self::$masterViewModel as $key=>$value){
                            $this->layout->add($key,$value); 
                        }
                    }
                    self::$masterViewModel['config'] = $this->config;
                    self::$masterViewModel['layout'] = $this->layout;
                    extract(self::$masterViewModel);
                }
                
                
            }
            
            if(!empty(self::$masterView)){
                if(!file_exists('views/'.self::$masterView.'.php')){
                    throw new \Exception("View not found",404);
                }
                include('views/'.self::$masterView.'.php');
            }
            
        }else{
            
            if(isset(self::$masterViewModel) && is_array(self::$masterViewModel)){
                if(!empty(self::$masterViewModel)){
                    foreach(self::$masterViewModel as $key=>$value){
                        $this->layout->add($key,$value); 
                    }
                }
                self::$masterViewModel['config'] = $this->config;
                self::$masterViewModel['layout'] = $this->layout;
                extract(self::$masterViewModel);
            }
            
            
            
            if(!empty(self::$masterView)){
                if(!file_exists('views/'.self::$masterView.'.php')){
                    throw new \Exception("View not found",404);
                }
                include('views/'.self::$masterView.'.php');
            }
        }
        
        if(!empty(self::$jsonData)){
            header('Content-Type: application/json');
            echo self::$jsonData;
        }
        
        
    }
}
