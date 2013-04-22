"use strict";
/* Copyright (c) 2011, David Bengoa Rocandio
* All rights reserved.
*
* This program is licensed under the Chicken Dance License v0.2
*
* You should have received a copy of the license text with this
* software, along with instructions on how to perform the Chicken
* Dance.
*
* Full license: http://bengoarocandio.com/html5tweets/COPYING
* Chicken Dance instructions: http://bengoarocandio.com/html5tweets/DANCE
*/

var cwidth, cheight; //Canvas width and height

var VEL = 1;

var FLY_TIME = 1000*VEL;
var VIEW_TIME = 5000*VEL;

var searchTerm = location.hash || "#mozcamplatam";

var pos_map = {};
var used_tweets = {};
var avaliableTweets = [];
var avaliablePhotos = [];
var alphaAnimating = [];
var avaliableTweetsLastUpdated = 0;
var bannerVisible = true;

function v(x,y){
    var flip = Math.random()>0.5;
    var ret = {
        type:"v",
        tweet:null,
        x:x,
        y:y,
        w:1,
        h:2,
        r:flip?1:3,
        f:flip
    };
    pos_map[x+"_"+y] = ret;
    pos_map[x+"_"+(y+1)] = ret;
    return ret;
}
function h(x,y){
    var flip = Math.random()>0.5;
    var ret = {
        type:"h",
        tweet:null,
        x:x,
        y:y,
        w:2,
        h:1,
        r:flip?2:0,
        f:flip
    };
    pos_map[x+"_"+y] = ret;
    pos_map[(x+1)+"_"+y] = ret;
    return ret;
}

var tweet_holders = [
    v(1,1),
    v(1,3),
    v(2,3),
    v(3,3),
    v(4,2),
    v(5,2),
    v(8,1),
    v(9,1),
    v(6,4),
    v(9,4),
    v(1,5),
    v(4,5),
    v(5,5),
    v(7,5),
    v(8,5),
    v(5,7),
    v(6,6),
    v(9,6),
    v(3,8),
    v(4,8),
    v(1,9),
    v(2,9),
    v(5,9),
    v(6,9),
    v(9,9)
];
var U = 300;

var actualTweet = null,
    actualTweetAnimating = false,
    actualTweetBorn = 0,
    previousTweet = null;


for(var x = 1; x<=9; ++x){
    for(var y = 1; y<=10; ++y){
        if(!(x+"_"+y in pos_map)){
            tweet_holders.push(h(x,y));
        }
    }
}



var $canvas = jQuery("canvas"),
    canvas = $canvas[0],
    ctx = canvas.getContext("2d"),
    icanvas = document.createElement("canvas"),
    ictx = icanvas.getContext("2d"),
    $window = jQuery(window),
    $body = jQuery(document.body);


function updateCanvasSize(){
    /*cwidth = $window.width();
    cheight = $window.height();*/
   cwidth = 900;
    cheight = $window.height();

    $canvas.attr({
        width: cwidth,
        height: cheight
    });
    scheduleAnimationFrame(render);
}

function avaliableCount() {
    return avaliablePhotos.length + avaliableTweets.length;
}

var lastFlickr = false; // True if last obj was a photo
function nextAvaliable(){
    if(avaliablePhotos.length && avaliableTweets.length){
        lastFlickr = !lastFlickr;
        return (lastFlickr?avaliablePhotos:avaliableTweets).shift();
    }else{
        return (avaliablePhotos.length && avaliablePhotos.shift()) ||
               (avaliableTweets.length && avaliableTweets.shift())
    }
}

function insertarOrdenado(twt){
    var ins = false, objList = twt.flickr?avaliablePhotos:avaliableTweets;
    for(var i=0, l=objList.length; i<l; ++i){
        if(twt.id > objList[i].id){
            ins = true;
            objList.splice(i,0,twt);
            break;
        }
    }
    !ins && objList.push(twt);
    avaliableTweetsUpdated();
}
function addTweets(flickr, data){

    var res = flickr?data["photos"]["photo"]:data["results"];

    for(var i = 0; i<res.length;++i){
        var tweet = res[i];
        if(tweet.id in used_tweets && avaliableCount() > 0)continue;
        tweet.flickr = flickr;
        var img = new Image();
        tweet["imgelm"] = img;
        img.onload = (function(twt){return function(){
            if(bannerVisible){
                jQuery("#load").fadeOut();
                bannerVisible = false;
            }
            insertarOrdenado(twt);
        }})(tweet);

        if(flickr){
            img.src = "http://farm" + tweet.farm + ".staticflickr.com/" + tweet.server + "/" + tweet.id + "_" + tweet.secret + "_z.jpg";
        }else{
            img.src = tweet[flickr?"":"profile_image_url"];
        }
    }
}
function loadTweets(){
    avaliableTweetsLastUpdated = +new Date();
    var baseUrl = "http://search.twitter.com/search.json?q=";
    jQuery.getJSON(baseUrl + encodeURIComponent(searchTerm) + "&rpp=45&result_type=recent&callback=?", addTweets.bind(this, false));
    jQuery.getJSON("http://api.flickr.com/services/rest/?method=flickr.photos.search&tags=" + encodeURIComponent(searchTerm) + "&format=json&api_key=8a1a30adce6e6eb4600f0fae518f31aa&jsoncallback=?", addTweets.bind(this, true));
}

function isEmpty(x,y){
    return !pos_map[x+"_"+y]["tweet"];
}
function validPos(x,y){
    return x<=9 && x>=1 && y<=10 && y>=1
}

function getEmptyPlaceholder(){
    var ax = actualTweet.x,
        ay = actualTweet.y;

    for(var r=1; r<10; ++r){

        //Filas horizontales
        for(var x=ax-r+1; x<ax+r-1; ++x){
            if(validPos(x,ay+r) && isEmpty(x,ay+r)){
                return pos_map[x+"_"+(ay+r)];
            }
        }
        for(var x=ax-r+1; x<ax+r-1; ++x){
            if(validPos(x,ay-r) && isEmpty(x,ay-r)){
                return pos_map[x+"_"+(ay-r)];
            }
        }
        //Filas verticales
        for(var y=ay-r+1; y<ay+r-1; ++y){
            if(validPos(ax+r,y) && isEmpty(ax+r,y)){
                return pos_map[(ax+r)+"_"+y];
            }
        }
        for(var y=ay-r+1; y<ay+r-1; ++y){
            if(validPos(ax-r,y) && isEmpty(ax-r,y)){
                return pos_map[(ax-r)+"_"+y];
            }
        }
        //Esquinas
        var esquinas = [[1,1],[-1,1],[1,-1],[-1,-1]];
        for(var i = 0; i<4; ++i){
            if(validPos(ax+r*esquinas[i][0],ay+r*esquinas[i][1]) && isEmpty(ax+r*esquinas[i][0],ay+r*esquinas[i][1])){
                return pos_map[(ax+r*esquinas[i][0])+"_"+(ay+r*esquinas[i][1])];
            }
        }
    }

    //Uhm...
}


function updateInnerCanvas(){
    for(var i = 0, l=alphaAnimating.length; i<l; ++i){
        var aainfo = alphaAnimating[i];
        var hld = aainfo[0];
        ictx.save();
        ictx.translate(hld.x*U,hld.y*U);
        ictx.rotate(hld.r*Math.PI/2);

        if(hld.r == 2 || hld.r == 1){
            ictx.translate(0,-U);
        }
        if(hld.r == 2 || hld.r == 3){
            ictx.translate(-2*U,0);
        }
        ictx.clearRect(0,0,2*U,U);

        var opacity;
        if(new Date()-aainfo[3] > aainfo[4]){
            alphaAnimating.splice(i,1);
            i--; l--;
            opacity = aainfo[2];
            if(aainfo[2] == 0){
                aainfo[0]["tweet"] = null;
            }
        }else{
            opacity = easeInOut(aainfo[1],aainfo[2],aainfo[4],new Date() - aainfo[3]);
        }

        hld["tweet"] && renderTweet(hld["tweet"],0,0,opacity);
        ictx.restore();
    }
}

function freePlaceholdersIfNeeded(){
    var older_placeholders = [];

    for(var i=0,l=tweet_holders.length; i<l; ++i){
        if(tweet_holders[i]["tweet"]){
            older_placeholders.push(tweet_holders[i]);
        }
    }
    if(older_placeholders.length > tweet_holders.length-3){ //Si quedan menos de 3 libres
        var older = older_placeholders.sort(function(a,b){
            return a.tweet.born - b.tweet.born;
        })[0];
        alphaAnimating.push([older,0.5,0,+new Date(),400]);
    }

}

function avaliableTweetsUpdated(){
    if(new Date()-avaliableTweetsLastUpdated > 30000){
        loadTweets();
    }
    if(avaliableCount() === 0)return;

    if(!actualTweet){
        var ph = tweet_holders[8];
        ph.tweet = nextAvaliable();
        actualTweet = ph;
        actualTweetBorn = +new Date();
        ph.tweet.born = actualTweetBorn;
        alphaAnimating.push([ph,0,1,actualTweetBorn,800]);
        scheduleAnimationFrame(render);
    }else if(new Date()-actualTweetBorn > VIEW_TIME){
        var ph = getEmptyPlaceholder();
        ph.tweet = nextAvaliable();
        previousTweet = actualTweet;
        actualTweet = ph;
        actualTweetAnimating = true;
        actualTweetBorn = +new Date();
        ph.tweet.born = actualTweetBorn;

        alphaAnimating.push([actualTweet,0,1,actualTweetBorn,800]);
        alphaAnimating.push([previousTweet,1,0.5,actualTweetBorn,400]);

        freePlaceholdersIfNeeded();
        scheduleAnimationFrame(render);
    }
}

setInterval(avaliableTweetsUpdated,1000);


function drawWords(words,x,y,color){
    var dx = 0;
    for(var i = 0, l = words.length; i<l; ++i){
        ictx.fillStyle="#ccc";
        //Sombra
        ictx.fillText(words[i],x+dx+2,y+2,U*2);

        if(searchTerm.toLowerCase() == words[i].toLowerCase()){
            ictx.fillStyle = "#447BC4";
        }else if(words[i][0] == "@"){
            ictx.fillStyle = "#ccc";
        }else if(/^https?:/.test(words[i])){
            ictx.fillStyle = "#ccc";
        }else{
            ictx.fillStyle = "#484848";
        }
        ictx.fillText(words[i],x+dx,y,U*2);

        dx += ictx.measureText(words[i] + " ").width;
    }
}

function renderTweet(tweet,x,y,op){
    ictx.globalAlpha=op;
    if(tweet.flickr){
        var iw = tweet.imgelm.width, ih= tweet.imgelm.height;
        var h = Math.min(U, ih, (ih/iw)*(2*U));
        var w = Math.min(2*U, iw, (iw/ih)*U);
        ictx.drawImage(tweet.imgelm, x+(600-w)/2, y+(300-h)/2, w, h);
        return;
    }
    var font_size = 30, padded_font_size = font_size+5;
    var words = tweet["text"].split(/\s+/);
    ictx.fillStyle = "#ddd";
    //ictx.font = "bold "+font_size+"px MuseoSans"
    ictx.font = "bold "+font_size+"px sans";

    var vpos = 1;
    var printed = 0;
    for(var i = 0,l=words.length; i<=l; ++i){
        if(ictx.measureText(words.slice(printed,i).join(" ")).width > (U*2-10)){
            if(i-printed > 1){
                drawWords(words.slice(printed,i-1),x+5,y+vpos*padded_font_size);
                printed = i-1;
                i--;
            }else{
                drawWords([words[i]],x+5,y+vpos*padded_font_size);
                printed = i;
            }
            vpos++;
        }
    }
    if(printed < words.length){
        drawWords(words.slice(printed,words.length),x+5,y+vpos*padded_font_size);
    }
    ictx.fillStyle="#fff";
    ictx.fillRect(x+10,y+(vpos+0.5)*padded_font_size-5,60,60);
    ictx.drawImage(tweet["imgelm"],x+15,y+(vpos+0.5)*padded_font_size,50,50);
    ictx.fillStyle = "#ccc";
    ictx.font = "20px sans";
    ictx.fillText(prettyDate(tweet["created_at"]) + " por @" + tweet["from_user"], x+80 ,y+30+(vpos+0.5)*padded_font_size);

}


function init(){
    icanvas.setAttribute("width",10*U);
    icanvas.setAttribute("height",11*U);

    updateCanvasSize();
    $window.resize(updateCanvasSize);
    loadTweets();
}

function camRot(r){
    return (r%2==1?r+2:r)*Math.PI/2;
}

function render(){
    if(!actualTweet)return;

    updateInnerCanvas();

    ctx.clearRect(0,0,cwidth,cheight);
    ctx.save();

    ctx.rotate(-Math.PI/50);

    ctx.translate(cwidth/2,cheight/2);

    var dt = new Date() - actualTweetBorn;
    if(dt > FLY_TIME){
        actualTweetAnimating = false;
    }
    var cx,cy;
    if(actualTweetAnimating){
        ctx.rotate(easeInOut(camRot(previousTweet.r),camRot(actualTweet.r),FLY_TIME,dt));
        var cx = easeInOut(-(previousTweet.x+previousTweet.w/2)*U,-(actualTweet.x+actualTweet.w/2)*U,FLY_TIME,dt);
        var cy = easeInOut(-(previousTweet.y+previousTweet.h/2)*U,-(actualTweet.y+actualTweet.h/2)*U,FLY_TIME,dt);
    }else{
        ctx.rotate(camRot(actualTweet.r));
        var cx = -(actualTweet.x+actualTweet.w/2)*U,
            cy = -(actualTweet.y+actualTweet.h/2)*U;
    }
    ctx.translate(cx,cy);
    ctx.drawImage(icanvas,0,0);

    ctx.restore();
    if(actualTweetAnimating || alphaAnimating.length > 0)scheduleAnimationFrame(render);
}
window.addEventListener("MozBeforePaint", render, false);

var pow = Math.pow;
function easeInOut(minValue,maxValue,totalSteps,actualStep) {
    var t = Math.min(actualStep/totalSteps,1)*2, c = maxValue-minValue;
    if(t < 1){
        return c/2*(pow(t,3)) + minValue;
    }else{
        return c/2*(pow(t-2,3)+2) + minValue;
    }
}
/*
 * JavaScript Pretty Date
 * Copyright (c) 2008 John Resig (jquery.com)
 * Licensed under the MIT license.
 */

// Takes an ISO time and returns a string representing how
// long ago the date represents.
function prettyDate(time){
	var date = new Date(time),
		diff = ( (new Date() - date)/ 1000),
		day_diff = Math.floor(diff / 86400);
	if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
		return "hace ??? ";
	return day_diff == 0 && (
			diff < 60 && "Hace " + Math.floor(diff) + " segundos" ||
			diff < 120 && "Hace 1 minuto" ||
			diff < 3600 && "Hace " + Math.floor( diff / 60 ) + " minutos" ||
			diff < 7200 && "Hace una hora" ||
			diff < 86400 && "Hace " + Math.floor( diff / 3600 ) + " horas") ||
		day_diff == 1 && "Ayer" ||
		day_diff < 7 && "Hace " + day_diff + " dias" ||
		day_diff < 31 && "Hace " + Math.ceil( day_diff / 7 ) + " semanas";
}
var scheduleAnimationFrame = window.mozRequestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.oRequestAnimationFrame ||
    window.msRequestAnimationFrame ||
    window.requestAnimationFrame ||
    function(callback){
        setTimeout(callback,30);
    };

init();

