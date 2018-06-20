/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var edit_id=0;
var edit_item='';
var edit_verb='';
var edit_type="add";
var fadeout=0;
  $(function () {
        getSearch();
  })
 
  function getSearch(){
      $('#loader').show();
      $('#grid-list').html('');
      $('#grid-list').load(APPLICATION_URL+'/log/list/format/html',function(){
          $('#loader').hide();
      });
  }
  function onImgRollover(e,src,id){

      $('body').append('<div class="img-pop"><img src="'+$('#'+id).attr('src')+'"></div>');
              var w;
              if($("#"+id).width()>$("#"+id).height()){
                  w=360;
                  h=($("#"+id).height()/$("#"+id).width())*360;
              }else{
                  h=360;
                  w=($("#"+id).width()/$("#"+id).height())*360+10;
              }
              $('.img-pop').css('left',e.pageX-$("#"+id).width()-w);
              $('.img-pop').css('top',e.pageY-$("#"+id).height()-h/2);
  }
  function onImgRolloff(){
      $('.img-pop').remove();
  }