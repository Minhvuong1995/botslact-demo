<?php $root_url = Yii::getAlias('@web'); ?>
<script src="<?php echo $root_url ?>/assets/f963e8c5/jquery.js"></script>
<h2> Bot Detail </h2>
<?php
$edit =0;
if(isset($info_bot)){
    $edit =1;
}
?>
<form id="saveform" action="<?php echo $root_url.'/index.php?r=bot%2Fsave'?>" method="POST">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
    <input type="hidden" name="id_bot" value="<?php if($edit&&isset($info_bot['id_bot'])) echo $info_bot['id_bot']; ?>" />
    <input type="hidden" name="date_send" id="date_send">
    <input type="hidden" name="month_send" id="month_send">
    <input type="hidden" name="date_of_week" id="date_of_week">
<table id='dvLst'>
    <tr>
        <th colspan = "1" >Id Bot </th><td colspan = "5"><?php if($edit) echo $info_bot['id_bot']; ?></td>
    </tr>
    <tr>
        <th> Channel</th>
        <td>
            <select style="width: 100%" id="group_id" name="group_id"">
            <?php
            foreach($channel as $key => $value){
                
                if(($edit) && $key== $info_bot['group_id']){
                    echo "<option  value=".$key.' selected ="selected" >'.$value.'</option>';
                }
                else{
                    echo "<option  value=".$key.">".$value."</option>";
                }
            }
            ?>
            </select>
        </td>
    </tr>

    <tr>
        <th>Name</th>
        <td><input class="inputfull" type="text" name="name" id="name" value="<?php if($edit) echo $info_bot['name']; ?>"></td>
    </tr>
    <tr>
        <th>Content</th>
        <td><textarea name="content" id="content"rows="4" style="width: 100%;" ><?php if($edit) echo $info_bot['content']; ?> </textarea></td>
    </tr>
    <tr>
        <th>Time send</th>
        <td><input class="inputfull"  type="time" id="time_send" name="time_send" min="00:00" max="23:59" value ="<?php if($edit){echo $info_bot['time_send'];}else{ echo "08:00:00";} ?>">
        </td>
    </tr>
    <tr>
        <th>Day of month Send</th>
        <td>
        <input type="checkbox" name="CkAllDay" id="CkAllDay">
        <label for="CkAllDay"> Send all day of month</label>
        <div id ="DayDetail" class="detail">
            <table id="DetailTable" >
            <tr>
            <td><input type="checkbox" name="day1" id="day01"> <label for="day1">  01 </label></td>
            <td><input type="checkbox" name="day2" id="day02"> <label for="day2">  02 </label></td>
            <td><input type="checkbox" name="day3" id="day03"> <label for="day3">  03 </label></td>
            <td><input type="checkbox" name="day4" id="day04"> <label for="day4">  04 </label></td>
            <td><input type="checkbox" name="day5" id="day05"> <label for="day5">  05 </label></td>
            <td><input type="checkbox" name="day6" id="day06"> <label for="day6">  06 </label></td>
            <td><input type="checkbox" name="day7" id="day07"> <label for="day7">  07 </label></td>
            <td><input type="checkbox" name="day8" id="day08"> <label for="day8">  08 </label></td>
            <td><input type="checkbox" name="day9" id="day09"> <label for="day9">  09 </label></td>
            <td><input type="checkbox" name="day10" id="day10"> <label for="day10"> 10 </label></td>
            </tr>
            <tr>
            <td><input type="checkbox" name="day11" id="day11"> <label for="day11"> 11 </label></td>
            <td><input type="checkbox" name="day12" id="day12"> <label for="day12"> 12 </label></td>
            <td><input type="checkbox" name="day13" id="day13"> <label for="day13"> 13 </label></td>
            <td><input type="checkbox" name="day14" id="day14"> <label for="day14"> 14 </label></td>
            <td><input type="checkbox" name="day15" id="day15"> <label for="day15"> 15 </label></td>
            <td><input type="checkbox" name="day16" id="day16"> <label for="day16"> 16 </label></td>
            <td><input type="checkbox" name="day17" id="day17"> <label for="day17"> 17 </label></td>
            <td><input type="checkbox" name="day18" id="day18"> <label for="day18"> 18 </label></td>
            <td><input type="checkbox" name="day19" id="day19"> <label for="day19"> 19 </label></td>
            <td><input type="checkbox" name="day20" id="day20"> <label for="day20"> 20 </label></td>
            </tr>
            <tr>
            <td><input type="checkbox" name="day21" id="day21"> <label for="day21"> 21 </label></td>
            <td><input type="checkbox" name="day22" id="day22"> <label for="day22"> 22 </label></td>
            <td><input type="checkbox" name="day23" id="day23"> <label for="day23"> 23 </label></td>
            <td><input type="checkbox" name="day24" id="day24"> <label for="day24"> 24 </label></td>
            <td><input type="checkbox" name="day25" id="day25"> <label for="day25"> 25 </label></td>
            <td><input type="checkbox" name="day26" id="day26"> <label for="day26"> 26 </label></td>
            <td><input type="checkbox" name="day27" id="day27"> <label for="day27"> 27 </label></td>
            <td><input type="checkbox" name="day28" id="day28"> <label for="day28"> 28 </label></td>
            <td><input type="checkbox" name="day29" id="day29"> <label for="day29"> 29 </label></td>
            <td><input type="checkbox" name="day30" id="day30"> <label for="day30"> 30 </label></td>
            </tr>
            <tr>
            <td><input type="checkbox" name="day31" id="day31"> <label for="day31"> 31 </label></td>
            </tr>
            </table>
        </div>
        </td>
    </tr>
    <tr>
        <th>Month of year Send</th>
        <td>        
            <input type="checkbox" name="CkAllMonth" id="CkAllMonth">
            <label for="CkAllMonth"> Send all month of year </label>
            <div id ="MonthDetail" class="detail">
                <table id="DetailTable" >
                    <td><input type="checkbox" name="month01" id="month01"> <label for="month01">  01 </label></td>
                    <td><input type="checkbox" name="month02" id="month02"> <label for="month02">  02 </label></td>
                    <td><input type="checkbox" name="month03" id="month03"> <label for="month03">  03 </label></td>
                    <td><input type="checkbox" name="month04" id="month04"> <label for="month04">  04 </label></td>
                    <td><input type="checkbox" name="month05" id="month05"> <label for="month05">  05 </label></td>
                    <td><input type="checkbox" name="month06" id="month06"> <label for="month06">  06 </label></td>
                    <td><input type="checkbox" name="month07" id="month07"> <label for="month07">  07 </label></td>
                    <td><input type="checkbox" name="month08" id="month08"> <label for="month08">  08 </label></td>
                    <td><input type="checkbox" name="month09" id="month09"> <label for="month09">  09 </label></td>
                    <td><input type="checkbox" name="month10" id="month10"> <label for="month10">  10 </label></td>
                    <td><input type="checkbox" name="month11" id="month11"> <label for="month11">  11 </label></td>
                    <td><input type="checkbox" name="month12" id="month12"> <label for="month12">  12 </label></td>
                </table>
            </div>
        </td>
        
    </tr>
    <tr>
        <th>Day of week Send</th>
        <td>        
            <input type="checkbox" name="CkAllWeek" id="CkAllWeek">
            <label for="CkAllWeek"> Send day of week </label>
            <div id ="WeekDetail" class="detail">
                <table id="DetailTable" >
                    <td><input type="checkbox" name="week01" id="week01"> <label for="week01">  Sunday </label></td>
                    <td><input type="checkbox" name="week02" id="week02"> <label for="week02">  Monday </label></td>
                    <td><input type="checkbox" name="week03" id="week03"> <label for="week03">  Tuesday </label></td>
                    <td><input type="checkbox" name="week04" id="week04"> <label for="week04">  Wednesday </label></td>
                    <td><input type="checkbox" name="week05" id="week05"> <label for="week05">  Thurday  </label></td>
                    <td><input type="checkbox" name="week06" id="week06"> <label for="week06">  Friday </label></td>
                    <td><input type="checkbox" name="week07" id="week07"> <label for="week07">  Saturday </label></td>
                </table>
            </div>
        </td>
        
    </tr>
    <tr>
        <th>Remind</th>
        <td>
            <input type="checkbox" name="remind" id="remind" <?php if($edit && $info_bot['remind']!=0) echo 'checked'; ?>  >
            <label for="CkRemind"> Remind </label>
            <div id ="RemindDetail">
                <label for="CkRemind"> Delay time </label>
                <br>
                <input type="text" name="time_remind" id="time_remind" <?php if($edit && $info_bot['remind']!=0) echo 'value ='. $info_bot['time_remind'] ; ?> >
                <br>
                <label for="CkRemind"> Text redmind </label>
                <textarea name="text_remind" id="text_remind" rows="3" style="width: 100%;"> <?php if($edit && $info_bot['text_remind']!=0) echo  $info_bot['text_remind'] ; ?> </textarea>
            </div>
        </td>
    </tr>
</table>
<br>
<div style = "float:right;">
    <input type="button" id ="btnSave" value="Save">
    <input type="button" id="btnCancel" value="Cancel">  
</div>
</form>
<script type="text/javascript">
$(document).ready(function() {
     //CkRemind
    if($("#remind")[0].checked == false){
        $("#RemindDetail").hide();
    }
    $("#remind").click(function(){
       
       if($("#remind")[0].checked == true){
            $("#RemindDetail").show();
       }
       else{
            $("#RemindDetail").hide();
       }
    });



    <?php
        if(isset($save)&&$save){?>
           alert('Save successfully') 
        <?php
        }
        ?>
    // load day
    var day =[];
    <?php 
        if($edit==0) echo '$("#CkAllDay").prop("checked", true); $("#DayDetail").hide();'; 
        else{ 
        ?>
            var str =" " + "<?php if($info_bot['date_send']!=NULL) echo  $info_bot['date_send']; else echo "50,";?>";
            day = str.split(',');
            if(day[0] ==50){
                $("#CkAllDay").prop("checked", true); 
                $("#DayDetail").hide();
            }
            else{
                for(var i=0;i<str.length;i++){
                    $("#day"+day[i]+"").prop("checked", true);
                }
            }
        <?php
        }
    ?>
    
    $("#CkAllDay").click(function(){
       
       if($("#CkAllDay")[0].checked == true){
            $("#DayDetail").hide();
       }
       else{
            $("#DayDetail").show();
       }
    });

    // load month
    var month = [];
    <?php 
        if($edit==0) echo '$("#CkAllMonth").prop("checked", true); $("#MonthDetail").hide();'; 
        else{ 
        ?>
            var str =" " +" <?php if($info_bot['month_send']!=NULL) echo  $info_bot['month_send']; else echo "13,";  ?>";
            month = str.split(',');
            if(month[0]==13){
                $("#CkAllMonth").prop("checked", true);
                $("#MonthDetail").hide();
            }
            else{
                for(var i=0;i<str.length;i++){
                if (month[i])
                $("#month"+month[i]+"").prop("checked", true);
            }
            }
            
        <?php
        }
    ?>
    
    $("#CkAllMonth").click(function(){
       
       if($("#CkAllMonth")[0].checked == true){
            $("#MonthDetail").hide();
       }
       else{
            $("#MonthDetail").show();
       }
    });

    //load week
    var week = [];
        <?php 
            if($edit==0){?>
            var str ="02,03,04,05,06";
            <?php }else{ ?>
            var str =" " + "<?php if($info_bot['date_of_week']!=NULL) echo  $info_bot['date_of_week']; else echo "8,";  ?>";
            <?php
            }
            ?>
    week = str.split(',');
    if(week[0]==8){
        $("#CkAllWeek").prop("checked", true);
        $("#WeekDetail").hide();
    }
    else{
        for(var i=0;i<str.length;i++){
            $("#week"+week[i+1]+"").prop("checked", true);
        }
    }

    
    $("#CkAllWeek").click(function(){
       
       if($("#CkAllWeek")[0].checked == true){
            $("#WeekDetail").hide();
       }
       else{
            $("#WeekDetail").show();
       }
    });


    $('form').bind("keypress", function(e) {
        if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
        }
    });
    $("#btnCancel").click(function(){
        window.location="<?php echo $root_url.'/index.php?r=bot%2F'?>"
    });

    
    $("#btnSave").click(function(){
        var fdate= ",";
        var fmonth= ",";
        var fweek = ",";
        //get date
        if($("#CkAllDay")[0].checked == true){
            fdate = "";
        }
        else{
            for(var i=1;i<=31;i++){
                if(i<10){
                    var dataday = $("#day0"+i)[0].checked;
                    if(dataday ==true){
                        fdate += "0"+i+',';
                    } 
                }
                else{
                    var dataday = $("#day"+i)[0].checked;
                    if(dataday ==true){
                        fdate += ""+i+',';
                    } 
                }    
            }
        }

        //get month
        if($("#CkAllMonth")[0].checked == true){
            fmonth = "";
        }
        else{
            for(var i=1;i<=12;i++){
                if(i<10){
                    var datamonth = $("#month0"+i)[0].checked;
                    if(datamonth ==true){
                        fmonth += "0"+i+',';
                    } 
                }
                else{
                    var datamonth = $("#month"+i)[0].checked;
                    if(datamonth ==true){
                        fmonth += ""+i+',';
                    } 
                }    
            }
        }

        //get week
        if($("#CkAllWeek")[0].checked == true){
            fweek = "";
        }
        else{
            for(var i=1;i<=7;i++){
                var dataweek = $("#week0"+i)[0].checked;
                if(dataweek ==true){
                    fweek += "0"+i+',';
                } 
            }
        }
        if($("#remind")[0].checked == false){
            $("#time_remind").val('');
        }
        $("#date_send").val(fdate);
        $("#month_send").val(fmonth);
        $("#date_of_week").val(fweek);
        $('#saveform').submit();
    });

});
</script>