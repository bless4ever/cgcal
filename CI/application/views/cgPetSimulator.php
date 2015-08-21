<!DOCTYPE html>
<html>
<head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="baidu-site-verification" content="B1LS7CXrmW" />
        <title>Next Generation PetCalc - CgCal.com</title>
        <meta charset="utf-8">

</head>
<body>
        <h3>>>魔力宝贝宠物模拟器<<</h3>
        <h3>欢迎来到新一代魔力宝贝算档器>CGCal<</h3>
        <h4>感谢魔力百科提供宠物资料数据<br></h4>
        <h4>代码托管在：<a href="https://github.com/yanggs07/cgcal">github</a></h4>
        <h3><a href="/">返回首页</a></h3>

    <form action= "/cgPetSimulator/" method = "post" >
        宠物名称<input type=text id ="petName" name="petName" value="<?php echo $petName?>" style=" width:280px" placeholder="输入宠物名称或拼音简称，如‘螳螂’，‘螳’，‘tl’均可"><br>
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

        if ($petGrade != '')
        {
            echo '宠物档次<input type=text name="petGrade" value="'.$petGrade.'" style=" width:120px" placeholder="档次将被自动抓取" readonly>（暂不支持自定义）<br>';
            echo '宠物等级<input type=text name="petLv" value="'.$petLv.'" style=" width:40px" placeholder="100">输入宠物当前等级，默认为100级<br>';
            echo '掉档值<input type=text name="petDiffGrade" value="'.$petDiffGrade.'" style=" width:120px" placeholder="">输入类似12223的掉档值<br>';
            echo '随机档<input type=text name="petRandomGrade" value="'.$petRandomGrade.'" style=" width:120px" placeholder="22222">输入类似12223的随机档，可空<br>';
            echo '加点方式<input type=text name="addBP" value="'.$addBP.'" style=" width:120px" placeholder="0,0,0,0,0">按照顺序输入加点，默认不加<br>';
        }
        ?>
        <input type = "submit" value = "提交" >
        <?php
        if ($result != ''){
            echo '<p>模拟结果</p>';

                echo $result;

        }
        ?>
    </form>
</body>
