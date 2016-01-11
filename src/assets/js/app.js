/**
 * Created by alex on 13.12.15.
 */

App = (function($){
  "use strict";

  var controllerName;
  var firstBind = true;              // flag, shows if current bind() call is first one;


  function getControllerName(){
    if(!controllerName){
      controllerName = $("head").attr("data-controller") + "::" + $("head").attr("data-method");
    }

    return controllerName;
  }


  function bindController(cList, controllerName){
    if( !cList[controllerName] ) return null;

    bind(
      cList[controllerName].bind,
      cList[controllerName].initType
    );

  }


  function bindModules(modules){
    Object.keys(modules).forEach(function(key){
      bind(modules[key].bind, modules[key].initType);
    });
  }


  function bind(body, initType){

    if( !firstBind ) initType = undefined;
    if( initType ) initType = initType.toLowerCase();

    switch (initType) {

      case "load":
        $(window).load(function(){
          body();
        });
        break;

      case "ready":
        $(document).ready(function(){
          body();
        });
        break;

      case undefined:
      case null:
        body();
        break;

      default:
        throw new Error("Invalid initTime value ('" + initType + "')");
        break;

    }
  }


  return {

    settings: {},
    controllers: {},
    modules: {},
    helpers: {},


    bind: function(){

      bindModules(this.modules);
      bindController(this.controllers, getControllerName());

      firstBind = false;
    },


    unbind: function(){

    }


  };

})(jQuery);



//App.controllers["Backend/UsersController::getList"] = {
//  bind: function(){
//
//  },
//  unbind: function(){
//
//  },
//  initType: "ready" // load, ready, null
//};
//
//
//App.modules["load"] = {
//  bind: function(){
//    console.log("load");
//  },
//  unbind: function(){
//
//  },
//  initType: "load" // load, ready, null
//};
//
//
//
//App.bind();
