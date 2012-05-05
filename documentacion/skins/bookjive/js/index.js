var ap = null
var ap2 = null

function prepareAnimator () {
  ap = new AnimationPath();
  ap.addKF(0, {opacity:1})
  ap.addKF(9, {opacity:0})

  ap2 = new AnimationPath();
  ap2.addKF(0, {opacity:0})
  ap2.addKF(9, {opacity:1})


  var tboxes = document.getElementsByClassName('topicbox')
  for (var j=0;j<tboxes.length;j++) {
    elems = tboxes[j].getElementsByTagName('td')
    for (var i=0;i<elems.length;i++) {
      longdesc = elems[i].getElementsByClassName('longdesc');
      if (longdesc.length < 1)
        continue;
      var elem = elems[i]
  
      var a = new Animator();
      var desc = elem.getElementsByClassName('desc')[0]
      var longdesc = elem.getElementsByClassName('longdesc')[0]
      a.push(desc, ap)
      a.push(longdesc, ap2)
      a.rev()
      elem.animator = a
      elem.addEventListener('mouseover', switchAnim, false);
      elem.addEventListener('mouseout', switchAnim2, false);
    }
  }
}

function switchAnim () {
  this.animator.rev()
  this.animator.start(20)
}

function switchAnim2 () {
  this.animator.rev()
  this.animator.start(60)
}