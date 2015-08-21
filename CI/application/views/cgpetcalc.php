<html>
<head>
        <meta name="baidu-site-verification" content="B1LS7CXrmW" />
        <title>Next Generation PetCalc - CgCal.com</title>
        <meta charset="utf-8">

</head>
<body>
        <h3>欢迎来到新一代魔力宝贝算档器>CGCal<</h3>
        <h4>感谢魔力百科提供宠物资料数据<br>
        欢迎关注魔力百科更好（看）的算档器！</h4>
        <h4>代码托管在：<a href="https://github.com/yanggs07/cgcal">github</a></h4>
        <h3>亲！最近刚刚上线了宠物模拟器，<a href="/cgPetSimulator/">点我看看。</a></h3>

    <form action= "/" method = "post" >
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
        ?>
        宠物档次<input type=text name="petGrade" value="<?php echo $petGrade?>" style=" width:120px" placeholder="档次将被自动抓取">（暂不支持自定义）<br>
        宠物等级<input type=text name="petLv" value="1" style=" width:40px"readonly="yes">（暂时只支持1级宠物哦）<br>
        当前数据<input type=text name="petData" value="<?php echo $petData?>" style=" width:280px" placeholder="输入血魔攻防敏，以空格分开，可加精神回复"><br>
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
