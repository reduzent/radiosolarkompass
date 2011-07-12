$(document).ready(function(){
  $("#jquery_jplayer").jPlayer({
    ready: function () {
      this.element.jPlayer("setFile", "http://195.176.254.176:8010/rsk.mp3").jPlayer("play");
    },
    swfPath: "js"
  })
});

