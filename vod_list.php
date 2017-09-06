<?php 
	/**
	 * Description : 오락서비스 > VOD LIST
	 * Date : 2015.11.18
	 * Version : 1.0
	 */
	//일본한국(日韩）
// 	error_reporting(E_ALL);
// 	ini_set('display_errors', 'On');
	$root_path = $_SERVER["DOCUMENT_ROOT"];
	include_once $root_path.'/Lib/Conf/Config.php';
	include_once $root_path.'/Comm/isLogin.php';
	//임시 테스트용 코드
	/*if ($G_MEMBER_ID == 'testUser' || $G_MEMBER_ID == '18552441004' || $G_MEMBER_ID == '18317035205') {
	// if ($G_MEMBER_ID == '18552441004' || $G_MEMBER_ID == '18317035205') {
		echo "<script>location.href='vod_list_tmp.php';</script>";
		exit;
	}*/
		
	/*echo "<script>location.href='vod_list_tmp.php';</script>";
	exit;*/
	$strLi01 = '';
	$strLi02 = '';
	$strLi03 = '';
	$strLi04 = '';
	$nNowYear = (int)date("Y");
	$nYearCnt =0;
	for($y=$nNowYear;$y>2010;$y--){
		$strLi01 .='<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,\'\',\'3\',\''.$y.'\');nSelTV(this,1,2);"> - '.$y.'</a></li>'."\r\n";
		$strLi02 .='<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,\'\',\'3\',\''.$y.'\');nSelTV(this,2,2);"> - '.$y.'</a></li>'."\r\n";
		$strLi03 .='<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,\'\',\'3\',\''.$y.'\');nSelTV(this,3,2);"> - '.$y.'</a></li>'."\r\n";
		$strLi04 .='<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,\'\',\'3\',\''.$y.'\');nSelTV(this,4,2);"> - '.$y.'</a></li>'."\r\n";
		$nYearCnt++;
	}//end for
	
	$strWebGame = "nAppStart(3)";
	if($G_MEMBER_HOS==8)$strWebGame = "setWebViewMove('http://sda.4399.com/4399swf/upload_swf/ftp19/ssj/20160711/b6/game.html');";
?>
<!doctype html>
<html lang="zh-cn">
	<head>
		<?include_once $root_path.'/Comm/htm_header.php';?>
		<script>
			var gPageNo = 1;
			var gMode = 10;
			var gInit = true;
			var oScrollTimer = null;
			var isCalling = false;
			var gKeyword = "";
			var gCate = "";
			var gCateName = "";
			var gYearCnt = <?=$nYearCnt?>;
			var gOrderMode = 1;

			//서브 메뉴 열림 확인
			var gCateNo = 0;
			var gCateStep = 0;
			var gCateOpen = 0;
			
			//결제관련 
			var bBuyItem=0;//영화가 아닌 단건구매시에는 _item의 고유번호 
			var nVodSeq = 0;
			function setInitIPList(nItem,page,search,cate,catename){
				$("#pop_buy").hide();
				if(page==1){
					gInit = true;
				}else{
					gInit = (nItem==gMode) ? false : true;
				}	

				if(gInit)setRightMenuInit(nItem,search,cate,catename);
				gPageNo = page;
	      		if(gPageNo <1)gPageNo=1;
				
	      		if($.trim(search)=='')gKeyword='';		
				var sData = {nMode : nItem , page : gPageNo, keyword : gKeyword , strCate : cate , strCateName : catename ,orders : gOrderMode};
				$.ajax({
					type : "post", data : sData , url : 'getVodList3.php',
					error : function(request,status,error) { /*alert('Data Load Fail Error :'+reqeust.responseText);*/ },context : this,
				    success: function(data) {
				    	isCalling = false;
				    	clearTimer();
				      	var oJson = eval("("+data+")");	
				      	if(oJson.code==1){
				      		setIPList(oJson.other);				      		
				      		gMode = nItem;
				      		//$("#pop_buy").hide();
				      		$("#pop_buy").show();
				      		gCate = cate;
				      		gCateName = catename;				      		
				      	}else{
					      	if(gPageNo>1)gPageNo = gPageNo-1;
				      		//$("#pop_buy").hide();
				      		$("#pop_buy").show();
					      	alert(oJson.msg);
				      	}		      	
				    }
				});
			}//end fnc

			function setIPList(oJson2){
					
				try{			
					var strLi="";		
					if(oJson2!=null){						
						var oJson = eval("("+oJson2+")");
						var i=0,nLen = oJson.length;
						var iptvTitle , vod_seq ,strImg;
						var cssOn = "",strLink="";
						var strDesc = "",strDesc2="";
						for(i;i<nLen;i++){					
							iptvTitle = oJson[i]['workname'];	
							if(jQuery.trim(iptvTitle).length>6)iptvTitle = jQuery.trim(iptvTitle).substring(0, 6)+"...";
							vod_seq = oJson[i]['id'];
							if(iptvTitle=="undefined" || typeof(iptvTitle)==undefined || $.trim(iptvTitle)=="")continue;
							
							strLink = '<a href="javascript:void(0)" onclick="nVod('+vod_seq+');\">';
							strLi +='<li><figure>';
							
							if(parseInt(oJson[i]['isFree'],10)==2){
								strLi +='<div class="Banner_pay01"><span> VIP </span></div>';
							}else{
								strLi +='<div class="Banner_pay02"><span>免费</span></div>';
							}
														strLi += strLink;
							strDesc = oJson[i]["introduction"];				
									
							if($.trim(strDesc)!=""){
								strDesc2 = "";
								if(jQuery.trim(strDesc).length>8)strDesc2 = "...";
								strDesc = jQuery.trim(strDesc).substring(0, 8)+strDesc2;
							}else{
								strDesc = "&nbsp;";
							}

							strImg = oJson[i]['img01'] !="" ? oJson[i]['img01'] : oJson[i]['img02'];
							strLi +='<img src="'+strImg+'" alt="movie logo" style="width:272px;height:382px;border:1px solid #ccc;"/></a>';

							strLi +='<div class="txtBox"><span class="rtxt">HOT : '+numberWithCommas(oJson[i]['hot'])+'&nbsp;</span></div>';
							
							strLi +='</figure><dl>';
							strLi +='<dt>'+strLink+iptvTitle+'</a></dt><dd>'+strDesc+'</dd></dl></li>';					
						}
					}
					if(!gInit)strLi = $("#channel_list").html()+strLi;
					$("#channel_list").html(strLi);
					if(gPageNo>1)setScroll(2);
				}catch(e){
					//alert("Error :"+e);
					alert("由于网络问题，请稍候使用");
				}
			}

			
			function numberWithCommas(x) {
			    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}
			function setScroll(mode){
				var h = $(document).scrollTop();
				h = h+445;
				if(mode==1) h=0;
				$("html, body").animate({ scrollTop: h }, 1000);
			}

			function nSelTV(t ,cate , step){
				$("#iptv_channel > li").each(function(index){
		                $(this).removeClass("on");
		        }); 
				var op = $(t).parent();
				op.addClass("on");

				if(step>1){
					$(".lv1").each(function(i){
						if(cate==3 || cate==4){
							if(cate==3 && i==3)$(this).addClass("on");
							if(cate==4 && i==2)$(this).addClass("on");
						}else{
							if((cate-1) == i)$(this).addClass("on");
						}
					});
				}
			}

			function nSetItem(mode , id , t){
				$("#item_box > a").each(function(index){
					$(this).removeClass("itemBox_on");
				});
				$(t).addClass("itemBox_on");
				bBuyItem = id;
				nVodPlay();
			}		

			function nSetItemBuy(mode , id , t){
				$("#item_box > a").each(function(index){
					$(this).removeClass("itemBox_on");
				});
				$(t).addClass("itemBox_on");
				bBuyItem = id;
				nVodPayment();
			}		

			
			function setPaging(){
				clearTimer();
				isCalling =true;
				var page = gPageNo+1;
				setInitIPList(gMode,page , gKeyword ,gCate ,gCateName);
			}

			function clearTimer(){
				if(oScrollTimer!=null){
					   clearInterval(oScrollTimer);
					   oScrollTimer = null;
				}
			}
			function nVod(ids){
				var sData = {nMode : gMode , id : ids};
				$.ajax({
					type : "post", data : sData , url : 'getVodData3.php',
					//type : "post", data : sData , url : 'getVodData_tmp.php',
					error : function(request,status,error) { /*alert('Data Load Fail Error :'+reqeust.responseText);*/ },context : this,
				    success: function(data) {
				    	isCalling = false;
				    	clearTimer();
				      	var oJson = eval("("+data+")");	
				      	if(oJson.code==1){
				      		$("#content_header").html(oJson.other);
				      		$("#pop_et_pay").show();
				      		//$("body").css({overflow:'hidden'}).bind('touchmove', function(e){e.preventDefault()});
				      		$(".pop_cont").animate({ scrollTop: 0 }, 1000);
				      		$("#buy_now_time").html(oClock.getNowDate());
				      		nVodSeq = ids;
				      	}else{
				      		$("#pop_et_pay").hide(0,hsdComm.backgroundRecovery());
					      	alert(oJson.msg);
				      	}		      	
				    }
				});
			}
			function setClose(){
				$("#pop_et_pay").hide();
				$("body").unbind('touchmove');
				$(document).off(".disableScroll");
				$("#container").css("height","100%");//스크롤 이벤트가 발생되지 않아 다시 넣음
			}

			//구매창 띄우기
			function nVodPayment(){
				$("#pop_et_pay").hide();
				if(gMode==10)bBuyItem = nVodSeq;		
				
				var sData = {nMode : gMode};
				$.ajax({
					type : "post", data : sData , url : 'getVodPayment.php',
					error : function(request,status,error) { },context : this,
				    success: function(data) {
				      	var oJson = eval("("+data+")");	
				      	if(oJson.code==1){
					      	var msg = '<div class="tit_grey">结算分类</div>'+oJson.msg;
					      	$("#payment_list").html(msg);
				      		//$("body").css({overflow:'hidden'}).bind('touchmove', function(e){e.preventDefault()});
				      		bBuyItem = nVodSeq;	
				      		$("#pop_et_pay_pay").show();
				      	}else{
				      		$("#pop_et_pay_pay").hide(0,hsdComm.backgroundRecovery());
					      	alert(oJson.msg);
				      	}		      	
				    }
				});
			}
			//구매 실행
			function nVodBuy(){
				var frm = document.vodForm;
				frm.nVodSeq.value = bBuyItem;
				frm.nCouponSeq.value = $(':radio[name="payment_type"]:checked').val();
				if(parseInt(frm.nCouponSeq.value,10)<1){
					alert("请选择分类");
					return;
				}
				var formData = $("form[name=vodForm]").serialize();

				$.ajax({
					type : "post",
					data : formData,
				 	url : '/Payment/_Custom/pay_vod_qrcode.php',
				 	error : function(e) { alert('Error #'+e.data); },
				    success: function(data) {
					    var oJson = eval("("+data+")");
					    if(oJson.code == 1){
						    $("input[name=paymentSeq]").val(oJson.msg);
							$("#pop_qrcode").find("img.qrcode").attr("src",oJson.other);
						    $("#pop_et_pay_pay").hide(0,hsdComm.backgroundRecovery());
						    $("#pop_qrcode").show();
						    isPaymentPop=true;//결제창 열림 확인
					    }else if(oJson.code == 2){
					    	alert(oJson.msg);
					    	location.href = "/Member/login.php?reUrl="+encodeURIComponent(location.href);
						}else if(oJson.code == 3){
							$("input[name=paymentSeq]").val(oJson.msg);
							setWebviewPay(oJson.other);
					    }else{
							alert(oJson.msg);
					    }
				    }
				});	
			}

			//qr code 읽은 후 닫기 방지 코드 Start ---------------------------------------->
			//결제 관련 창 닫기전 이동시 확인
			var isPaymentPop = false;
			$(window).on("beforeunload" , function(){
				if(isPaymentPop){
					chk_payment_done(1);
					//return  "결제 정보 확인 중입니다. 이동시 결제가 비정상 처리 될 수 있습니다.";
					return '正在确认结算成功状态，关闭可能会导致不能正常处理结算';
				}	
			});

			//결제 완료 체크
			function chk_payment_done(nMode){
				var paySeq = $("input[name=paymentSeq]").val();
				if(paySeq == ""){
					isPaymentPop = false;
					if(nMode==2)document.location.reload();
					return;
				}
				$.ajax({
					type : "post",
					data : {"paySeq" : paySeq},
				 	url : '/Payment/_Custom/pay_chk_order_vod.php',
				 	error : function(e) { alert('Error #'+e.data); },
				    success: function(data) {
				    	isPaymentPop=false;
				    	if(nMode==2){
				    		var oJson = eval("("+data+")");
				    		 if(oJson.code == "1"){
							    	isPaymentPop = false;
							    	location.href = "/Member/my_order_media.php";  
							    }else{
								    if(oJson.msg.indexOf("#NOTPAY")<0)alert(oJson.msg);
									document.location.reload();
							    }
				    	}
				    }
				});	
			}
			//qr code 읽은 후 닫기 방지 코드 End  ---------------------------------------->
			

			function chk_payment(){
				if(confirm("是否已支付完成？")){
					var paySeq = $("input[name=paymentSeq]").val();
					if(paySeq == ""){
						alert("결제 정보가 없습니다.");
						return;
					}

					$.ajax({
						type : "post",
						data : {"paySeq" : paySeq},
					 	url : '/Payment/_Custom/pay_chk_order_vod.php',
					 	error : function(e) { alert('Error #'+e.data); },
					    success: function(data) {
						    var oJson = eval("("+data+")");
							alert(oJson.msg);
						    if(oJson.code == 1){
						    	$("#pop_qrcode").hide();
							    $("#pop_pay").show();
							    setTimeout(function(){ 
							    	location.href = "/Member/my_order_media.php";  
								}, 3000);
						    }else{
								if(confirm("未完成支付，要取消支付吗 ?")){
									location.reload();
								}
						    }
					    }
					});	
					
				}	
			}//end fnc 

			function getURLParameter(name) {
	            return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]);
	        }
	 
	        function getURLParameter2(name) {
	             return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
	        }

	        var isBuyPlay=false;
			function setBuyPlayer(){
				var vodSeq = getURLParameter("vodSeq");
				var vodMode = getURLParameter("vodMode");
				if($.trim(vodSeq)=="" || parseInt(vodSeq,10)<1) vodSeq = getURLParameter2("vodSeq");
				if($.trim(vodMode)=="" || parseInt(vodMode,10)<1) vodMode = getURLParameter2("vodMode");
				vodSeq = parseInt(vodSeq,10);
				vodMode = parseInt(vodMode,10);
				if(vodSeq>0 && vodMode>0){
					gMode = vodMode;
					nVodSeq = vodSeq;
					isBuyPlay = true;
					nVodPlay();
					
				}
			}
			
			function nVodPlay(){
				if(gMode==10)bBuyItem = nVodSeq;
				if(gMode!=10 && bBuyItem==0){
					alert("请选择需要播放的集数后，点击播放");
					return;
				}
// 				alert(gMode+","+bBuyItem);
				var sData = {nMode : gMode , id : bBuyItem};
				$.ajax({
					type : "post", data : sData , url : 'getVodUrl3.php',
					error : function(request,status,error) { /*alert('Data Load Fail Error :'+reqeust.responseText);*/ },context : this,
				    success: function(data) {
				      	var oJson = eval("("+data+")");	
						//alert(oJson.other.adMode);
				      	if(oJson.code==1){
				      		//传其他的参数
				      		var id,nMode,workname1,total_number,play_number,total_time,play_time,workname2,mp4Url,add_time,isFree,nSeq;
				      		$.each( oJson.other.result, function(i, n){
							  id=n.id;
							  nMode=n.nMode;
							  workname1=n.workname1;
							  total_number=n.total_number;
							  play_number=n.play_number;
							  total_time=n.total_time*1000;
							  play_time=n.play_time*1000;
							  workname2=n.workname2;
							  mp4Url=n.mp4Url;
							  add_time=n.add_time;
							  isFree=n.isFree;
							  nSeq = n.nSeq;
							});
				      		var data='{'+
									    '"code": '+oJson.other.code+','+
									    '"msg": "'+oJson.other.msg+'",'+
									    '"mp4Url": "'+oJson.other.mp4Url+'",'+
									    '"result": ['+
									        '{'+
									            '"id": '+id+','+
									            '"nHospitalNo": '+<?php echo $G_MEMBER_HOS;?>+','+
									            '"strUserID": '+<?php echo $G_MEMBER_ID?$G_MEMBER_ID:0;?>+','+
									            '"nMode": '+nMode+','+
									            '"workname1": "'+workname1+'",'+
									            '"total_number": '+total_number+','+
									            '"play_number": '+play_number+','+
									            '"total_time": "'+total_time+'",'+
									            '"play_time": "'+play_time+'",'+
									            '"workname2": "'+workname2+'",'+
									            '"isFree": "'+isFree+'",'+
									            '"mp4Url": "'+mp4Url+'",'+
									            '"add_time": '+add_time+''+
												'"nSeq": '+nSeq+''+
									        '}'+
									    ']'+
									'}';
				      		 nMovie(oJson.other.mp4Url, "vod",oJson.other.adMode,data);
				      		//$("#pop_et_pay").hide(0,hsdComm.backgroundRecovery());
				      		setClose();
				      		if(isBuyPlay){
				      			setTimeout(function(){ 
							    	location.href = "vod_list.php";  
								}, 1000);
				      		}
				      	}else{
				      		alert(oJson.msg);
				      	}		      	
				    }
				});
			}		


			//다음영상 정보 가져오기 
			function getNextMovie(){

				if ( gMode==10 )return;
				if ( nVodSeq <1) {
					alert("Vod Information Nothing!!!");
					return;
				}
				
				var sData = {nMode : gMode , nId : nVodSeq};
				$.ajax({
					type : "post", data : sData , url : 'getVodNext.php',
					error : function(request,status,error) { },context : this,
				    success: function(data) {
				      	var oJson = eval("("+data+")");	
				      	if (oJson.code == 1) {
				      		nMovieNext(oJson.msg,oJson.other);
				      	}   else {
				      		nMovieNext("","");
				      	}   	
				    }
				});
			}	
			
			$(window).scroll(function() {
				evScroll();
			});

			function evScroll(){
				var scrollSize = $(window).scrollTop() + $(window).height();
				   if(typeof(_Bridge) != "undefined")scrollSize = scrollSize+20;
				   if(scrollSize >= $(document).height()) {
					   var isView = $('#pop_et_pay').is(":visible"); 
					   if(!isCalling && !isView){
					   		clearTimer();
					  	 	oScrollTimer =  setInterval(setPaging ,1000);
					   }
				   }
			}

			function nSearch(){
				if($('#pop_search').css('display') == 'none'){
					$("#pop_search").show();
					$("#keyword").focus();
				}else{
					$("#pop_search").hide();
				}
			}

			function setKeyword(){//검색				
				gKeyword = $("#keyword").val();
				if($.trim(gKeyword)==""){
					alert("검색어를 입력하세요.");
					return;
				}
				$("#pop_search").hide();
				setInitIPList(gMode,1 ,gKeyword ,gCate , gCateName);
				$("#keyword").val(' ');
			}

			function nSelSubItem(nMenu , mode , dispMode){
				$(".lv2").hide();
				$(".lv3").hide();
				$(".lv2").each(function(i){
					if(nMenu==1 && i <3)$(this).show();//film
					if(nMenu==2 && i >2 && i<6)$(this).show();//drama
					if(nMenu==3 && i >8)$(this).show();//enter
					if(nMenu==4 && i >5 && i<9)$(this).show();//ani
				});

				 var arCateCnt = new Array(10 ,8 , 13 , 3);
				 var areaCnt = 6;
				 var yearCnt = gYearCnt + 5;//11
				 var iStart = 0 , iEnd =0;
				 var arStart = new Array(0,0,0);
				 var arEnd = new Array(0,0,0);
			     var cmmLiCnt = areaCnt+yearCnt;
				
				 switch(nMenu){
				 case(1) : arStart[0] = 0;arEnd[0] = arCateCnt[0];break;
				 case(2) : arStart[0] = arCateCnt[0]+cmmLiCnt;arEnd[0] = arStart[0]+arCateCnt[1];break;//드라마
				 case(3) : arStart[0] = arCateCnt[0]+arCateCnt[1]+arCateCnt[2]+(cmmLiCnt*3);arEnd[0] = arStart[0]+arCateCnt[3];break;//애니메이
				 case(4) : arStart[0] = arCateCnt[0]+arCateCnt[1]+(cmmLiCnt*2);arEnd[0] = arStart[0]+arCateCnt[2];break;//예능
				 }
				 
				 arStart[1] = arEnd[0];
				 arEnd[1] = arStart[1]+areaCnt;
				 arStart[2] = arEnd[1];
				 arEnd[2] = arStart[2]+yearCnt;
				 mode = (mode=="") ? 1 : mode;
				 iStart = arStart[mode-1] == 0 ? -1 : (arStart[mode-1]-1);
				 iEnd = arEnd[mode-1];
				 if(gCateNo!=nMenu || gCateStep !=mode || dispMode!=1 || gCateOpen!=1 ){
					  $(".lv3").each(function(i){
						 if(i > iStart && i< iEnd)$(this).show();
					  });
					  gCateOpen = 1;
				 }else{
					 gCateOpen = 0;
				 }
				 gCateNo=nMenu;
				 gCateStep = mode;
			}//end fnc

			//메뉴 초기화 
			function setRightMenuInit(m ,search , cate , catename){
				var i = m/10;
				var pst =1;
				//if(cate!="")pst=2;
				gCateOpen = 1;
				
				if(catename!="" && catename!="all2")gCateOpen=0;
				gCateNo = i;
				gCateStep = 1;
				
				nSelSubItem(i,cate ,pst);
			}

			//정렬
			function nSelOrder(t , mode){
				
				$("#orderUL > li").each(function(index){
	                $(this).removeClass("on");
	        	}); 

				var o = $(t).parent();
				o.addClass("on");

				gOrderMode = mode;
				//정렬 호출
				setInitIPList(gMode,1,gKeyword,gCate,gCateName);
			}

			function nItemTab(nT){
				$(".item_box_pg_item").hide();
				$("#item_pg"+nT).show();
			}
			
			$(function () {
				gMode = 10;
				$("#pop_search").hide();
		      	setInitIPList(gMode,1,'','1','all2');
		    });

			$(document).ready(function(){
				$(".btn_pay_check").on("click",function(e){
					e.preventDefault();
					chk_payment();
				});

				setBuyPlayer();
			});
		</script>
		<style>
		
		.et_cont{width:1536px;}
		.et_cont .coverflow{padding:40px;}
		.et_cont .list_form_03{padding:40px 40px 0;background:#e8e8e8;}
		.et_cont .list_form_03 ul{overflow:hidden;}
		.et_cont .list_form_03 li{float:left;width:280px;margin-left:11px;height:500px;}
		.et_cont .list_form_03 li:nth-child(4n+1){margin-left:10px;}
		.et_cont .list_form_03 li figure{width:280px;height:370px;}
		.et_cont .list_form_03 li .txtBox{position:absolute; width:272px; height:30px;margin-top:-30px;background-color:#000;filter:Alpha(opacity:60);opacity:0.6;overflow:hidden;text-align:right; z-index:55;}
		.et_cont .list_form_03 li .txtBox .rtxt{font-weight:bold;font-size:20px;filter:alpha(opacity=100); opacity:1;color:white;}
  
		.et_cont .list_form_03 li dl{margin:20px 0 40px;}
		.et_cont .list_form_03 li dt a{font-size:32px;color:#666}
		.et_cont .list_form_03 li dd{margin-top:15px;font-size:28px;color:#888;}


		.load_box {overflow:hidden;width:100%;text-align:center;height:90px;padding: 40px 0px 40px 0px;background:white;}
		.load_box .spinner {margin:10px auto 0px;text-align:center;width:80%;height:80px;border:1px solid #cecece;line-height:80px;font-size:24px;}
		.load_box .spinner > div {width:27px;height:27px;background-color: #333; border-radius: 100%;display: inline-block; -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;animation: sk-bouncedelay 1.4s infinite ease-in-out both;}
		.load_box .spinner .bounce1 {-webkit-animation-delay: -0.32s;animation-delay: -0.32s;}
		.load_box .spinner .bounce2 {-webkit-animation-delay: -0.16s;animation-delay: -0.16s;}
		
		 @-webkit-keyframes sk-bouncedelay {
		  0%, 80%, 100% { -webkit-transform: scale(0) }
		  40% { -webkit-transform: scale(1.0) }
		}
		
		@keyframes sk-bouncedelay {
		  0%, 80%, 100% { 
		    -webkit-transform: scale(0);
		    transform: scale(0);
		  } 40% { 
		    -webkit-transform: scale(1.0);
		    transform: scale(1.0);
		  }
		}
				
		#pop_et_pay_pay{}
		#pop_et_pay_pay .pop_cont{box-shadow:5px 10px 10px #7a7a7a}
		#pop_et_pay_pay .pop_cont header strong{padding-left:97px;background:url(/Images/common/ico_pop_header_num.png) no-repeat left center;}
		#pop_et_pay_pay .pop_cont header span{float:left;padding-left:60px;background:url(/Images/common/ico_pop_header_time.png) no-repeat left center;}
		#pop_et_pay_pay .pop_cont article{position:relative;margin-top:40px;}
		#pop_et_pay_pay .pop_cont article:nth-of-type(1){margin-top:0px;}
		#pop_et_pay_pay .pop_cont article .btn_right{background:#a8a8a8}
		#pop_et_pay_pay .pop_cont article input[type='radio']{display:none;}
		#pop_et_pay_pay .pop_cont article label{display:block;background:url(/Images/common/btn_pop_radio.png) no-repeat right 30px;}
		#pop_et_pay_pay .pop_cont article input[type='radio']:checked ~ label{background-position:right -70px;}
		#pop_et_pay_pay .pop_cont .pay_mem{height:150px;line-height:96px;padding:0 40px;color:#444;background:url(/Images/common/bg_pop_cont_line.png) repeat-x left bottom}
		#pop_et_pay_pay .pop_cont .pay_mem span{display:inline-block;}
		#pop_et_pay_pay .pop_cont .pay_mem ul{display:inline-block;float:right;}
		#pop_et_pay_pay .pop_cont .pay_mem li{float:left;margin-left:40px;}
		#pop_et_pay_pay .pop_cont .pay_mem li:first-child{margin-left:0;}
		#pop_et_pay_pay .pop_cont .pay_mem li label{padding-left:47px;background-position:left 30px;}
		#pop_et_pay_pay .pop_cont .pay_mem li input[type='radio']:checked ~ label{background-position:left -70px;}
		#pop_et_pay_pay .pop_cont header a.btn_close{position:absolute;right:0;top:15px;width:77px;height:77px;background:url(/Images/common/btn_cal_close.png) no-repeat center center;}

		
		.pop_wrap2{position:absolute;width:100%;height:100%;top:-10px;left:0;}
		.pop_wrap2 .pop_dim_bg{position:fixed;z-index:1000;width:100%;height:100%;top:102;left:0;background-color:#fff;}
		.pop_wrap2 .pop_cont{position:fixed;width:1005px;top:120px;left:59%;margin-left:-679px;z-index:1001;}
		.search_box {height:84px;width:1005px;text-align:center;}
		.search_box .search{float:left;width:841px;height:84px;background:url(/Images/search_bg.png) no-repeat;}
		.search_box .search .input_search{width:829px;height:74px;border:0px;text-align:left;margin:5px 0px 0px 10px;line-height:74px;font-size:40px;}
		.search_box .search_btn{float:left;width:163px;height:84px;}
		
		#item_box .item_box_pg {width:100%;height:80px;float:left;color:#222;margin-top:-10px;}
		#item_box .item_box_pg_item {width:100%;height:220px;float:left;display:none;}
		#item_box .item_box_pg a.on{color:#000;font-weight:bold;}
		.itemBox {width:95px;height:95px;line-height:95px;margin-left:0;margin-top:10px;font-size:60px;color:#fff;text-align:center;border-radius:20px;background:#00ad85;display:inline-block}
		.itemBox_on {width:95px;height:95px;line-height:95px;margin-left:0;margin-top:10px;font-size:60px;color:#fff;text-align:center;border-radius:20px;background:#005234;display:inline-block;}
		
		#content_header .btn_area2{margin:60px 0 20px;font-size:60px;text-align:center;}
		#content_header .btn_area2 span{display:inline-block;width:444px;height:110px;line-height:110px;color:#fff;}
		#content_header .btn_area2 a.btn_grey{background:#555}
		#content_header .btn_area2 span.btn_oranges{background:#fc470e;margin-left:16px;}	
		.et_nav .dp_02 ul li.lv2 {border-color:#534d4b transparent #201a18 transparent;}
		.et_nav .dp_02 ul li.lv2 a{color:#ccc;margin-left:20px;}
		.et_nav .dp_02 ul li.lv3 {display:block;border-color:#534d4b transparent #201a18 transparent;}		
		.et_nav .dp_02 ul li.lv3 a{color:#ccc;margin-left:40px;}
		
		.orderList {width:100%;height:50px;background-color:#e8e8e8;font-size:28px;padding-top:20px;}
		.orderList ul {width:640px;float:right;margin-right:20px;}
		.orderList ul li{float:left;width:160px;height:50px;text-align:center;line-height:50px;}
		.orderList ul li.on{background-color:#fc470e;}
		.orderList ul li.on a{color:#fff;}
		
		.Banner_pay01 {width: 0;height: 0;border-top: 100px solid #fc470e;border-right: 100px solid transparent;position:absolute;z-index:50;filter:Alpha(opacity:90);opacity:0.9;}
		.Banner_pay01 span{position:absolute;margin:-80px 0 0 5px;color:white;font-weight:bold;width:80px;font-size:22px;}
		.Banner_pay02 {width: 0;height: 0;border-top: 100px solid #6A9BD8;border-right: 100px solid transparent;position:absolute;z-index:52;filter:Alpha(opacity:90);opacity:0.9;}
		.Banner_pay02 span{position:absolute;margin:-80px 0 0 5px;color:white;font-weight:bold;width:80px;font-size:22px;}
		
		</style>
	</head>
	<body>
		<div id="wrap" class="et contents">
			<?include_once $root_path.'/Comm/header.php';?>	

			<div id="container">
				<?include_once $root_path.'/Comm/nav.php';?>

				<aside class="et_nav">
					<ul class="dp_01">
						<li><span class="v_al"><a href="service_main.php"><i class="fa_01"></i>主页</span></a>
							<div class="dp_02_wrap">
								<div class="dp_02">
									
								</div><!--end dp_02-->
							</div><!--end dp_02_wrap-->
						</li>
						<li><span class="v_al"><a href="iptv_list.php"><i class="fa_02"></i>直播</a></span>
							<div class="dp_02_wrap">
								<div class="dp_02">									
								</div><!--end dp_02-->
							</div><!--end dp_02_wrap-->
						</li>
						<li  class="on"><span class="v_al"><a href="vod_list.php"><i class="fa_03"></i>点播</a></span>
							<div class="dp_02_wrap">
								<div class="dp_02">
									<strong class="bk_tit_01">类别名称&nbsp;&nbsp;<img src="/Images/search.png" style="width:80px;" onClick="nSearch()"/></strong>
									<ul id="iptv_channel">
										<li class="on lv1"><a href="#" onClick="setInitIPList(10,1,'','1','all2');nSelTV(this,1,1);">电影</a></li>
										<li class="lv2"><a href="#" onClick="nSelSubItem(1,1,1)">✛ 影片类型</a>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','all');nSelTV(this,1,2);">- 全部</a></li>
										<!--<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','恐怖悬疑');nSelTV(this,1,2);"> - 恐怖悬疑</a></li>-->
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','警匪动作');nSelTV(this,1,2);"> - 警匪动作</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','战争史诗');nSelTV(this,1,2);"> - 战争史诗</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','文艺情感');nSelTV(this,1,2);"> - 文艺情感</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','科幻灾难');nSelTV(this,1,2);"> - 科幻灾难</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','喜剧幽默');nSelTV(this,1,2);"> - 喜剧幽默</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','经典纪实');nSelTV(this,1,2);"> - 经典纪实</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','3D');nSelTV(this,1,2);"> - 3D</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','微影院');nSelTV(this,1,2);"> - 微影院</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','1','其他');nSelTV(this,1,2);"> - 其他</a></li>
										<li class="lv2"><a href="#"  onClick="nSelSubItem(1,2,1)">✛ 发行地区</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','2','all');nSelTV(this,1,2);"> - 全部</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','2','内地');nSelTV(this,1,2);"> - 内地</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','2','港台');nSelTV(this,1,2);"> - 港台</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','2','欧美');nSelTV(this,1,2);"> - 欧美</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','2','日韩');nSelTV(this,1,2);"> - 日韩</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','2','其他');nSelTV(this,1,2);"> - 其他</a></li>
										<li class="lv2"><a href="#"  onClick="nSelSubItem(1,3,1)">✛ 发行时间</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','3','all');nSelTV(this,1,2);"> - 全部</a></li>
										<?=$strLi01?>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','3','00');nSelTV(this,1,2);"> - 00年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','3','90');nSelTV(this,1,2);"> - 90年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','3','80');nSelTV(this,1,2);"> - 80年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(10,1,'','3','更早年代');nSelTV(this,1,2);"> - 更早年代</a></li>
										<!--Drama -->
										<li class="lv1"><a href="#" onClick="setInitIPList(20,1,'','1','all2');nSelTV(this,2,1);">电视剧</a></li>
										<li class="lv2"><a href="#" onClick="nSelSubItem(2,1,1)">✛ 分类</a>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','1','all');nSelTV(this,2,2);">- 全部</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','1','人文军旅');nSelTV(this,2,2);"> - 人文军旅</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','1','言情偶像');nSelTV(this,2,2);"> - 言情偶像</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','1','情感生活');nSelTV(this,2,2);"> - 情感生活</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','1','历史古装');nSelTV(this,2,2);"> - 历史古装</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','1','刑侦悬疑');nSelTV(this,2,2);"> - 刑侦悬疑</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','1','武侠魔幻');nSelTV(this,2,2);"> - 武侠魔幻</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','1','网络剧');nSelTV(this,2,2);"> - 网络剧</a></li>
										<li class="lv2"><a href="#"  onClick="nSelSubItem(2,2,1)">✛ 地区</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','2','all');nSelTV(this,2,2);"> - 全部</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','2','内地');nSelTV(this,2,2);"> - 内地</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','2','港台');nSelTV(this,2,2);"> - 港台</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','2','欧美');nSelTV(this,2,2);"> - 欧美</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','2','日韩');nSelTV(this,2,2);"> - 日韩</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','2','其他');nSelTV(this,2,2);"> - 其他</a></li>
										<li class="lv2"><a href="#"  onClick="nSelSubItem(2,3,1)">✛ 时间</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','3','all');nSelTV(this,2,2);"> - 全部</a></li>
										<?=$strLi02?>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','3','00');nSelTV(this,2,2);"> - 00年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','3','90');nSelTV(this,2,2);"> - 90年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','3','80');nSelTV(this,2,2);"> - 80年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(20,1,'','3','更早年代');nSelTV(this,2,2);"> - 更早年代</a></li>
										<!--Enter -->
										<li class="lv1"><a href="#" onClick="setInitIPList(40,1,'','1','all2');nSelTV(this,4,1);">综艺</a></li>
										<li class="lv2"><a href="#" onClick="nSelSubItem(4,1,1)">✛ 分类</a>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','all');nSelTV(this,4,2);">- 全部</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','资讯');nSelTV(this,4,2);"> - 资讯</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','娱乐');nSelTV(this,4,2);"> - 娱乐</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','访谈');nSelTV(this,4,2);"> - 访谈</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','纪实');nSelTV(this,4,2);"> - 纪实</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','探索');nSelTV(this,4,2);"> - 探索</a></li>
										<!-- <li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','时尚');nSelTV(this,4,2);"> - 时尚</a></li> -->
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','生活');nSelTV(this,4,2);"> - 生活</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','体育');nSelTV(this,4,2);"> - 体育</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','少儿');nSelTV(this,4,2);"> - 少儿</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','公开课');nSelTV(this,4,2);"> - 公开课</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','军事游戏');nSelTV(this,4,2);"> - 军事游戏</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','1','恰恰');nSelTV(this,4,2);"> - 恰恰</a></li>
										<li class="lv2"><a href="#"  onClick="nSelSubItem(4,2,1)">✛ 地区</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','2','all');nSelTV(this,4,2);"> - 全部</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','2','内地');nSelTV(this,4,2);"> - 内地</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','2','港台');nSelTV(this,4,2);"> - 港台</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','2','欧美');nSelTV(this,4,2);"> - 欧美</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','2','日韩');nSelTV(this,4,2);"> - 日韩</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','2','其他');nSelTV(this,4,2);"> - 其他</a></li>
										<li class="lv2"><a href="#"  onClick="nSelSubItem(4,3,1)">✛ 时间</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','3','all');nSelTV(this,4,2);"> - 全部</a></li>
										<?=$strLi04?>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','3','00');nSelTV(this,4,2);"> - 00年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','3','90');nSelTV(this,4,2);"> - 90年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','3','80');nSelTV(this,4,2);"> - 80年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(40,1,'','3','更早年代');nSelTV(this,4,2);"> - 更早年代</a></li>
										<!--Ani -->
										<li class="lv1"><a href="#" onClick="setInitIPList(30,1,'','1','all2');nSelTV(this,3,1);">动漫</a></li>
										<li class="lv2"><a href="#" onClick="nSelSubItem(3,1,1)">✛ 分类</a>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','1','all');nSelTV(this,3,2);">- 全部</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','1','动漫电影');nSelTV(this,3,2);"> - 动漫电影</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','1','动漫电视');nSelTV(this,3,2);"> - 动漫电视</a></li>
										<li class="lv2"><a href="#"  onClick="nSelSubItem(3,2,1)">✛ 地区</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','2','all');nSelTV(this,3,2);"> - 全部</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','2','内地');nSelTV(this,3,2);"> - 内地</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','2','港台');nSelTV(this,3,2);"> - 港台</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','2','欧美');nSelTV(this,3,2);"> - 欧美</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','2','日韩');nSelTV(this,3,2);"> - 日韩</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','2','其他');nSelTV(this,3,2);"> - 其他</a></li>
										<li class="lv2"><a href="#"  onClick="nSelSubItem(3,3,1)">✛ 时间</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','3','all');nSelTV(this,3,2);"> - 全部</a></li>
										<?=$strLi03?>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','3','00');nSelTV(this,3,2);"> - 00年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','3','90');nSelTV(this,3,2);"> - 90年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','3','80');nSelTV(this,3,2);"> - 80年代</a></li>
										<li class="lv3"><a href="javascript:void(0)" onClick="setInitIPList(30,1,'','3','更早年代');nSelTV(this,3,2);"> - 更早年代</a></li>
									</ul>
								</div><!--end dp_02-->
							</div><!--end dp_02_wrap-->							
						</li>
						<li><span class="v_al"><a href="javascript:vodi(0)" onclick="nAppStart(2);"><i class="fa_04"></i>音乐</a></span>
							<div class="dp_02_wrap">
								<div class="dp_02">
									
								</div><!--end dp_02-->
							</div><!--end dp_02_wrap-->
						</li>
						<li><span class="v_al"><a href="javascript:void(0)"  onclick="<?=$strWebNew?>"><i class="fa_05"></i>新闻</a></span>
							<div class="dp_02_wrap">
								<div class="dp_02">
									
								</div><!--end dp_02-->
							</div><!--end dp_02_wrap-->
						</li>
						<li><span class="v_al"><a href="game_list.php"><i class="fa_06"></i>游戏</a></span>
							<div class="dp_02_wrap">
								<div class="dp_02">
									
								</div><!--end dp_02-->
							</div><!--end dp_02_wrap-->
						</li>
						<li><span class="v_al"><a href="javascript:void(0)" onclick="nAppStart(1);"><i class="fa_07"></i>阅读</a></span>
							<div class="dp_02_wrap">
								<div class="dp_02">
									
								</div><!--end dp_02-->
							</div><!--end dp_02_wrap-->
						</li>
					</ul><!--end dp_01-->
				</aside><!--end et_nav-->
				
				<section class="et_cont">
					<div class="coverflow"><?include_once $root_path.'/Banner/vod_banner.php';?></div>
					<div class="orderList">
					    	<ul id="orderUL">
					    	   <li class="on"><a href="javascript:void(0)" onclick="nSelOrder(this,1)" >最新更新</a></li>
					    	   <li ><a href="javascript:void(0)" onclick="nSelOrder(this,2)" >最多播放</a></li>
							   <li ><a href="javascript:void(0)" onclick="nSelOrder(this,3)" >收&nbsp;&nbsp;&nbsp;&nbsp;费</a></li>
							   <li ><a href="javascript:void(0)" onclick="nSelOrder(this,4)" >免&nbsp;&nbsp;&nbsp;&nbsp;费</a></li>
					    	 </ul>
					 </div>
					<article class="list_form_03">
						<ul id="channel_list">						
						</ul>
					</article>
					<!-- loadding -->
					<div id="pop_buy" class="load_box">
					     <div class="spinner">
						  <div class="bounce1"></div>
						  <div class="bounce2"></div>
						  <div class="bounce3"></div>
						  <span class="spinner_txt">正在加载中</span>
						</div>
						
					</div>
					<!-- loadding -->
				</section>
			</div>
			<!-- search -->
			<div id="pop_search" class="pop_wrap2">
				<div class="pop_dim_bg"></div>
				<div class="pop_cont">
					<div class="search_box">
					  <div class="search">
					  	<input type="text" name="keyword" id="keyword" value="" class="input_search" placeholder="请输入影片名"/>
					  </div>
					  <div class="search_btn">
					  <a href="javascript:void(0)" onclick="setKeyword()"><img src="/Images/btn_search.png"></a>
					  </div>
					</div>
				</div>
			</div>
			<!-- search -->
			
			<!-- view -->
			<div class="pop_area">
				<!--구매하기 팝업-->
				<div id="pop_et_pay" class="pop_wrap" style="display:none;">
					<div class="pop_dim"></div>
					<div class="pop_cont" style="z-index:1000;height:1080px;overflow:auto;">
						<header>
							<a href="javascript:void(0)" onclick="setClose();" class="btn_close"></a>
						</header>
						
						<section>
							<article id="content_header"></article>
						</section>
					</div>
				</div>
				<!--구매하기 팝업 끝-->
				<!-- 결제하기 팝업 -->
				<div id="pop_et_pay_pay" class="pop_wrap" style="z-index:9999;display:none;">
					<div class="pop_dim"></div>
					<div class="pop_cont">
						<header>
						</header>
						<section>
							<form name="vodForm" onsubmit="return false;">
							<input type="hidden" name="paymentSeq" value="">
							<input type="hidden" name="nVodSeq"/>
							<input type="hidden" name="nCouponSeq"/>
							<article>
								<div id="payment_list">
									
								</div>
							</article>
							<article>
								<div class="tit_grey">结算方式</div><!-- 결제수단 -->
								<ul>
									<li><input type="radio" name="nPayType" value="1" id="ra_01" checked/><label for="ra_01">微信</label></li>
									<li><input type="radio" name="nPayType" value="2" id="ra_02"  /><label for="ra_02"> 支付宝</label></li>
								</ul>
							</article>
							<div class="btn_area">
								<a href="javascript:$('#pop_et_pay_pay').hide();setClose();" class="btn_grey">关闭</a>
								<a href="javascript:nVodBuy()" class="btn_orange">确认</a>
							</div>
							</form>
						</section>
					</div>
				</div>
				<!-- 결제하기 팝업 -->
				<!--결제완료 팝업-->
				<div id="pop_qrcode" class="pop_wrap" style="display:none;z-index:9999;">
					<div class="pop_dim"></div>
					<div class="pop_cont">
						<header>
							<strong>结算 </strong>
							<!-- <a href="javascript:location.reload();" class="btn_close"></a> -->
							<a href="javascript:chk_payment_done(2);" class="btn_close"></a>
						</header>
						<section>
							<div class="qucode_area" style="text-align: center;">
								<img class="qrcode" src="http://<?=HOST_NAME?>/Payment/qrcode.php?data=111" style="width:500px;height:500px;">
							</div>
							<div class="" style="text-align:center;margin:40px;">
								<strong>请用微信扫码支付</strong>
							</div>
							<!--주문확인 대기중 끝-->
							<div class="btn_area">
								<a href="#" class="btn_grey btn_pay_check">确认付款</a>
							</div>
						</section>
					</div>
				</div>
				<!--결제완료 팝업 끝-->
			</div>
			<!-- view -->
			
		</div><!--end wrap-->
	</body>
</html>
<?include_once $root_path.'/Comm/footer.php';?>