<html>
<head>
        <title>Next Generation PetCalc</title>
        <meta charset="utf-8">
</head>
<body>
        <h3>欢迎来到新一代魔力宝贝算档器</h3>
        <h4>感谢魔力百科提供宠物资料数据</h4>
        <h4>欢迎关注魔力百科更好（看）的算档器！</h4>

    <form action= "/" method = "post">
        宠物名称<input type=text id ="petName" name="petName" value="<?php echo $petName?>"><br>
        <?php
        if ($petSelect != ''){
            echo '<p>选择宠物：';
            echo '<select name="petList" onchange="submit();">';
            foreach ($petSelect as $key => $result) {
                echo '<option value ="'.$result.'">'.$result.'</option>';
            }
            echo '</select>';
            echo '</p>';
        }
        ?>
        宠物档次<input type=text name="petGrade" value="<?php echo $petGrade?>" ><br>
        宠物等级<input type=text name="petLv" value="1" readonly="yes"><br>
        当前数据<input type=text name="petData" value="<?php echo $petData?>"><br>
        <input type = "submit" value = "提交" >
        <?php
        if ($petResult != ''){
            echo '<p>计算结果</p>';
            foreach ($petResult as $key => $result) {
                echo $result;
            }
        }
        ?>
    </form>
</body>
