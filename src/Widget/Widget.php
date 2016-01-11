<?php namespace LazyNewbie\Boilerplate\Widget;


use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

abstract class Widget implements Renderable, Htmlable{

  protected $view;
  protected $namespace;
  protected $data = [];

  public function __construct(array $data = [], $view = null){
    $this->namespace = \Config::get("ln-boilerplate.widgets.views_namespace");
    if($data) $this->data = $data;
    if($view) $this->view = $view;
  }

  public function setViewData(array $input, $merge = true){
    if($merge) $this->data = array_merge($this->data, $input);
    else       $this->data = $input;
  }

  public function setView($name){
    $this->view = $name;
  }

  public function render(){
    return \View::make("{$this->namespace}::{$this->view}", $this->data);
  }

  public function toHtml(){
    return $this->__toString();
  }

  public function __toString(){
    return $this->render()->toString();
  }
} 