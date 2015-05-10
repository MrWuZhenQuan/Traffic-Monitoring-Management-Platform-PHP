/**
 * Created by Joseph on 2014/6/17.
 */
$(document).ready(function(){
    init();
});
function init(){
    var $table = $("#menu-table");
    addSubMenuClickable();
    bindEventLitener();
    $("#add-top-menu").click(function(){
        var $tr = createTopMenu();
        $table.append($tr);
        addSubMenuClickable();
        bindEventLitener();
    });
    //保存所有菜单
    $("#save-all-menu").click(function(){
        var menuData = getAllMenuData();

        $("#save-menu-input").val(JSON.stringify(menuData));
        // alert("save-submit:\n" + $("#save-menu-input").val());
    });
    //生成微信菜单
    $("#generate-menu").click(function(){
        var allAcitivedMenus = getAcitivedMenu();
        if(allAcitivedMenus === false){
            showAlert();
            event.preventDefault();
        }
        if(allAcitivedMenus ===undefined || allAcitivedMenus.length===0){
        }
        sortAllMenu(allAcitivedMenus);
        $("#generate-menu-input").val(transferToWechatJson(allAcitivedMenus));

       //alert("generate-submit:\n" + $("#generate-menu-input").val());
    });
}

//创建一级菜单
function createTopMenu(menuObj){
    var $tr =  $('<tr class="top-menu"></tr>');
    var $squenceTd =  $('<td> <input type="text" class="sequence" /></td>');
    var $nameTd = $('<td><input type="text"/><a class="add-menu"> <span class="glyphicon glyphicon-plus"></span></a></td>');
    var $keyOrLinkTd = $('<td><input type="text" /></td>');
    var $activedTd = $('<td><input type="checkbox" checked="checked"></td>');
    var $deleteTd = $('<td><a class="btn btn-default delete"><span class="glyphicon glyphicon-remove"></span> 删除</a></td>');
    if(menuObj!=undefined) {
        $squenceTd.find("input").val(menuObj.sequence);
        $nameTd.find("input").val(menuObj.name);
        $keyOrLinkTd.find("input").val(menuObj.content);
        $activedTd.find("input").attr("checked", menuObj.isActived);
    }
    $tr.append($squenceTd);
    $tr.append($nameTd);
    $tr.append($keyOrLinkTd);
    $tr.append($activedTd);
    $tr.append($deleteTd);
    return $tr;
}

//创建二级菜单
function createSubMenu(subMenuObj){
    // alert("createSubMenu" + JSON.stringify(subMenuObj));
    var $tr = $('<tr class="sub-menu"></tr>');
    var $squenceTd = $('<td><input type="text" class="sequence" /></td>');
    var $nameTd = $('<td><i class="board"></i> <input type="text"/></td>');
    var $keyOrLinkTd = $('<td><input type="text"/></td>');
    var $activedTd = $('<td><input type="checkbox" checked="checked"/></td>');
    var $deleteTd = $('<td><a class="btn btn-default delete"><span class="glyphicon glyphicon-remove"></span> 删除</a></td>');
    if(subMenuObj!=undefined) {
        $squenceTd.find("input").val(subMenuObj.sequence);
        $nameTd.find("input").val(subMenuObj.name);
        $keyOrLinkTd.find("input").val(subMenuObj.content);
        $activedTd.find("input").attr("checked", subMenuObj.isActived);
    }
    $tr.append($squenceTd);
    $tr.append($nameTd);
    $tr.append($keyOrLinkTd);
    $tr.append($activedTd);
    $tr.append($deleteTd);
    return $tr;
}

//为添加子菜单按钮绑定事件
function addSubMenuClickable(group){
    $(".add-menu").unbind();
    $(".add-menu").click(function(){
        var $tr = $(this).parent().parent();
        $tr.after(createSubMenu(group));
        bindEventLitener();
    });
}


//获取所有菜单数据，包括激活和未被激活的，保存在一个
function getAllMenuData(){
    var allData = new Array();
    var menuGroup = null;
    var menuItemArr = null;
    $("#menu-table tbody tr").each(function(index){
        var $tr = $(this);
        var $tds = $(this).find("td");
        var sequence = $tds.eq(0).find("input").val();
        var name = $tds.eq(1).find("input").val();
        var content = $tds.eq(2).find("input").val();
        var isActived = $tds.eq(3).find("input").is(':checked');
        var menuItem = new MenuItem(sequence, name, content, isActived);
        //如果是一级菜单
        if($tr.hasClass("top-menu")){
            menuGroup = new Object();
            menuItemArr = new Array();
            menuGroup.topMenu = menuItem;
            menuGroup.menuItemArr = menuItemArr;
            allData.push(menuGroup);
        }
        //如果是二级菜单
        if($tr.hasClass("sub-menu")){
            menuItemArr.push(menuItem);
        }
    });
    return allData;
}

//获得被激活的菜单以便生成微信菜单
function getAcitivedMenu(){
    var allData = new Array();
    var menuGroup = null;
    var menuItemArr = null;
    var topMenuCount = 0;
    var subMenuCount = 0;
    var isLegal = true;
    $("#menu-table tbody tr").each(function(index){
        var $tr = $(this);
        var $tds = $(this).find("td");
        var sequence = $tds.eq(0).find("input").val();
        var name = $tds.eq(1).find("input").val();
        var content = $tds.eq(2).find("input").val();
        var isActived = $tds.eq(3).find("input").is(':checked');

        var menuItem = new MenuItem(sequence, name, content, isActived);
        //如果是一级菜单
        if($tr.hasClass("top-menu")){
            menuGroup = new Object();
            menuItemArr = new Array();
            subMenuCount = 0;
            if(isActived === false) return;
            topMenuCount++;
            if(topMenuCount>3) {
                isLegal = false;
                return false;
            }
            menuGroup.topMenu = menuItem;
            menuGroup.menuItemArr = menuItemArr;
            allData.push(menuGroup);
        }
        //如果是二级菜单
        if($tr.hasClass("sub-menu")){
            if(isActived === false) return;
            subMenuCount++;
            if(subMenuCount>5){
                isLegal = false;
                return false;
            }
            menuItemArr.push(menuItem);
        }
    });
    if(!isLegal)
        return false;
   return allData;
}

function sortAllMenu(allAcitivedMenus){
    sortTopMenu(allAcitivedMenus);
}

function sortTopMenu(menuGroups){
    var sequenceArr = new Array();
    var i;
    //将菜单项数组的sequence字段取出并构成数组
    for(i=0; i<menuGroups.length; i++){
        sequenceArr[i] =  parseInt(menuGroups[i].topMenu.sequence);
        //排序
        sortSubMenuArr(menuGroups[i].menuItemArr);
    }
    sortArrByIndexArr(sequenceArr, menuGroups);
}
//排序菜单项
function sortSubMenuArr(menuItemArr){
    var sequenceArr = new Array();
    var i;
    //将菜单项数组的sequence字段取出并构成数组
    for(i=0; i<menuItemArr.length; i++){
        sequenceArr[i] =  parseInt(menuItemArr[i].sequence);
    }
    sortArrByIndexArr(sequenceArr, menuItemArr);
}

/**
 * 根据一个索引数组来排序另外一个数组
 * @param indexArr
 * @param objArr
 */

function sortArrByIndexArr(indexArr, objArr){
    var i,j;
    //直接插入排序
    for(i=1; i<indexArr.length; i++){
        for(j=i-1; j>=0; j--){
            var tmpNumber;
            var tmpObject;
            if(j===0){
                if(indexArr[i]<indexArr[j]){
                    //交换显示顺序数组元素
                    tmpNumber = indexArr[i];
                    indexArr.splice(i,1);
                    indexArr.splice(j, 0, tmpNumber);

                    //交换菜单数组元素
                    tmpObject = objArr[i];
                    objArr.splice(i,1);
                    objArr.splice(j, 0, tmpObject);
                }
            }else{
                if(indexArr[i]<indexArr[j] && indexArr[i]>=indexArr[j-1]){
                    tmpNumber = sequenceArr[i];
                    sequenceArr.splice(i,1);
                    sequenceArr.splice(j, 0, tmpNumber);

                    //交换菜单数组元素
                    tmpObject = objArr[i];
                    objArr.splice(i,1);
                    objArr.splice(j, 0, tmpObject);
                }
            }
        }
    }
}
function showAlert(){
    var $alert = $(".alert.alert-danger");
    $alert.fadeIn(500,function(){
        setTimeout(function(){$alert.fadeOut();}, 4000);
    });
}

/**
 * 一个菜单对象
 * @param sequence
 * @param name
 * @param content
 * @param isActived
 * @constructor
 */
function MenuItem(sequence, name, content, isActived){
    this.sequence = sequence;
    this.name = name;
    this.content = content;
    this.isActived = isActived;
}
/**
 * 菜单项组，包括一级菜单和二级菜单
 * @param topMenu  一级菜单
 * @param menuItemArr 二级菜单
 * @constructor
 */
function MenuGroup(topMenu, menuItemArr){
    this.topMenu = topMenu;
    this.menuItemArr = menuItemArr;
}

/**
 * 确保input只能输入数字
 */
function IsNum(e) {
    var k = window.event ? e.keyCode : e.which;
    if (((k >= 48) && (k <= 57)) || k == 8 || k == 0) {
    } else {
        if (window.event) {
            window.event.returnValue = false;
        }
        else {
            e.preventDefault(); //for firefox
        }
    }
}

/**
 * 绑定事件监听器，每添加一个新的菜单项都要从新绑定一次
 */
function bindEventLitener(){
    sequenceVaildation();
     deleteClickabale();
}
/**
 * 删除按钮
 */
function deleteClickabale(){
    $("a.delete").unbind();
    $("a.delete").click(function(){
        var $tr = $(this).parent().parent();
        $tr.remove();
    });
}

//保证顺序input只能输入数字
function sequenceVaildation(){
    $(".sequence").unbind();
    $(".sequence").keypress(function(e){
        IsNum(e);
    });
}

//转换成后台需要的json
function transferToWechatJson(menuGroupArrJson){
    var menuGroupArr = eval(menuGroupArrJson);
    var buttonArr = new Array();
    for(var i=0; i<menuGroupArr.length; i++){
        var menuGroup = menuGroupArr[i];
        var obj = new Object();
        obj.name = menuGroup.topMenu.name;
        //没有二级菜单
        if(menuGroup.menuItemArr==undefined || menuGroup.menuItemArr.length<=0){
            //如果是关键字类型
            if(isKey(menuGroup.topMenu.content)){
                obj.type = "click";
                obj.key = menuGroup.topMenu.content;
            }else{//url类型
                obj.type = "view";
                obj.url = menuGroup.topMenu.content;
            }
        }else{//拥有二级菜单
            var subMenuArr = new Array();
            for(var j=0; j<menuGroup.menuItemArr.length; j++){
                //二级菜单微信对象
                var subMenuObj = new Object();
                subMenuObj.name = menuGroup.menuItemArr[j].name;
                //
                if(isKey(menuGroup.menuItemArr[j].content)){
                    subMenuObj.type = "click";
                    subMenuObj.key = menuGroup.menuItemArr[j].content;
                }else{
                    subMenuObj.type = "view";
                    subMenuObj.url = menuGroup.menuItemArr[j].content;
                }
                subMenuArr.push(subMenuObj);
            }
            obj.sub_button = subMenuArr;
        }
        buttonArr.push(obj);
    }
    var wechatJson = new Object();
    wechatJson.button = buttonArr;
    return JSON.stringify(wechatJson)
}

//是否关键字类型
function isKey(str){
    //链接类型
    if(str.indexOf("http")===0){
        return false;
    }//关键字类型
    else{
        return true;
    }
}

//进入菜单页面时初始化菜单
function initMenu(menuJson) {
    // alert(menuJson);
    var $tbody = $("tbody");
    var menuGroupArr = eval(menuJson);
    if(menuGroupArr == undefined || menuGroupArr == null)
        return;
    for(var i=0; i<menuGroupArr.length; i++){
        var menuGroup = menuGroupArr[i];
        var topMenuObj = menuGroup.topMenu;
        var subMenuArr = menuGroup.menuItemArr;
        var $topMenuTr = createTopMenu(topMenuObj);
        $tbody.append($topMenuTr);
        for(var j=0; j<subMenuArr.length; j++){
            var $subMenuTr = createSubMenu(subMenuArr[j]);
            $tbody.append($subMenuTr);
        }
    }
    deleteClickabale();
}

