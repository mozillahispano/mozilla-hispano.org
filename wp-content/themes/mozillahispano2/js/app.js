var App = {
  init: function(){

  
    function showmenu(){
        var menuWrap = document.getElementById('menu2');

        if(menuWrap.classList.contains('on')){
          menuWrap.classList.remove('on');
          menuWrap.classList.add('off');
        }
        else {
          menuWrap.classList.remove('off');
          menuWrap.classList.add('on');
        }
    }
    
    var menu = document.getElementById('resp-button'),
        menuWrap = document.getElementById('menu2');

    if (window.innerWidth < 768){
      menuWrap.classList.add('off');
    }
    menu.addEventListener("click", showmenu, false);
    menu.addEventListener("touchstart", showmenu, false);
  }
};

window.onload = function(){
  App.init();
};
