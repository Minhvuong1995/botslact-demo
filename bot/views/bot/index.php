<!DOCTYPE html>
<html>
<?php $rooturl = Yii::getAlias('@web'); ?>
<script src="<?php echo $rooturl ?>/assets/f963e8c5/jquery.js"></script>
<h2> Bot Manage </h2>
<label for="cars">Choose a channel:</label>
<select id="chanel">
    <?php
    foreach($info_chanel as $key => $value){
         echo "<option value=".$key.">".$value."</option>";
    }
    ?>
    
</select>
<table>
    <br>
    
    <tr class="linkadd"> <a class="linkadd" href="<?php echo $rooturl.'/index.php?r=bot%2Fedit'?>">ADD BOT</a></tr>
    <div id="dvLst">
        
    </div>
    
</table>
<html>
<script type="text/javascript">
$(document).ready(function() {
    
    $('#chanel').on('change', function() {
        getBot()
    });
    $('form').bind("keypress", function(e) {
        if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
        }
    });
    getBot();
});
function getBot(){
    $.ajaxSetup({
        data: <?= \yii\helpers\Json::encode([
            \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
        ]) ?>
    });
    $.ajax({
            url: "<?php echo $rooturl.'/index.php?r=bot%2Fget'?>",
            type: 'Post',
            data: {
            id: $("#chanel").val()
            },
            success: function(data) {
                console.log(data);
            loadlist(data);
            }
    });
}
function loadlist(data){
    // var bots_data = Array();
    var bots_data = JSON.parse(data);
    // var bots_data = data;
    var divdt ='';

    divdt +=    '<tr Style ="display: flex;" ><th style="width: 10%;">ID</th><th style="width: 18%;">Name</th><th style="width: 18%;">Channel</th><th style="width: 27%;">Content</th><th style="width: 10%;" >Time</th><th style="width: 18%;">Action</th></tr>';
    for (i=0;i<bots_data.length ;i++)
    {
        divdt += '<tr id="bot'+bots_data[i]["id_bot"]+'">';
        divdt += "<td style='width: 10%;' >"+bots_data[i]['id_bot']+"</td>";
        divdt += "<td style='width: 18%;' >"+bots_data[i]['name']+"</td>";
        divdt += "<td style='width: 18%;' >"+bots_data[i]['group_id']+"</td>";
        divdt += "<td style='width: 27%;' >"+bots_data[i]['content']+"</td>";
        divdt += "<td style='width: 10%;' >"+bots_data[i]['time_send']+"</td>";
        // divdt += "<td>"+bots_data[i]['']+"</td>";
        divdt += '<td style="width: 18%;" > <a class="button1" href=" <?php echo $rooturl.'/index.php?r=bot%2Fedit&id='?> '+bots_data[i]["id_bot"]+'">Edit</a><br>';
        divdt += '<input class="button1" type="button" value="Delete"  onclick="deletebot('+ bots_data[i]["id_bot"]+ ')" ></td>';
        divdt += "</tr>";
    }
    $("#dvLst").html(divdt);
}
function deletebot(id){
    if (confirm('Are you sure to detete bot id:'+id)) {
        $.ajaxSetup({
            data: <?= \yii\helpers\Json::encode([
                \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
            ]) ?>
        });
        $.ajax({
            url: "<?php echo $rooturl.'/index.php?r=bot%2Fdelete' ?>",
            type: 'Post',
            data: {
            id: id
            },
            success: function(data) {
                if(data ==1){
                    alert('Delete successfully');
                    $("#bot"+id).hide(1000);
                }
                else{
                    alert('Delete Error');
                }
            
            }
        });
    }else
    {
      
    }
}

</script>