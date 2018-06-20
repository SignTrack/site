/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var edit_id=0;
var edit_item='';
var edit_verb='';
var edit_type="add";
var fadeout=0;
var locations=new Array();
var today_timer=null;
var is_today=true;
var row=0;
var history_items=new Array();
var filteredItems=[];

$(function () {
    $( "#btn-user-add" )
    .click(function() {
        edit_type="add";
        $( "#form-add" ).dialog( "open" );
    });
    formatPick(CURRTIME,$( ".dpick" ));
    formatPick(TRAIL90,$( "#filter-start" ));
    formatPick(CURRTIME,$( "#filter-end" ));
    $( ".dpick" ).datepicker().on('changeDate', function(ev){
        $(".dpick").datepicker('hide');
                
    });
    $('#acct-area, #acct-phone1, #acct-phone2').autotab_magic().autotab_filter('numeric');
    filterSearch(0);
    //setupCampaign();
})
function setupCampaign(){
    console.log('ajax time...');
    $.ajax(
    {
        url: "https://signtrackapp.com/manage/campaign/handler/format/html",
        data: {
            email: 'jfb10@test.com',
            fname: 'Joseph',
            lname: 'Belfore',
            sign_limit: 200,
            package_name: 'Standard Package',
            phone: '(234)234-2344',
            type:'setup'
        },
        type: "POST",
        success: function(response){ 
            if(response.length==6){
                alert("Worked well");
                //success
                //display message, link to https://admin.signtrackapp.com, and reponse as password
            }else{
                //error
                alert("Sorry, there was an error setting up your account. Please call (xxx)xxx-xxxx");
            }
        }
    });
}

function checkPrem(){
    if($('#input-premium').is(':checked')){
        $('.premlist').show();
    }else{
       $('.premlist').hide(); 
    }
}
function clearAdd(){
    $('#input-name').val('');
    $('#input-contact').val('');
    $('#input-website').val('');
    $('#input-email').val('');
    $('#input-office1').val('');
    $('#input-office2').val('');
    $('#input-office3').val('');
    $('#input-mobile1').val('');
    $('#input-mobile2').val('');
    $('#input-mobile3').val('');
    $('#input-address').val('');
    $('#input-city').val('');
    $('#input-state').val('AZ');
    $('#input-zip').val('');
    $('#input-verified').prop('checked', false);
    $('#input-estimate').prop('checked', false);
    $('#input-premium').prop('checked', false);
    $('#input-auto').prop('checked', false);
    $('#input-rv').prop('checked', false);
    $('#input-boat').prop('checked', false);
    $('#input-detailing').prop('checked', false);
    $('#input-oil').prop('checked', false);
    $('#input-tire').prop('checked', false);
    $('#input-visa').prop('checked', false);
    $('#input-bbb').prop('checked', false);
    $('#input-guarantee').prop('checked', false);
    $('#input-discount').prop('checked', false);
    $('#input-ase').prop('checked', false);
    $('#input-24').prop('checked', false);
    $('#input-rvcert').prop('checked', false);
    $('#input-note').val('');
    formatPick(CURRTIME,$( "#input-date" ));
    $('#input-cycle').val('Monthly');
    $('#input-notify').prop('checked', false);
    checkPrem();
}


function drop(id){
    $('#msg-update').css('opacity','0.0');
    edit_id=id;
    var result = $.grep(items, function(e){
        return e.campaign_id == id;
    });
    edit_item=result[0].name;
    edit_verb="deleted";
    $('#msg-user-delete').html('<div style="color:#cc0000;">Are you sure you want to <strong>permantently delete</strong> campaign <strong><span id="delete-user">'+result[0].name+'</span></strong> and all data associated with it?</div>');
    $('#deleteModal').modal('show');
}

function confirm_delete(){
    loader('show');
    $.ajax(
    {
        url: APPLICATION_URL+"/campaign/handler/format/html",
        data: {
            campaign_id: edit_id, 
            type:'delete'
        },
        type: "POST",
        success: function(response){ 
            $('#deleteModal').modal('hide');
            if(isNumber(response)){
                if(response>0){
                    dataView.deleteItem(edit_id);
                    grid.invalidate();    
                }
            }else{
                alert("Sorry, there may have been an error deleting this record.");
            }
        }
    });
}

function formatDate(fdate){

    return fdate.substr(6,4)+'-'+fdate.substr(0,2)+'-'+fdate.substr(3,2);
}

function formatPick(val,dpicker){
    fday = val.substr(8, 2);
    fmonth = val.substr(5, 2);
    fyear = val.substr(0, 4);
    setDate = fmonth+'/'+fday+'/'+fyear;

    var pubdate = $( dpicker).datepicker('setValue',setDate).on('changeDate', function() {
        pubdate.hide();
    }).data('datepicker');
    ;
    $( dpicker ).datepicker('place','input');
}

function show_msg(type,msg){
    $('#msg-update').css('opacity','1.0');
    if(type>0){
        $('#msg-update').html('<div class="alert alert-success">The campaign was successfully '+edit_verb+'.</div>'); 
    }else{
        $('#msg-update').html('<div class="alert alert-error">The campaign could not be '+edit_verb+' due to an error. Please try again later. '+msg+'</div>'); 
    }
    clearTimeout(fadeout);
    fadeout = setTimeout(function() { 
        $('.alert-success,.alert-error').fadeOut(1000);
    },  5000);
}

function filterSearch(isExport){ 
    var curr_stats = $('#stats-campaigns').html();
    if($('#filter-search').val()!=''){
        $('#filter-state').val('ALL');
    }
    if(isExport==0){
        $('#grid-list').html('');
    }
    $.ajax(
    {
        url: APPLICATION_URL+"/campaign/list/format/html",
        data: {
            is_export: isExport,
            type:'search'
        },
        type: "POST",
        success: function(response){ 
            try{
                if(isExport){
                    response = response.replace(/"/g, '');
                    window.open('/tmp/'+response); 
                    $('#stats-campaigns').html(curr_stats);
                }else{
                   
                   $('#stats-campaigns').html('<span></span>');
                   $('#grid-list').html(response); 
                   $('#stats-campaigns span').html('Records: <strong>'+items.length+'</strong>');
                }
            }catch(err){
            //window.location.replace(APPLICATION_URL+'/auth/logout');
            }
        }
    }); 
}
function show_add(){
    edit_type = "add";
    edit_verb = "added";
    $('#acct-email,#acct-area,#acct-limit,#acct-phone1,#acct-phone2,#acct-fname,#acct-lname').val('');
    $('#addModal').modal('show');
}
function addCampaign(){
     var msg = '';
    $( "#form-campaign .required" ).each(function() {
        if($( this ).val()=='' || $( this ).val()==0){
            msg='Please enter a value for each field';
        }
      });
    if (msg == '') {
        
        if (edit_type == "edit") {
            edit_verb = "updated";
        } else {
            edit_verb = "added";
        }
        var data = {
            email : $('#acct-email').val(),
            package_name : 'Demo Account',
            sign_limit : $('#acct-limit').val(),
            fname : $('#acct-fname').val(),
            phone : '('+$('#acct-area').val()+')'+ $('#acct-phone1').val()+' '+$('#acct-phone2').val(),
            qty : $('#acct-qty').val(),
            lname : $('#acct-lname').val(),
            type : 'setup'
        };
        //loader('show');
        $.ajax(
                {
                    url: APPLICATION_URL + "/campaign/handler/format/html",
                    data: data,
                    type: "POST",
                    success: function (response) {
                        edit_item = $('Campaign');
                        if (isNumber(response)) {
                            if (response >= 0) {
                                filterSearch(0);
                                show_msg(1, '');
                            } else {
                                if (response == -1) {
                                    alert('The user you are trying to add is already a member of this campaign.');
                                } else {
                                    show_msg(0, '');
                                }
                            }
                            $('#addModal').modal('hide');
                        } else {
                            alert('Sorry, there was an error.');
                        }
                    }
                });
    } else {

        alert(msg);

    }
}
function filterCampaigns(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1;
    var yyyy = today.getFullYear();
    if(dd<10){
        dd='0'+dd
    } 
    if(mm<10){
        mm='0'+mm
    } 
    today = yyyy+'-'+mm+'-'+dd;
    
    if( $('#filter-campaign').val()=="Active"){
        console.log('sort active');
        filteredItems = $.grep(items, function(e){return e.date_end > today});
    }else if($('#filter-campaign').val()=="Archived"){
        console.log('sort archive');
        filteredItems = $.grep(items, function(e){return e.date_end < today});
    }else{
        console.log('sort all');
        filteredItems=items;
    }
    if($('#itemGrid').html()!=''){
        console.log(filteredItems);
            dataView.beginUpdate();
            dataView.setItems(filteredItems, 'campaign_id');
            dataView.endUpdate();
            grid.invalidate();
            grid.render();
          }
          
}
//determine if shipments are displaying today's records and if so, update

function evalUpdate(){
    if(is_today){
        filterSearch(0);
    }
}
//convert datetime stamp
function stringToDate(s) {
  var dateParts = s.split(' ')[0].split('-'); 
  var timeParts = s.split(' ')[1].split(':');
  var d = new Date(dateParts[0], --dateParts[1], dateParts[2]);
  d.setHours(timeParts[0], timeParts[1], timeParts[2])

  return d
}
//2 decimal rounding
function precise_round(num,decimals){
    var sign = num >= 0 ? 1 : -1;
    return (Math.round((num*Math.pow(10,decimals))+(sign*0.001))/Math.pow(10,decimals)).toFixed(decimals);
}
