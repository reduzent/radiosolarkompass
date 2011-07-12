  var delay = 120;
  var num = 12;
  var current = 0;
  var urlbase = 'pix/aussenden_';
  var aussenden = document.getElementById("aussenden");
  function timeMsg() {
    var t=setTimeout("timeMsg()",delay);
    current = (current + 1) % num;
    document.getElementById('aussenden').src = urlbase + (current + 1) + '.png';
  }
  if (aussenden != null) {
    timeMsg();
  }

