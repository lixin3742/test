<?php 
	/**
	 * Description : 버전 업데이트 정보 불러오기
	 * Date : 2015.12.25
	 * Version : 1.0
	 */
	// error_reporting(E_ALL);
	// ini_set('display_errors', 'On');
	$nNowDate = (int)date("mdH");
// 	if($nNowDate<70800 || $nNowDate >70807){
// 		//echo '{"code":1,"msg":"0","other":""}';
// 		echo '{"code":0,"msg":"Parameter nothing.","other":""}';
// 		exit;
// 	}
	
	$root_path = $_SERVER["DOCUMENT_ROOT"];
	include_once $root_path.'/Lib/Conf/Config.php';
	
	$oVAR->getVar('strDevice', Variable::VARIABLE_ALL,'string');
	$oVAR->getVar('strVersion', Variable::VARIABLE_ALL,'string');
	
	
 	if($_GDATA['strDevice']=='')returnMsg(0,"Parameter nothing.","",$oMySQL);
 	
 	$strInsert =  "INSERT INTO tUpdatelog(strDevice,strVersion) VALUES(?,?)";
 	$InsertId = $oMySQL->exec($strInsert,array($_GDATA['strDevice'],$_GDATA['strVersion']),1);
 	
 	$strTableName = 'tcompany_version';
 	$strTableColumn = ' strVersion,nHospital,nKind ';
 	$strTableWhere = " isUse='y' AND isDel='n'";
 	$strQuery =  'SELECT '.$strTableColumn.' FROM '.$strTableName.' WHERE '.$strTableWhere.' ORDER BY id DESC LIMIT 1';
 	$arParams = array();
 	$arResult = $oMySQL->exec($strQuery,$arParams,3);
 	//判断当前的设备在不在更新的医院的科室下
 	$strColumn = " SELECT id, nHospital ,nKind , strDevice FROM tDeviceList WHERE ";
	$strColumn .= " strDevice=? AND isDel=? AND isUse=?";
	$arParam = array($_GDATA['strDevice'] ,'n','y');
	$arData = $oMySQL->exec($strColumn,$arParam,3);
	$ifDownLoad = false;
    if($arResult['nHospital'] == $arData['nHospital']){
    	if($arResult['nKind'] ==1 || $arResult['nKind'] == $arData['nKind']){
    		$ifDownLoad=true;
    	}
    }
    
 	if(is_array($arResult) && $ifDownLoad){
 		$strVersion = $arResult['strVersion'];
 		$strDownLoad =  'http://'.DOMAIN.'/Version/AmcSec_'.$strVersion.'.apk';
 	}else{
 		$strVersion = '0';
 		$strDownLoad =  '';
 	}
 	returnMsg(1,$strVersion,$strDownLoad,$oMySQL);
 	
 	/*
 	
//  	$strInsert =  "INSERT INTO tUpdatelog(strDevice,strVersion) VALUES(?,?)";
//  	$InsertId = $oMySQL->exec($strInsert,array($_GDATA['strDevice'],$_GDATA['strVersion']),1);
 	
 	$nTime = date("mdH");
 	$nMin = sprintf('%02d',(floor((int)date("i")/10)*10));
 	$nStartTime = $nTime.$nMin;
 	
 	$nNextTime = date("mdH",strtotime('+10 minute'));
 	$nNextMin = sprintf('%02d',(floor((int)date("i",strtotime('+10 minute'))/10)*10));
 	$nEndTime = $nNextTime.$nNextMin;
 	
 	
 	$strTableName = 'tAppUpdateLog';
 	$strTableColumn = ' id, strVersion ';
 	//$strTableWhere = " strDevice=? AND isUse='y' AND isDel='n' AND  nTime >= ".$nStartTime." AND nTime < ".$nEndTime;
 	$strTableWhere = " strDevice=? AND isUse='y' AND isDel='n'";
 	$strQuery =  'SELECT '.$strTableColumn.' FROM '.$strTableName.' WHERE '.$strTableWhere.' ORDER BY id DESC LIMIT 1';
 	
 	$arParams = array($_GDATA['strDevice']);
 	$arResult = $oMySQL->exec($strQuery,$arParams,3);
 	
 	if(is_array($arResult)){
 		$strUpdate = "UPDATE tAppUpdateLog SET isUse='n' WHERE id=?";
 		$oMySQL->exec($strUpdate,array($arResult['id']),4);
 		$strDownLoad =  'http://'.DOMAIN.'/Version/AmcSec_'.$arResult['strVersion'].'.apk';
 	}else{
 		$strVersion = '0';
 		$strDownLoad =  '';
 	}
 	returnMsg(1,$strVersion,$strDownLoad,$oMySQL);*/
?>