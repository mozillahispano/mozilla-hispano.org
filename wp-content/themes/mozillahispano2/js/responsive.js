(function(){
  var toggle = document.getElementById('toggle');
  var menu = document.getElementById('menu-nav');
  console.log('initial value: ' + menu);

  function showMenu(el){
    if(el.classList.contains('open')){
      el.classList.remove('open');
    } else {
      el.classList.add('open');
    }
  }

  if (window.innerWidth < 768){
    console.log('this value: ' + menu);
    menu.classList.add('close');
  }

  toggle.addEventListener('click', function(){showMenu(menu)}, false);
  toggle.addEventListener('touchstart', function(){showMenu(menu)}, false);
})();
