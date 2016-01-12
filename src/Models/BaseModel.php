<?php namespace LazyNewbie\Boilerplate\Models;


class BaseModel extends \Illuminate\Database\Eloquent\Model
{

  protected static function boot(){
    parent::boot();

    //lets try to register our observer

    $className = get_called_class();
    $obClass = explode("\\", $className);
    array_splice($obClass, 2, 0, 'Observers');
    $className = implode("\\", $obClass) . "Observer";

    if(class_exists($className)){
      $className::observe(new $obClass );
    }
  }


  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = ["id", "created_at", "updated_at"];

  /**
   * Set all empty fields to null
   *
   * @var bool
   */
  protected $nullable = [];

  /**
   * If date field format is different from Y-m-d
   * we need to use replacement pattern
   *
   * structure field_name => [ "pattern" => "", "replacement" => "" ]
   */
  protected $quickMutators = [];


  /**
   * Cache for throughCache method
   */
  protected $cache = [];


  /**
   * this date format will be used in date presenter
  */
  protected $dateFormat = "d/m/Y";


  /**
   * Create a new Eloquent model instance.
   *
   * @param  array  $attributes
   */
  public function __construct(array $attributes = array()){
    parent::__construct($attributes);

    if( !empty($this->nullable) ){
      self::saving(function($model){
        $attr = $model->attributes;
        foreach ($attr as $name => $value) {
          if (empty($value) && in_array($name, $this->nullable)) {
            $attr[$name] = null;
          }
        }
        $model->setRawAttributes($attr);
        return true;
      });
    }

  }


  /**
   * Keep data that was already processed by current model
   *
   * @param string $key
   * @param callable $function
   * @param bool $force
   * @return mixed
   */
  protected function throughCache($key, callable $function, $force = false){
    if($force || !isset($this->cache[$key])){
      $this->cache[$key] = $function();
    }
    return $this->cache[$key];
  }


  /**
   * Override default attributes mutator
   *
   * @param  string  $key
   * @param  mixed   $value
   * @return void
   */
  public function setAttribute($key, $value){
    if(isset($this->quickMutators[$key])){
      $value = preg_replace($this->quickMutators[$key]["pattern"], $this->quickMutators[$key]["replacement"], $value);
    }
    parent::setAttribute($key, $value);
  }


  /**
   * This method may be used for quick date formatting
   *
   * @param string $fieldName
   * @return string
   */
  public function presentDate($fieldName){
    return $this->$fieldName->format($this->dateFormat);
  }


  /**
   * Returns raw attribute value
   *
   * @param string $attr
   * @return string|int|float|null
  */
  public function getRawAttribute($attr){
    return isset($this->attributes[$attr])?$this->attributes[$attr]:null;
  }

}
