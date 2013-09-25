jQuery(function(){
   var os = "win";
   if(navigator && navigator.userAgent){
       var ua = navigator.userAgent.toLowerCase();
       if(ua.indexOf("linux") >= 0){
           os = "unix";
       }else if(ua.indexOf("os x") >=0){
           os = "mac"; // bah!
       }
   }
   var body = jQuery(document.body).addClass(os);

   jQuery(".winSelect, .macSelect, .unixSelect").click(function(){
       var esto = jQuery(this);
       var newos = esto.hasClass("macSelect")?"mac":(esto.hasClass("unixSelect")?"unix":"win");
       body.removeClass("unix").removeClass("mac").removeClass("win").addClass(newos);
       return false;
   });
});

/* Para wikibits.js */
var stylepath, wgContentLanguage;