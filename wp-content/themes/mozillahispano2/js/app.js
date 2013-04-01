var App = {
  init: function(){

  
    function showmenu(){
        var menuWrap = document.getElementById('menu2');

        if(menuWrap.style.visibility  == 'false' || menuWrap.style.visibility == 'visible'){
          menuWrap.style.visibility = "hidden";
        }
        else {
          menuWrap.style.visibility="visible";
        }
    }
    
    var menu = document.getElementById('resp-button'),
        menuWrap = document.getElementById('menu2');

    if (window.innerWidth < 768){
      menuWrap.style.visibility = 'hidden';
    }
    menu.addEventListener("click", showmenu, false);
    menu.addEventListener("touchstart", showmenu, false);
  }
};

window.onload = function(){
  App.init();
};
