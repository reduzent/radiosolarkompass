  var delay = 80;
  var num = 4;
  var current = 0;
  var urlbase = 'pix/aussenden_';
  function timeMsg()
  {
    var t=setTimeout("timeMsg()",delay);
    current = (current + 1) % num;
    document.getElementById('aussenden').src = urlbase + current + '.png';
  }

