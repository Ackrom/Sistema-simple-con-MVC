var cargaInic=function(){
  $.ajax({
    URL:__URL__,
    method:"POST",
    data:{tabla:0,id:0},
  }).done(function(datos){
    alert(this.url);
    $("#si").html(datos);
  });
}
