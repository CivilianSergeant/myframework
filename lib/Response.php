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
    protected $route;

    
    protected static $masterView;
    protected static $masterViewModel;
    protected static $view;
    protected static $viewModel;
    
    public function __construct($route) {
        $this->layout = new Layout($this);
        $this->config = new Config();
        $this->route  = $route;
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
    
    public function isLoggedIn()
    {
        if(isset($_SESSION)){
            $this->sessionData = $_SESSION;
            if(!empty($this->sessionData)){
                return true;
            }
            return false;
        }
        else{
            return false;
        }

    }
    
    public function authorise(){
        if($this->route == Config::get('default_login_route')){
            if($this->isLoggedIn()){
                $this->redirect('/');
            }
        }else{
            if($this->isLoggedIn()){
                $this->redirect(Config::get('default_login_route'));
            }
        }
    }

    

    public function setLayoutData($key,$value)
    {
        $this->layout->add($key,$value);
    }
    
    public function getLayout()
    {
        return $this->layout;
    }
    
    public function getConfig()
    {
        return $this->config;
    }

            
    public function render()
    {
        
        Bootstrap::init($this);
        
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
                extract(self::$masterViewModel);
            }
            
            self::$masterViewModel['layout'] = $this->layout;
            
            if(!empty(self::$masterView)){
                if(!file_exists('views/'.self::$masterView.'.php')){
                    throw new \Exception("View not found",404);
                }
                include('views/'.self::$masterView.'.php');
            }
        }
        
        
    }
}
