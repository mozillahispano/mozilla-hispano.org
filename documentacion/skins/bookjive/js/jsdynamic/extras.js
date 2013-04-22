function sum(n) {
  if ((n == 0) || (n == 1))
      return 1;
  else {
      var result = (n + sum(n-1) );
      return result;
  }
}


/**
  * Jednostajnie przyspieszony/spowolniony
  **/
function recountFramesS (frameList, kf1, kf2, params) {
  var fnum = kf2-kf1;
  var vect = params.vect?params.vect:1;
  var t = sum(fnum);
  var a = 1/t;
  if (vect>0)
    var v = 0;
  else
    var v = fnum/t;
  var s = 0;
  for (var i=kf1+1;i<kf2;i++) {
    if (!frameList[i])
      frameList[i] = {vals:{},num:i};
    for (val in frameList[kf2].vals) {
      var diff = (frameList[kf2].vals[val]-frameList[kf1].vals[val]);
      frameList[i].vals[val] = frameList[kf1].vals[val] + (diff*(s+v));
    }
    s += v;
    v += (a*vect);
  }
}