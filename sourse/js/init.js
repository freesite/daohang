/*
*
*@Daliu
*
*121.42.144.230
*/
/*配制*/
var config = {
	domain:"http://www.freesite.cc/index.php/welcome/",
	currentPart:''
}
//全局id用于与后台数据交互，用户个人id用于前端界面效果控制
var system ={
	currentPartId:'',//当前正打开窗口的全局id
	zIndex:'',
	tool:'',
	toolstate:'off',
	openingWin:'',//当前正打开窗口的个人id
	openWin:new Array(),//记录打开窗口的用户个人id
	openWinpartId:new Array(),//记录打开窗口的全局id
	nickName:''
}
var lib = {
	getSession:getSession,
	select:select,
	drag:drag,
	drop:drop,
	close:close,
	open:open,
	getParts:getParts,
	getLinks:getLinks,
	openAddLinks:openAddLinks,
	trash:trash,
	activeWin:activeWin,
	openSetting:openSetting,
	resortLink:resortLink,
	edit:edit
}

$(document).ready(function(){
$("[name='state']").bootstrapSwitch();
lib.getSession();
lib.drag();
lib.drop();
lib.close();
lib.getParts();
lib.activeWin();
lib.openSetting();
});

//重新为链接排序
//ui为$(this)
//model值为0或1，0代表两个模块状态下的第一个模块，1代表第二个模块
function resortLink(event,ui,model){
	var sort = ui.sortable("toArray",{attribute:"data-id"});
	var partId = system.openWinpartId[model];
	$.post(config.domain+"resortLink",{sort:sort,partId:partId},function(data){
		//alert(system.openWin[model]);
		//alert($("#win-"+system.openWin[model]+".win-content").html());
		//此处需要隐藏"还没有添加任何内容哦！"这句话
	});
}

//启动编辑功能，使链接可在模块间移动
function edit(){
	//1.先验证内存中的工具是不是当前点击工具
	//2.验证工具是打开还是关闭状态
	//3.绑定事件，在事件中再做一次验证
	//工具有两种状态，从第一次点击工具进入和从别的工具进入
	
		if(system.tool=='edit')
		{
			if(system.openWin.length<=2)
			{
				var clickList = $(".win-list");
				clickList.hover(function(){
					var _this = $(this);
					_this.css({"cursor":"move"});
						});
				var length = system.openWin.length;
				switch(length){
					case 1:
						$("#win-"+system.openWin[0]+" .win-content").sortable({
							revert: true,
							zIndex:2,
							cursor:"move",
							update:function(event,ui){
								lib.resortLink(event,$(this),0);
							}});
					break;
					case 2:
						$("#win-"+system.openWin[0]+" .win-content").sortable({
							"connectWith":"#win-"+system.openWin[1]+" .win-content",
							revert: true,
							zIndex:2,
							cursor:"move",
							update:function(event,ui){
								lib.resortLink(event,$(this),0);
							}});
						$("#win-"+system.openWin[1]+" .win-content").sortable({
							"connectWith":"#win-"+system.openWin[0]+" .win-content",
							revert: true,
							zIndex:2,
							cursor:"move",
							update:function(event,ui){
								lib.resortLink(event,$(this),1);
							}});
					break;
					default:
						$("#win-"+system.openWin[0]+" .win-content").sortable({
							revert: true,
							zIndex:2,
							cursor:"move",
							update:function(event,ui){
								lib.resortLink(event,$(this),0);
							}});
					break;
				}
			}
			else
			{
				//还需要关闭链接移动功能
				//通过限制后面的模块不打开保证其功能可用
				
				$("#win-"+system.openingWin).hide();
				var length = system.openWin.length;
				for(var i=0;i<length;i++)
				{
					if(system.openWin[i]==system.openingWin)
					{
						system.openWin.splice(i,1);
						system.openWinpartId.splice(i,1);
					}
				}
				alert("目前只支持2个模块同时管理链接，您已经打开了两个模块！");
			}
		}
		var edit = $(".edit");
		edit.unbind("click").bind("click",function(){
			var tool = 'edit';
			if(system.tool===tool)
			{
				//相同则切换本工具状态，取消工具
				system.tool='';
				$(".win-content").sortable();
				$(".win-content").sortable("destroy");
				var clickList = $(".win-list");
				clickList.hover(function(){
					var _this = $(this);
					_this.css("cursor","pointer");
					});
			}
			else
			{
				if(system.openWin.length<=2)
				{
					//首先要关闭已经打开的功能，如：删除链接功能
					
					
					system.tool = tool;
					//不同，然后直接开始操作，取消之前的当前事件
					var clickList = $(".win-list");
					clickList.unbind();
					clickList.hover(function(){
						var _this = $(this);
						_this.css({"cursor":"move"});
						//取消链接上之前绑定的事件并还原url值
						//原定要在此处还原url值，经仔细分析决定在鼠标移出时即立刻还原url的值和去除事件
						/*var url = _this.data("url");
						if(url!='')
						{
							//alert(url);
							_this.attr({"href":url,"onclick":""});
						}*/
							});
							
					var length = system.openWin.length;
					switch(length){
							case 1:
								$("#win-"+system.openWin[0]+" .win-content").sortable({
									revert: true,
									zIndex:2,
									cursor:"move",
									update:function(event,ui){
										lib.resortLink(event,$(this),0);
									}});
							break;
							case 2:
								$("#win-"+system.openWin[0]+" .win-content").sortable({
									"connectWith":"#win-"+system.openWin[1]+" .win-content",
									revert: true,
									zIndex:2,
									cursor:"move",
									update:function(event,ui){
										lib.resortLink(event,$(this),0);
									}});
								$("#win-"+system.openWin[1]+" .win-content").sortable({
									"connectWith":"#win-"+system.openWin[0]+" .win-content",
									revert: true,
									zIndex:2,
									cursor:"move",
									update:function(event,ui){
										lib.resortLink(event,$(this),1);
									}});
							break;
							default:
								$("#win-"+system.openWin[0]+" .win-content").sortable({
									revert: true,
									zIndex:2,
									cursor:"move",
									update:function(event,ui){
										lib.resortLink(event,$(this),0);
									}});
							break;
						}
					}
				else
				{
					alert("目前只支持2个模块同时管理链接，请先关闭多余的模块！");
				}
			}
		});
	
}
//向数据库提交调整顺序后的链接

//打开设置框
function openSetting(){
	$("#cog").click(function(){
		$("#win-setting").show();
	});
}
//删除链接
//在获取模块链接功能中被调用
function trash(){
	//如果之前是打开的这个功能，则打开新窗口时还初始化删除功能
	if(system.tool==='trash')
	{
		var clickList = $(".win-list");
		clickList.hover(function(){
			var _this = $(this);
			_this.css({"cursor":"url(../sourse/images/delete.ico),auto"});
			var url = _this.attr("href");
			_this.attr({"href":"javascript:;","onclick":"return false;"});
			_this.unbind("mouseleave").bind("mouseleave",function(){
				$(this).attr({"href":url,"onclick":""});
			});
			clickList.unbind("click").bind("click",function(){
				var id = $(this).data("id");
				$(this).remove();
				$.post(config.domain+"delLinks",{id:id},function(data){//alert(data);
					
				});
			});
			});
	}
	var trash = $(".trash");
	//为工具按钮绑定删除链接功能
	trash.unbind("click").bind("click",function(){
		var tool = 'trash';
		
		if(system.tool===tool)
		{
			//若已经打开工具则切换本工具状态
			system.tool = '';
			var clickList = $(".win-list");
			clickList.css("cursor","pointer");
			//清除所有绑定事件
			clickList.unbind();
		}
		else
		{
			//取消排序功能
			$(".win-content").sortable();
			$(".win-content").sortable("destroy");
			
			//若还没打开工具则表示本次点击为打开此功能，进入删除链接功能
			system.tool = tool;
			var clickList = $(".win-list");
			clickList.hover(function(){
				var _this = $(this);
				//此处要针对单个链接设置才能保证鼠标效果不出错
				_this.css({"cursor":"url(../sourse/images/delete.ico),auto"});
				var url = _this.attr("href");
				_this.data("url",url);
				_this.attr({"href":"javascript:;","onclick":"return false;"});
				_this.unbind("mouseleave").bind("mouseleave",function(){
					$(this).attr({"href":url,"onclick":""});
					//alert($(this).attr("href"));
				});
				clickList.unbind("click").bind("click",function(){
					var id = $(this).data("id");
					$(this).remove();
					$.post(config.domain+"delLinks",{id:id},function(data){//alert(data);
						
					});
				});
				});
		}
	});
}
/*获取session*/
function getSession(){
	$.post(config.domain+"getSession",function(data){
		if(data==='')
		{
			data="您还未登录";
		}
		lib.select("#showUserName").innerHTML='<span class="glyphicon glyphicon-user"></span>&nbsp '+data+'<span class="caret"></span>';
	
	});
}
//选择器，目前只能选#id和.class
function select(string){
    var isId = new RegExp("#");
    var isClass = new RegExp(".");
    if(isId.test(string)===true)
    {
        var idArray = string.split("#");
        var id = idArray[1];
        return document.getElementById(id);
    }
    else if(isClass.test(string)===true)
    {
        var classArray = string.split(".");
        var thisClass = classArray[1];
        var btn = document.getElementsByTagName("*");
        for(var i=0 ; i<btn.length ; i++)
        {
            if(btn[i].className===thisClass)
            {
                return btn[i];
                break;
            }
        }
    }
    else
    {
        alert("不是id选择");
    }
}
//drag grid: [ 50, 20 ],
function drag(){
	$("body").droppable();
	$(".win").draggable({
  zIndex: 100
});
}
//drop
function drop(){
	
}
//关闭当前窗口
function close(){
	var window = $(".closes");
	window.on("click",function(){
		$(this).parent().parent().parent().parent().hide(300);
		var part = $(this).data("part");
		var length = system.openWin.length;
		for(var i=0;i<length;i++)
		{
			if(system.openWin[i]==part)
			{
				system.openWin.splice(i,1);
				system.openWinpartId.splice(i,1);
			}
		}
	});
}
//打开当前窗口
function open(){
	var graid = $(".graid-each");
	graid.on("click",function(){
		var object_this = $(this);
		var part = 	object_this.data("part");
		system.openingWin = part;
		var partsId = object_this.data("partsid");
		var title = object_this.children('.caption').html();
		config.currentPart = part;
		system.currentPartId = partsId;
		var id  = "#win-"+part;
		var win_this = $(id);
		win_this.show(300)
		$(id+" .title").html(title);
		$(id+" .closes").data("part",part);
		$(id+" .plus").data("partId",partsId);
		$(id+" .plus").data("part",part);
		lib.getLinks(partsId);
		lib.openAddLinks();
		
		$(".win").css("z-index","0");
		win_this.css("z-index","1");
		//用数组记录窗口是否打开，便于链接管理部分控制只有两个模块同时开启
		var length = system.openWin.length;
		var isIn = false;
		for(var i=0;i<length;i++)
		{
			if(part==system.openWin[i])
			{
				isIn =true;
				break;
			}
		}
		if(isIn==false)
		{
			system.openWin.push(part);
			system.openWinpartId.push(partsId);
		}
	});
}
//将当前活动窗口置顶
function activeWin()
{
	var win = $(".win");
	win.mousedown(function(){
		win.css("z-index","0");
		$(this).css("z-index","1");
	});
}
//获取窗口中的所有链接
function getLinks(partsId){
	$.post(config.domain+"getLinks",{partsId:partsId},function(data){
		var _data = JSON.parse(data);
		var length = _data.length;
		if(length!=0)
		{
			var html = '';
			for(var i=0;i<length;i++)
			{
				var icon = _data[i]['pic'];
				var links = _data[i]['links'];
				if(icon==='')
				{
					icon = links+'favicon.ico';
				}
				html+='<a class="win-list"data-part="'+_data[i]['partId']+'"data-id="'+_data[i]['linksId']+'"data-url="" href="'+links+'"onclick=""target="_blank"><image src="'+icon+'"width="16px"height="16px"/>&nbsp'+_data[i]['name']+'</a>';
			}
			$("#win-"+config.currentPart+" .win-content").html(html);
		}
		else
		{
			//$("#win-"+config.currentPart+" .win-content").html("<p class='hock-linkNull'>还没有添加任何内容哦！</p>");
		}
		//检查是否进入链接移动功能
		lib.edit();
		//检查是否进入链接删除功能
		lib.trash();
	});
}
//打开添加链接模态框提交链接
function openAddLinks(){
	
	$(".plus").unbind("click").bind("click",function(){
		
		$('#myModal').modal('show');
		system.currentPartId = $(this).data("partId");
		config.currentPart = $(this).data("part");
		var saveLinks = $("#saveLinks");
		saveLinks.unbind("click").bind('click',function(){
			var linkName = $("#linkname").val();
			var links = $("#links").val();
			var partsId = system.currentPartId;
			$.post(config.domain+"addLinks",{partsId:partsId,linkName:linkName,links:links},function(data){
				if(data==='true')
				{
					alert("添加成功！");
					$('#myModal').modal('hide');
					$("#links").val('');
					$("#linkname").val('');
					//刷新窗口中的链接列表
					//alert(system.tool);
					lib.getLinks(config.currentPart);
				}
				else if(data==='notlogin')
				{
					
				}
				else
				{
					
				}
			});
		});
	});
	
}
//获取所有用户已经打开的模块
function getParts()
{
	$.post(config.domain+'getParts',function(data){
		var _data = JSON.parse(data);
		var html = '';
		var length = _data.length;
		for(var i= 0;i<length;i++)
		{
			html+='<a href="javascript:;"class="graid-each" data-part="'+_data[i]['sort']+'"data-partsid="'+_data[i]['partsId']+'"><div class="caption">'+_data[i]['title']+'</div></a>';
		}
		$(".graid").html(html);
		lib.open();
		});
}