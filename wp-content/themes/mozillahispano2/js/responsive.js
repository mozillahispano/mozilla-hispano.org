(function(){
  var toggle = document.getElementById('toggle');
  var menu = document.getElementById('menu-nav');

  function showMenu(el){
    if(el.classList.contains('open')){
      el.classList.remove('open');
    } else {
      el.classList.add('open');
    }
  }

  if (window.innerWidth < 768){
    menu.classList.add('close');
  }

  toggle.addEventListener('click', function(){showMenu(menu)}, false);
  toggle.addEventListener('touchstart', function(){showMenu(menu)}, false);
})();
