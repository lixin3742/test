<?php 

	/**
	 * Description : VOD List 가져오기 (wasu xml 구조 변경에 따른 적용)
	 * Date : 2016-01-27
	 * Version : 1.0
	 */

// 	error_reporting(E_ALL);
// 	ini_set('display_errors', 'On');
	$root_path = $_SERVER['DOCUMENT_ROOT'];	
	include($root_path.'/Lib/Conf/Config.php');
	
	$oVAR->getVar('nMode', Variable::VARIABLE_POST,'integer');
	$oVAR->getVar('page', Variable::VARIABLE_POST,'integer');
	$oVAR->getVar('keyword', Variable::VARIABLE_POST,'string');
	$oVAR->getVar('strCate', Variable::VARIABLE_POST,'string');
	$oVAR->getVar('strCateName', Variable::VARIABLE_POST,'string');
	$oVAR->getVar('orders', Variable::VARIABLE_POST,'integer');
	
	$nOrder = ($_GDATA['orders']<1) ? 1 : $_GDATA['orders'];
	
	switch((int)$_GDATA['nMode']){		
		case(10) : 
		case(11) : $strTable = "tVod_film3";break;//영화 최신		
		case(20) : 
		case(21) : $strTable = "tVod_tv3";break;//드라마 최신		
		case(30) : 
		case(31) : $strTable = "tvod_cartoon_daily";break;//애니메이션 최신		
		case(40) : 
		case(41) : $strTable = "tVod_art3";break;//오락프로 최신		
		default : $strTable = "tVod_film3";break;//영화 최신
	}
	
	$nPage = ( $_GDATA["page"] < 1 ) ? 1 : $_GDATA["page"];
	$nLimit = 15;
	$nBlock = 10;
	$nTotalCnt = 0;
	$nTotalPage = 0;
	$oMySQL->setPagingInfo($nLimit,$nBlock);
	
	$strColumn = 'id , workname , img01 , img02, introduction ,hot ,isFree';
	$strWhere = 'isUse =? ';
	$arParam = array('y');
	if($_GDATA['keyword']!=''){
		$strWhere .= " AND CONCAT(workname ,introduction) LIKE ?";
		$arParam[] = "%".$_GDATA["keyword"]."%";
	}
	
	if($_GDATA['strCate']!='' && $_GDATA['strCateName']!='' && $_GDATA['strCateName']!='all'  && $_GDATA['strCateName']!='all2'){
		$nCate = (int)$_GDATA['strCate'];
		if($nCate!=3){
			switch($nCate){
				case(1) : $strWhere .= " AND cate2 ";break;//분류
				case(2) : $strWhere .= " AND region ";break;//지역
			}
			if($_GDATA['strCateName']=='其他'){
				if($nCate==1){
					//$strWhere .= " IN('','恐怖悬疑',?)";
					$strWhere .= " IN('',?)";
				}else{
					$strWhere .= " IN('',?)";
				}
			}else{
				$strWhere .= ' = ?';
			}
			$arParam[] = $_GDATA['strCateName'];
		}else{
			if(strLen($_GDATA['strCateName'])==2){
				switch($_GDATA['strCateName']){
					case('00'):$strWhere .= " AND showTime BETWEEN 2000 AND 2010";break;
					case('90'):$strWhere .= " AND showTime BETWEEN 1990 AND 1999";break;
					case('80'):$strWhere .= " AND showTime BETWEEN 1980 AND 1989";break;
					case('更早年代'):$strWhere .= " AND showTime <1980";break;
				}
			}else{
				$strWhere .= " AND showTime=?";
				$arParam[] = $_GDATA['strCateName'];
			}
			
		}
	}
	
	$strOrder =  " updateTime DESC";
	if($nOrder==2)$strOrder =  " cate2 DESC ,hot DESC";	
	$strOrder = 'isFree DESC ,'.$strOrder;
	
	$arCnt = $oMySQL -> getBoardCnt($strTable,$strColumn,$strWhere,$arParam);
	
	if(is_array($arCnt)){
		$nTotalCnt = $arCnt["total"];
		$nTotalPage = $arCnt["page"];
	}
	
	if($nPage >$nTotalPage){
		$strMsg = '没有相关数据';
		if($nPage >1)$strMsg = '已经是最后一页了 ['.$nPage.']';
		returnMsg(0 ,$strMsg ,'');
	}
	
	if($nTotalCnt > 0){
		$arList = $oMySQL -> getBoardList($strTable,$strColumn,$strWhere,$arParam,$strOrder,$nPage);
		
		if(is_array($arList)){
			$arJson = json_encode($arList);
			returnMsg(1 ,"成功" ,$arJson);
		}else{
			$strMsg = '没有相关数据';
			if($nPage>1)$strMsg = '已经是最后一页了 ['.$nPage.']';
			returnMsg(0 , $strMsg ,'');
		}
		
	}else{
		returnMsg(0 ,"失败" ,'');
	}
?>