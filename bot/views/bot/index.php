<script src="/bot/bot/web/assets/f963e8c5/jquery.js"></script>
<?php $rooturl = Yii::getAlias('@web');?>
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
    <tr class="linkadd"> <a class="linkadd" href="<?php echo $rooturl.'/index.php?r=bot%2Feditbots'?>">ADD BOT</a></tr>
    <div id="dvLst">
    </div>
    
</table>
<script type="text/javascript">
$(document).ready(function() {
    
    $('#chanel').on('change', function() {
        get_bos()
    });
    $('form').bind("keypress", function(e) {
        if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
        }
    });
    get_bos();
});
function get_bos(){
    $.ajaxSetup({
        data: <?= \yii\helpers\Json::encode([
            \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
        ]) ?>
    });
    $.ajax({
            url: "<?php echo $rooturl.'/index.php?r=bot%2Fgetbots'?>",
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
    divdt +=    '<tr><th>ID</th><th>Name</th><th>Channel</th><th>Content</th><th>Time</th><th>Repeat</th><th>Action</th></tr>';
    for (i=0;i<bots_data.length ;i++)
    {
        divdt += "<tr>";
        divdt += "<td>"+bots_data[i]['id_bot']+"</td>";
        divdt += "<td>"+bots_data[i]['name']+"</td>";
        divdt += "<td>"+bots_data[i]['group_id']+"</td>";
        divdt += "<td>"+bots_data[i]['content']+"</td>";
        divdt += "<td>"+bots_data[i]['timesend']+"</td>";
        // divdt += "<td>"+bots_data[i]['']+"</td>";
        divdt += "<td>"+'not set'+"</td>";
        divdt += '<td> <a href=" <?php echo $rooturl.'/index.php?r=bot%2Feditbots&id='?> '+bots_data[i]["id_bot"]+'">Edit</a></td>';
        
        divdt += "</tr>";
    }
    $("#dvLst").html(divdt);
}
</script>