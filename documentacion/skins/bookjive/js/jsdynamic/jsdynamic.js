function Animator () {
  var elemList = [];
  var I = null;
  var dir = 1;

  function step () {
    var running = false;
    var frame;

    for (elem in elemList) {
      if (dir == 1)
        frame = elemList[elem].ap.getNextFrame();
      else
        frame = elemList[elem].ap.getPrevFrame();
      if (frame !== null) {
        running = true;
        for (i in frame) {
          if (i=='left' || i=='top')
            elemList[elem].elem.style[i] = frame[i] + elemList[elem].ap.unit;
          else if (i=='opacity')
            elemList[elem].elem.style[i] = frame[i];
          else
            elemList[elem].elem.setAttribute(i, frame[i] + elemList[elem].ap.unit);
        }
      }
    }

    if (running === false)
      stop();
  }

  function start (t) {
    if (I)
      stop()
    I = setInterval(step, t);
  }

  function stop () {
    I = clearInterval(I);
  }

  function push (elem, ap) {
    elemList.push({elem:elem, ap:ap.clone()});
  }

  function rev () {
    dir = dir == 1 ? -1 : 1
  }

  return {start:start,stop:stop, rev:rev, push:push}
}

function AnimationPath (frameList) {
  if (!frameList) {
    frameList = [];
  }

  function recountFrames(kf1, kf2) {
    frames = kf2 - kf1;
    for (val in frameList[kf2].vals) {
      var diff = parseFloat(frameList[kf2].vals[val] - frameList[kf1].vals[val])/frames;
      for(var i=kf1+1;i<=kf2-1;i++) {
        if (!frameList[i])
          frameList[i] = {vals:{},num:i};
        frameList[i].vals[val] = frameList[kf2].vals[val] - (diff*(kf2-i))
      }
    }
  }

  function prevKF (kf) {
    if (kf == 0)
      return null;
    var tempKF = null;
    for (var i in frameList) {
      tempKF = frameList[i];
      break;
    }
    if (!tempKF)
      return null;
    while (tempKF.next !== null && tempKF.next.num < kf)
      tempKF = tempKF.next;
    return tempKF.num;
  }

  function nextKF (kf) {
    if (kf == 0)
      return null;
    if (!frameList[0])
      return null;
    var tempKF = frameList[0];
    while (tempKF.next !== null && tempKF.next.num <= kf)
      tempKF = tempKF.next;
    if (tempKF.next !== null)
      return tempKF.next.num;
    else
      return null;
  }

  function addKF (frameNum, vals, customFunc, cFParams) {
    var pKF = prevKF(frameNum);
    var nKF = nextKF(frameNum);
    if (!frameList[frameNum]) {
      frameList[frameNum] = {vals:{},num:frameNum,prev:pKF,next:nKF};
      if (pKF !== null)
        frameList[pKF].next = frameList[frameNum];
      if (nKF !== null)
        frameList[nKF].prev = frameList[frameNum];
    }
    for (var i in vals)
      frameList[frameNum].vals[i] = vals[i];

    if (pKF !== null)
      if (customFunc)
        customFunc(frameList, pKF, frameNum, cFParams);
      else
        recountFrames(pKF, frameNum);
    if (nKF !== null)
      recountFrames(frameNum, nKF);
  }

  function removeKF (frameNum) {
    if (frameNum === 0)
      return false;

    pKF = prevKF(frameNum);
    nKF = nextKF(frameNum);
    if (pKF !== null && nKF !== null)
      recountFrames(pKF, nKF);
    return true;
  }

  function clone () {
    var ap =  new AnimationPath(frameList);
    ap.unit = this.unit;
    return ap;
  }

  function getCurrentFrame (dir) {
    if (frameList[this.fp])
      return frameList[this.fp].vals;
    else
      return null;
  }

  function getNextFrame () {
    if (frameList[this.fp+1]) {
      this.fp+=1
      return frameList[this.fp].vals;
    } else
      return null;
  }

  function getPrevFrame () {
    if (frameList[this.fp-1]) {
      this.fp-=1
      return frameList[this.fp].vals;
    } else
      return null;
  }

  return {addKF:addKF,
          removeKF:removeKF,
          clone:clone,
          getCurrentFrame:getCurrentFrame,
          getNextFrame:getNextFrame,
          getPrevFrame:getPrevFrame, fp:0, unit:'px'}
}