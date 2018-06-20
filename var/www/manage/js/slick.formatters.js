/***
 * Contains basic SlickGrid formatters.
 * @module Formatters
 * @namespace Slick
 */

(function ($) {
  // register namespace
  $.extend(true, window, {
    "Slick": {
      "Formatters": {
        "UserOptions": UserOptionsFormatter,
        "ActionOptions": ActionOptionsFormatter,
        "CampaignActions": CampaignActionsFormatter,
        "CampaignLink": CampaignLinkFormatter,
        "StartFormatter": StartFormatter,
        "EndFormatter": EndFormatter,
        "LogTimeFormatter":LogTimeFormatter,
        "AddedFormatter":AddedFormatter,
        "LastLoginFormatter":LastLoginFormatter,
        "LastUpdateFormatter":LastUpdateFormatter,
        "SignImageFormatter":SignImageFormatter,
        "DistanceFormatter":DistanceFormatter,
        "SignIdFormatter":SignIdFormatter,
        "SignOptions":SignOptionsFormatter,
        "NameFormatter":NameFormatter
      }
    }
  });
  
  function UserOptionsFormatter(row, cell, value, columnDef, dataContext) {
    content='';
     
        content+='<a href="javascript:edit_user('+dataContext.user_id+','+row+')"><img style="width:20px;height:20px;" src="'+APPLICATION_URL+'/images/edit.png"></a>';
         content+='<a style="margin-left:15px;" href="javascript:delete_user('+dataContext.user_id+')"><img style="width:20px;height:20px;" src="'+APPLICATION_URL+'/images/delete.png"></a>';
         return content;
  }
  function SignOptionsFormatter(row, cell, value, columnDef, dataContext) {
    content='';
     
        content+='<a href="javascript:edit('+dataContext.sign_id+','+row+')"><img style="width:20px;height:20px;" src="'+APPLICATION_URL+'/images/edit.png"></a>';
         content+='<a style="margin-left:15px;" href="javascript:drop('+dataContext.sign_id+')"><img style="width:20px;height:20px;" src="'+APPLICATION_URL+'/images/delete.png"></a>';
         return content;
  }
  function ActionOptionsFormatter(row, cell, value, columnDef, dataContext) {
     content='';
     
        content+='<a href="javascript:edit('+dataContext.campaign_id+','+row+')"><img style="width:20px;height:20px;" src="'+APPLICATION_URL+'/images/edit.png"></a>';
         content+='<a style="margin-left:15px;" href="javascript:drop('+dataContext.campaign_id+')"><img style="width:20px;height:20px;" src="'+APPLICATION_URL+'/images/delete.png"></a>';
     
     
    return content;
  }
  function CampaignActionsFormatter(row, cell, value, columnDef, dataContext) {
     return '<a style="margin-left:15px;" href="javascript:drop('+dataContext.campaign_id+')"><img style="width:20px;height:20px;" src="'+APPLICATION_URL+'/images/delete.png"></a>';
  }
  function CampaignLinkFormatter(row, cell, value, columnDef, dataContext) {
    return '<a href="'+APPLICATION_URL+'/campaign/'+dataContext.campaign_id+'/dashboard">'+value+'</a>';
  }
  function StartFormatter(row, cell, value, columnDef, dataContext) {
    return dataContext.start;
  }
  function EndFormatter(row, cell, value, columnDef, dataContext) {
    return dataContext.end;
  }
  function LogTimeFormatter(row, cell, value, columnDef, dataContext) {
    return dataContext.logdate;
  }
  function AddedFormatter(row, cell, value, columnDef, dataContext) {
    return dataContext.added;
  }
  function LastLoginFormatter(row, cell, value, columnDef, dataContext) {
      if(dataContext.login!='00/00/00'){
          return dataContext.login;
      }else{
          return '--';
      }
  }
  function LastUpdateFormatter(row, cell, value, columnDef, dataContext) {
    return dataContext.last;
  }
  function SignIdFormatter(row, cell, value, columnDef, dataContext) {
    return dataContext.sign_id.substring(0, 3)+'-'+dataContext.sign_id.substring(3, 6)+'-'+dataContext.sign_id.substring(6, 9);
  }
  function NameFormatter(row, cell, value, columnDef, dataContext) {
    return dataContext.lname+', '+dataContext.fname;
  }
  function DistanceFormatter(row, cell, value, columnDef, dataContext) {
      if(value>.2){
          return '<div style="display:inline-block;padding:0px 4px;border-radius:3px;background-color:rgba(204,0,0,.2);"><small>'+value+'</small></div>';
      }else{
          return '<small>'+value+'</small>';
      }
  }
  function SignImageFormatter(row, cell, value, columnDef, dataContext) {
      if(dataContext.img!=''){
        return '<img id="img'+dataContext.log_id+'" src="'+APPLICATION_URL+'/images/upload/'+dataContext.img+'" style="height:27px;margin-top:-3px;" onmouseleave="onImgRolloff()" onmouseover="onImgRollover(event,\''+APPLICATION_URL+'/images/upload/'+dataContext.img+'\',\'img'+dataContext.log_id+'\')"> ';   
      }
  }
})(jQuery);