<!DOCTYPE html>
<html>
<head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="baidu-site-verification" content="B1LS7CXrmW" />
        <title>Next Generation EquipCalc - CgCal.com</title>
        <meta charset="utf-8">

</head>
<body>
        <h3>欢迎来到新一代装备模拟器>CGCal<</h3>
        <h4>感谢XXX提供宠物资料数据<br></h4>
        <h4>代码托管在：<a href="https://github.com/yanggs07/cgcal">github</a></h4>
        <h3>宠物计算器，<a href="/">点我看看。</a></h3>
        <h3>宠物模拟器，<a href="/cgPetSimulator/">点我看看。</a></h3>

    <form action= "/" method = "post" >
        装备类别<select name="equipType" onchange="submit();">
            <option value ="xue" <?php echo ($equipType=='xue'?'selected':'');?> >纯血</option>
            <option value ="gong" <?php echo ($equipType=='gong'?'selected':'');?> >纯攻</option>
            <option value ="fang" <?php echo ($equipType=='fang'?'selected':'');?> >纯防</option>
            <option value ="min" <?php echo ($equipType=='min'?'selected':'');?> >纯敏</option>
            <option value ="mo" <?php echo ($equipType=='mo'?'selected':'');?> >纯魔</option>
            <option value ="no" <?php echo ($equipType=='no'?'selected':'');?> >不加</option>
            <option value ="hun" <?php echo ($equipType=='hun'?'selected':'');?> >混加</option>
        </select>
        装备名称<input type=text id ="petName" name="petName" value="<?php echo $petName?>" style=" width:280px" placeholder="输入宠物名称或拼音简称，如‘螳螂’，‘螳’，‘tl’均可"><br>
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
        宠物档次<input type=text name="petGrade" value="<?php echo $petGrade?>" style=" width:120px" placeholder="档次将被自动抓取" readonly>（暂不支持自定义）<br>
        宠物等级<input type=text name="petLv" style=" width:40px" placeholder="1" value="<?php echo $petLv?>" onchange="submit();">请输入宠物等级<br>
        当前数据<input type=text id="petData" name="petData" value="<?php echo $petData?>" style=" width:280px" placeholder="输入血魔攻防敏，以空格分开，可加精神回复"><br>

        余点：<input type=text name="rBP" style=" width:20px" placeholder="0" value="<?php echo $rBP?>" <?php echo $rBPprop?> ><?php echo ($addBPMethod=='no'?'请确认低级未加的宠物的余点！':'');?><br>
        <br>
        <input type = "submit" value = "提交" >
        <br>
        <?php
        if ($petResult != ''){
            echo $petResult['type'];
            echo '<br>';
            foreach ($petResult['view'] as $key => $result) {
                echo $result;
                echo '<br>';
            }
        }
        ?>

        <script type="text/javascript">
        document.getElementsByName("<?php echo $focus?>")[0].focus();
        </script>
    </form>
</body>
