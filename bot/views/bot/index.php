<!DOCTYPE html>
<html>
<?php $root_url = Yii::getAlias('@web'); ?>
<script src="<?php echo $root_url ?>/assets/f963e8c5/jquery.js"></script>
<h2> Bot Manage </h2>
<div></div>
<br>
<div>
<!-- <h3>Bot Remind</h3> -->
</div>
<div>
<!-- <h3>Bot Periodic</h3> -->
<label for="cars">Choose a channel:</label>
<!-- select option channel -->
<select id="chanel">
    <?php
    foreach($info_chanel as $key => $value){
         echo "<option value=".$key.">".$value."</option>";
    }
    ?>
</select>
<a href="<?php echo $root_url.'/index.php?r=bot%2Fchannel'?>">Local channel</a>
<!-- table load bot -->
<table>
    <br>
    <tr class="link-add"> <a class="link-add" href="<?php echo $root_url.'/index.php?r=bot%2Fedit'?>">ADD BOT</a></tr>
    <div id="dvLst">  
    </div>
</table>
</div>
<html>
 <!-- Scripts -->
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

// get list bot in channel
function getBot(){
    $.ajaxSetup({
        data: <?= \yii\helpers\Json::encode([
            \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
        ]) ?>
    });
    $.ajax({
            url: "<?php echo $root_url.'/index.php?r=bot%2Fget'?>",
            type: 'Post',
            data: {
            id: $("#chanel").val()
            },
            success: function(data) {
                console.log(data);
                loadList(data);
            }
    });
}

// reload list bot by Channel
function loadList(data){
    var bots_data = JSON.parse(data);
    var divdt ='';
    // generate table data bot 
    divdt +=    '<tr Style ="display: flex;" ><th style="width: 10%;">ID</th><th style="width: 18%;">Name</th><th style="width: 18%;">Channel</th><th style="width: 27%;">Content</th><th style="width: 10%;" >Time</th><th style="width: 18%;">Action</th></tr>';
    for (i=0;i<bots_data.length ;i++)
    {
        divdt += '<tr id="bot'+bots_data[i]["id_bot"]+'">';
        divdt += "<td style='width: 10%;' >"+bots_data[i]['id_bot']+"</td>";
        divdt += "<td style='width: 18%;' >"+bots_data[i]['name'].substr(0,30)+"</td>";
        divdt += "<td style='width: 18%;' >"+bots_data[i]['group_id']+"</td>";
        divdt += "<td style='width: 27%;' >"+bots_data[i]['content'].substr(0,90)+"</td>";
        divdt += "<td style='width: 10%;' >"+bots_data[i]['time_send']+"</td>";
        // divdt += "<td>"+bots_data[i]['']+"</td>";
        divdt += '<td style="width: 18%;" > <a class="button1" href=" <?php echo $root_url.'/index.php?r=bot%2Fedit&id='?> '+bots_data[i]["id_bot"]+'">Edit</a><br>';
        divdt += '<input class="button1" type="button" value="Delete"  onclick="deleteBot('+ bots_data[i]["id_bot"]+ ')" ></td>';
        divdt += "</tr>";
    }
    // add table data to view
    $("#dvLst").html(divdt);
}

//delete bot from channel
function deleteBot(id){
    if (confirm('Are you sure to delete bot id:'+id)) {
        // set csrfToken page
        $.ajaxSetup({
            data: <?= \yii\helpers\Json::encode([
                \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
            ]) ?>
        });
        $.ajax({
            url: "<?php echo $root_url.'/index.php?r=bot%2Fdelete' ?>",
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
            ,error(){
                alert('Delete Error, connect false');
            }
        });
    }else
    {
      //cancel click
    }
}

</script>