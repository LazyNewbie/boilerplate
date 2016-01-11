<?php namespace LazyNewbie\Boilerplate\Traits;


trait RenderableTrait
{

  protected $beforeRender = [];           // list of methods that will be called before data render

  protected $viewData = [];               // list of variables that'll be passed to view
  protected $viewPrefix = "";             // this prefix will be prepended to view name

  protected $title = "";                  // page title
  protected $description = "";            // page description
  protected $keywords = "";               // page keywords
  protected $bodyClass = "";
  protected $bodyId = "";

  protected $styles = [];
  protected $scripts = [];

  protected $viewRequest;                 // request class, Illuminate\Http\Request by default

  protected $formData = [];
  protected $oldInputHasPriority = false;

  protected $cookies = [];                // list of cookies that will be sent with response
  protected $headers = [];                // list of headers that will be sent with response



  /**
   * Makes view
   *
   * @param string $viewName
   * @param array $data
   * @param string $controller
   *
   * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
   */
  public function view($viewName, array $data = [], $controller = ""){

    foreach($this->beforeRender as $method) $this->$method($viewName, $data, $controller);

    \View::share("__siteTitle", $this->title);
    \View::share("__siteDescription", $this->description);
    \View::share("__siteKeywords", $this->keywords);

    \View::share("__bodyId", $this->bodyId);
    \View::share("__bodyClass", $this->bodyClass);

    \View::share("__styles", $this->styles);
    \View::share("__scripts", $this->scripts);

    $rc = new \ReflectionClass($this);

    $className = str_replace( \App::getNamespace()."Http\\Controllers\\", "", $rc->getName() );


    \View::share("__controller", $controller? $controller: "{$className}::{$viewName}" );


    if($this->viewPrefix){
      $nbView = $this->viewPrefix.'.'.$viewName;
    }else{
      $nbView = 'controllers.' . str_replace("\\", ".", $className). '.' . $viewName;
    }

    if( \View::exists($nbView) ){
      $viewName = $nbView;
    }

    if( !empty($this->formData) ){
      if($this->oldInputHasPriority){
        $this
          ->request()
          ->session()
          ->flashInput( array_merge($this->formData, $this->request()->old(null, [])) );
      }else{
        $this
          ->request()
          ->session()
          ->flashInput( $this->request()->old(null, []), $this->formData );
      }
    }

    return view( $viewName, $this->viewData, $data );
  }


  /**
   * Makes response
   *
   * @param string $viewName
   * @param array $data
   * @param string $controller
   * @param int $status
   * @return \Illuminate\Http\Response
   */
  public function response($viewName, array $data = [], $controller = "", $status = 200){

    /**@var \Illuminate\Http\Response $response*/
    $response = app(
      '\Illuminate\Http\Response',
      [
        $this->view($viewName, $data, $controller),
        $status,
        $this->headers
      ]
    );

    foreach($this->cookies as $c){
      $response->withCookie($c);
    }

    return $response;
  }


  /**
   *
   * @return \Illuminate\Http\Request
  */
  private function request(){
    return $this->viewRequest? $this->viewRequest: app('Illuminate\Http\Request');
  }
}