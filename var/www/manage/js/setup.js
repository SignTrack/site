/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
    setDatepicker(currentDay(),'#input-date');
    $('#input-date').keydown(function(event){
       event.preventDefault();
    });
    $('#btn-submit').click(function(e){
       e.preventDefault();
       submitForm(); 
    });
})


function setDatepicker(val,dpicker){
    var pubdate = $( dpicker).datepicker('setValue',val).on('changeDate', function() {
        pubdate.hide();
    }).data('datepicker');
    ;
    $( dpicker ).datepicker('place','input');
    
}
function submitForm(){
    var msg='';
    if($('#input-campaign').val()==""){
        msg='Please enter a Campaign Name';
    }else if($('#input-state').val()==""){
        msg='Please select a state';
    }else if($('#input-password').val()=="" || $('#input-confirm').val()==""){
        msg='Please enter a password and confirm password';
    }else if($('#input-password').val().length<8){
        msg='Please enter a password at least 8 characters long';
    }else if($('#input-password').val()!=$('#input-confirm').val()){
        msg='The passwords entered do not match;';
        $('#input-password').val('');
        $('#input-confirm').val('');
    }
    if(msg==''){
        var locale;
        $('#input-city').val()==""?locale=$('#input-state').val():locale=$('#input-city').val()+', '+$('#input-state').val();
        $.ajax(
                        {

                            url: APPLICATION_URL+"/campaign/setup/format/html",
                            data: {
                                name:$("#input-campaign").val(),
                                date_election:format_mysql_date($("#input-date").val()),
                                locale:locale,
                                password:$("#input-password").val()
                                
                            },
                            type: "POST",
                            success: function(response){ 
                                
                                if(isNumber(response)){
                                    if(response>=0){
                                        
                                    
                                        window.location.replace(APPLICATION_URL+'/login');
                                    }else{
                                        alert('Sorry, there was an error');
                                    }
                                    
                                }else{
                                    //window.location.replace(APPLICATION_URL+'/auth/logout');
                                }
                            }
                        });
    }else{
        alert(msg);
    }
}
function currentDay(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!

    var yyyy = today.getFullYear();
    if(dd<10){
        dd='0'+dd
    } 
    if(mm<10){
        mm='0'+mm
    } 
    return mm+'/'+dd+'/'+yyyy;
}
function format_mysql_date(fdate){

    var formatteddate = fdate.substr(6,4)+'-'+fdate.substr(0,2)+'-'+fdate.substr(3,2);
    
    return formatteddate;
}