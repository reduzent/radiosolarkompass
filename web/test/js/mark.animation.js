  var delay = 120;
  var num = 8;
  var current = 0;
  var urlbase = 'pix/aussenden_';
  var aussenden = document.getElementById("aussenden");
  function timeMsg() {
    var t=setTimeout("timeMsg()",delay);
    current = (current + 1) % num + 1;
    document.getElementById('aussenden').src = urlbase + current + '.png';
  }
  if (aussenden != null) {
    timeMsg();
  }

