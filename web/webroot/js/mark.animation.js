  var delay = 80;
  var num = 12;
  var current = 0;
  var urlbase = 'pix/aussenden3_';
  function timeMsg() {
    var t=setTimeout("timeMsg()",delay);
    current = (current + 1) % num;
    var aussenden = document.getElementById("aussenden");
    if (aussenden != null) {
      aussenden.src = urlbase + current + '.png';
    }
  }

