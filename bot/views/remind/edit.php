<?php $root_url = Yii::getAlias('@web'); ?>
<script src="<?php echo $root_url ?>/assets/f963e8c5/jquery.js"></script>
<h2> Remind Detail </h2>
<?php
$edit = 0;
if (isset($info_config)) {
    $edit = 1;
}
?>
<form id="save_form" action="<?php echo $root_url . '/index.php?r=remind%2Fsave' ?>" method="POST">
    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
    <input type="hidden" name="id" value="<?php if ($edit) echo $info_config['id']; ?>" />
    <input type="hidden" name="name" id="name" value="" />
    <table id='dvLst'>
        <tr>
            <th colspan="1">Id Bot </th>
            <td colspan="5"><?php if ($edit) echo $info_config['id']; ?></td>
        </tr>
        <tr>
            <th> Channel</th>
            <td>
                <select <?php if ($edit) echo 'disabled="disabled"'; ?> style="width: 100%" id="id_channel" name="id_channel"">
                <?php
                foreach ($list_channel as $key => $value) {

                    if (($edit) && $key == $info_config['id_channel']) {
                        echo "<option  value=" . $key . ' selected ="selected" >' . $value . '</option>';
                    } else {
                        echo "<option  value=" . $key . " disable>" . $value . "</option>";
                    }
                }
                ?>
            </select>
            </td>
        </tr>
        
        <tr>
            <th> Remind to group</th>
            <td>
                <input type="checkbox" name="send_group" id="send_group" <?php if ($edit) if ($info_config['send_group']) echo 'checked'; ?>>
                    <label for="send_group"> Send remind to group/channel </label><br>
                    <label for="text_remind_group">Text Message</label><br>
                    <textarea name="text_remind_group" id="text_remind_group" rows="4" style="width: 100%;"><?php if ($edit) echo $info_config['text_remind_group']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th> Remind to private</th>
            <td>
                <input type="checkbox" name="send_private" id="send_private" <?php if ($edit) if ($info_config['send_private']) echo 'checked'; ?>>
                <label for="send_private"> Send remind to private message </label><br>
                <label for="text_remind_private">Text Message</label><br>
                <textarea name="text_remind_private" id="text_remind_private" rows="4" style="width: 100%;"><?php if ($edit) echo $info_config['text_remind_private']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th> Check notify</th>
            <td>
                <div style="float: left;">
                    <input type="checkbox" name="Check_all_message" id="Check_all_message" <?php if ($edit) if (!$info_config['check_notify_channel'] && !$info_config['check_notify_user']) echo 'checked'; ?>>
                    <label for="Check_all_message">Check all message from group </label><br>
                </div>

                <div id="notify_detail" style="float: left; padding-left: 30px;">
                    <input type="checkbox" name="check_notify_channel" id="check_notify_channel" <?php if ($edit) if ($info_config['check_notify_channel']) echo 'checked'; ?>>
                    <label for="check_notify_channel">Check notify group/channel </label><br>
                    <input type="checkbox" name="check_notify_user" id="check_notify_user" <?php if ($edit) if ($info_config['check_notify_user']) echo 'checked'; ?>>
                    <label for="check_notify_user">Check notify user </label>
                </div>

            </td>
        </tr>
        <tr>
            <th> Delay time </th>
            <td>
                <input type="text" name="time_remind" id="time_remind" value="<?php if ($edit) echo $info_config['time_remind']; ?>">(s)
            </td>
        </tr>
    </table>
    <div style="float:right;">
        <input type="button" id="btnSave" value="Save">
        <input type="button" id="btnCancel" value="Cancel">
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            <?php if (isset($save)) { ?>
                alert("Save successfully")
                // window.location = "<?php echo $root_url . '/index.php?r=remind%2F' ?>"
            <?php } ?>

            check_notify();
            $("#Check_all_message").click(function() {
                check_notify();
            });
            $("#btnSave").click(function() {
                var name = $("#id_channel option:selected").text();
                $("#name").val(name);
                $('#save_form').submit();
            });
            $("#btnCancel").click(function() {
                window.location = "<?php echo $root_url . '/index.php?r=remind%2F' ?>"
            });
        });

        function check_notify() {
            if ($("#Check_all_message")[0].checked == false) {
                $("#notify_detail").show();
            } else {
                $("#notify_detail").hide();
                $("#check_notify_channel").prop("checked", false);
                $("#check_notify_user").prop("checked", false);
            }
        }
    </script>