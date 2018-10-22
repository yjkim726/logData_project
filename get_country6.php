#! /data/wipsplus/server_dict/app/php/bin/php -q

<?php

 		$root_dir="/data/wipsplus/search_log";
//		$nowdate= date("Ymd", strtotime("-1 day"));
		$month=$argv[1];


	Class InsertData{
		public $service, $country;
	}


	// 검색통계 18년 국가 및 서버 추가

	function lineReadWOWT( $line, & $ctrData , & $ctgoryData , $flag, $serv, $dt){
		global $root_dir;	
		#$county=array{"KR","CO","US","JO","PCT","EP","CK","KPA","JP","PAJ","I1","DE","GB","FR","RU","TW","IN"};
#		$country=array("K","CO","U","JO","W1","E1","E2","CK","L1","L2","JK","P1","I1","D1","D2","D3","G1","G3","H1","H2","H3","R1","R2","R3","T1","T2","T3","T5","N3","N4","CE","AT","AU","CA","CH","DK","ES","FI","IT","NL","SE");
		$country=array("K","CO","U","J","W1","E1","E2","CK","L1","L2","P1","I1","D1","D2","D3","G1","G3","H1","H2","H3","R1","R2","R3","T1","T2","T3","T5","N3","N4","CE","AT","AU","CA","CH","DK","ES","FI","IT","NL","SE","B1","B2","B3","B4");
		$jdCountry = array("KJD", "UJD", "EJD", "JJD", "UST","KH","CH","JH");
		$def=0; $gbf=0; $ind=0 ; $frf=0; $ruf=0; $twf=0; $tenf=0;
		$jkf=0; $pajf=0; $cef=0; 
		$chf=0; $jhf=0; $khf=0;
		$ctr_tmp="";
		$cnt="";


		//category 변수
		$category= array('basic', 'number', 'step','integrated', 'easy', 'judge');
		#$category= array('judge');		
	
		//flag에 따른 country name 설정	
        if(  $flag==0   )
			$cnt="WO";
		else if ( $flag==1 )
			$cnt="WT";
		else if ( $flag==2 )
			$cnt="WG";
		else if ( $flag==3 )
			$cnt="WC";
		else if ( $flag==4 )
			$cnt="SK";



		//serv = 147 추가 
		if( ($serv=='235') || ($serv=='236') || ($serv=='237') || ($serv=='238') || ($serv=='239') || ($serv=='240')  || ($serv=='145')  || ($serv=='147')  ){
		// LN 6개국 추가
			foreach($jdCountry as $jdc){
				if(strpos($line, "coll=$jdc")){
					switch($jdc){
						case "KJD" :
							$ctrData[$flag]['KJD']++;
							$ctgoryData[$cnt]['KR']['judge']++;					
							break;
						case "UJD" :
							$ctrData[$flag]['UJD']++;
							$ctgoryData[$cnt]['US']['judge']++;					
							break;
						case "EJD" :
							$ctrData[$flag]['EJD']++;
							$ctgoryData[$cnt]['EP']['judge']++;					
							break;
						case "JJD" :
							$ctrData[$flag]['JJD']++;
							$ctgoryData[$cnt]['JP']['judge']++;					
							break;
						case "UST" :
							$ctgoryData[$cnt]['US']['judge']++;					
							break;
					}
					if($flag==2){
						switch($jdc){
							case "CH" : 
								$ctrData[$flag]['CN']++;
								$ctr_tmp="CN";
								break;
							case "KH" : 
								$ctrData[$flag]['KR']++;	
								$ctr_tmp="KR";
								break;
							case "JH" :
								$ctrData[$flag]['JP']++;
								$ctr_tmp="JP";
								break;	
						}
					}
				}
			}
		}

		//ex. &coll=JK1,JK3,JK4,JK6,JK2,JK5,JK7,P1&npp=200&start=1&svc=WT&tb=integrated
		/*통합검색에서 검색되는 경우
		  통합검색 : 한국어/영어 는 한국어 케이스 + 영어 케이스 합친 경우
			101,102,103 : 통합검색 > 일본 JK + P1
			144,146 : 144(LN/IN/DOCDB) + 146(JK) + P1
			106,107 : 통합검색 > 중국 CK (한국어 선택)
			243,244 : 통합검색 > 중국 CE (영어 선택)

		*/

		#if( ( (strpos($line, "tb=easy"))  || ( strpos( $line, "tb=integrated"))) && (( $serv=='243') || ($serv=='244') || ($serv=='101') || ($serv=='102') || ($serv=='103') || ($serv=='144') || ($serv=='145') || ($serv=='147')  )){

		else if( ( (strpos($line, "tb=easy"))  || ( strpos( $line, "tb=integrated"))) && (( $serv=='243') || ($serv=='244') || ($serv=='101') || ($serv=='102') || ($serv=='103') || ($serv=='144') || ($serv=='146')  || ($serv=='106')  || ($serv=='107')  )){
			//147 CO , FDA JDG SUIT 추가
			if($collArr=strstr($line,"coll=")){
				$collIdx = strpos($collArr, "&");
				$colls = substr( $collArr, 5, $collIdx-5 );				
				$strTok=explode(',', $colls);
				for( $i=0; $i< count($strTok) ; $i++ ){
					$str = $strTok[$i];
//					if($flag==0 || $str=="D1")	
//						echo "$line\n";

/*					if($flag==2){
						switch($str){
							case "CH1" : 
							case "CH2" : 
							case "CH3" : 
							case "CH4" :
								if($chf==0){
									$ctrData[$flag]['CN']++;
									$chf++;
								}		
								break;
							case "KH1" : 
							case "KH2" : 
							case "KH3" : 
							case "KH4" :
								if($khf==0){
									$ctrData[$flag]['KR']++;	
									$khf++;
								} 
								break;
							case "JH1" :
							case "JH2" :
							case "JH3" :
							case "JH4" :
							case "JH5" :
							case "JH6" :
							case "JH7" :
								if($jhf==0){
									$ctrData[$flag]['JP']++;
									$jhf++;
								}
								break;	
						}
					}
*/
					switch($str){
						case "CE1" :
						case "CE2" :
						case "CE3" :
						case "CE4" :
							if( $cef==0){
								$ctr_tmp="CN";
								$ctrData[$flag]['CN']++;
								$cef++;
							}
							break;
						case "JK1" :
						case "JK2" :
						case "JK3" :
						case "JK4" :
						case "JK5" :
						case "JK6" :
						case "JK7" :
							if( $jkf==0 ){
								$ctr_tmp="JP";
								$ctrData[$flag]['JP']++;
								$jkf++;
							}
							break; 
						case "P1" :
							if( $pajf==0){
								$ctrData[$flag]['PAJ']++;
								$pajf++;
							}
							break;
						case "I1" :
							$ctrData[$flag]['DOCDB']++;
							break;
						case "D1" : 
						case "D2" : 
						case "D3" : 
							if( $def==0 ){
								$ctrData[$flag]['DE']++;
							//	echo "$line\n\n\n";
							//	echo "$strTok[$i]";
								$def++;
							}
							break;
						case "G1" :
						case "G3" :
							if( $gbf==0 ){
								$ctrData[$flag]['GB']++;
								$gbf++;
							}
							break;
						case 'N3' :
						case 'N4' :
							if( $ind==0 ){
								$ctrData[$flag]['INDIA']++;
								$ind++;
							}
							break;
						case 'H1' : 
						case 'H2' : 
						case 'H3' : 
							if( $frf==0 ){
								$ctrData[$flag]['FR']++;
								$frf++;
							}
							break;
						case 'R1' :
						case 'R2' :
						case 'R3' :
							if( $ruf==0 ){
								$ctrData[$flag]['RU']++;
								$ruf++;
							}
							break;
						case 'T1' :
						case 'T2' :
						case 'T3' :
						case 'T5' :
							if( $twf==0 ){
								$ctrData[$flag]['TW']++;
								$twf++;
							}
							break;
						case 'AT' :
						case 'AU' :
						case 'CA' : 
						case 'CH' :
						case 'DK' :
						case 'ES' :
						case 'FI' :
						case 'IT' :
						case 'NL' :
						case 'SE' :
							if( $tenf==0){
								$ctrData[$flag]['10CTR']++;
								$tenf++;
							}
							break;
					}//end of switch
				}
			}
		}

		else if( ($str=strpos( $line, "tb=&membrCd"))){
				// if에 안들어가는 case tb=&membr .. 
				// 1. P1의 경우, JK1|JK2|JK3|P1으로 들어가, else에도 들어가지 않음
				// 2. D1의 경우, else에서 coll=D1으로 count
						echo "$line\n";
		}

		else{
			foreach($country as $ctr){
				if( $str = strpos($line, "coll=$ctr")){
//					if($flag==0 && $ctr=="D1")
//						echo "$line\n";
					switch($ctr){
						case 'B1' :
						case 'B2' :
						case 'B3' :
						case 'B4' :
							$ctrData[$flag]['PAT']++;
							break;
						case 'K' : 
							$ctr_tmp="KR";
							$ctrData[$flag]['KR']++;
							break;
						case 'CO':
						case 'CK':
						case 'CE' :
							$ctr_tmp="CN";
							$ctrData[$flag]['CN']++;
							break;
						case "U" :
							$ctr_tmp="US";
							$ctrData[$flag]['US']++;
							break;
						case 'E1' :
						case 'E2' :
							$ctr_tmp="EP";
							$ctrData[$flag]['EP']++;
							break;
//						case 'JO':
//						case 'JK' :
						case 'J' : 
							$ctr_tmp="JP";
							$ctrData[$flag]['JP']++;
							break;
						case 'W1' :
							$ctrData[$flag]['PCT']++;
							break;
						case 'L1' :
						case 'L2' : 
							$ctrData[$flag]['KPA']++;
							break;
						case 'P1' : 
							$ctrData[$flag]['PAJ']++;
							break;
						case 'I1' :
							$ctrData[$flag]['DOCDB']++; 
							break;
						case 'N3' :
						case 'N4' : 
							$ctrData[$flag]['INDIA']++;
							break;
						case 'D1' : 
						case 'D2' : 
						case 'D3' :
							$ctrData[$flag]['DE']++; 
							break;
						case 'G1' : 
						case 'G3' :
							$ctrData[$flag]['GB']++; 
							break;
						case 'H1' : 
						case 'H2' : 
						case 'H3' :
							$ctrData[$flag]['FR']++; 
							break;
						case 'R1' : 
						case 'R2' : 
						case 'R3' :
							$ctrData[$flag]['RU']++; 
							break;
						case 'T1' : 
						case 'T2' : 
						case 'T3' : 
						case 'T5' : 
							$ctrData[$flag]['TW']++; 
							break;
						case 'AT' :
						case 'AU' :
						case 'CA' : 
						case 'CH' :
						case 'DK' :
						case 'ES' :
						case 'FI' :
						case 'IT' :
						case 'NL' :
						case 'SE' :
							$ctrData[$flag]['10CTR']++;
							break;
						
						
					}//end of switch
				}
			}
		}//end of else


		//membrCd + category 구분자
		if( $ctr_tmp!=""  ){


			//membrCd 구분자
			if($memstr= strstr($line, "membrCd=") ){
				$membrIdx = strpos($memstr, "&");
				$membrCd = substr($memstr, 8, $membrIdx-8);
				if(!$membrCd == ""){
					$tmp = $membrCd."\n";
					$fpp=fopen("${root_dir}/membrCd/${ctr_tmp}_${cnt}_${dt}_membrCd","a");
					fwrite( $fpp ,$tmp);
				}
			}

			//category 구분자
			foreach( $category as $tb ){
				if( strpos($line, "tb=$tb")  ){
					$ctgoryData[ $cnt ][ $ctr_tmp ][$tb]++;
					
				}
			}



		}

	////////////////membrCd 구분자


	}




	function file_get_start(){

 		global $root_dir;
		global $month;
		$year="2017";

		$date=array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31');
#		$date=array('19');

		$ctrName=array("KR", "US", "CN", "JP", "EP", "PCT", "KPA", "PAJ", "INDIA", "DOCDB", "DE", "GB",
		"FR","RU","TW","10CTR", "KJD","UJD","EJD","JJD","UST","PAT");

		$ctrData=array(
			array('WO','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0, 'PCT'=>0, 'KPA'=>0, 'PAJ'=>0, 'INDIA'=>0, 'DOCDB'=>0, 
			'DE'=>0, 'GB'=>0, 'FR'=>0, 'RU'=>0, 'TW'=>0, '10CTR'=>0, 'KJD'=>0, 'UJD'=>0, 'EJD'=>0, 'JJD'=>0, 'UST'=>0, 'PAT'=>0 ),
		
			array('WT','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0, 'PCT'=>0, 'KPA'=>0, 'PAJ'=>0, 'INDIA'=>0, 'DOCDB'=>0, 
			'DE'=>0, 'GB'=>0, 'FR'=>0, 'RU'=>0, 'TW'=>0, '10CTR'=>0, 'KJD'=>0, 'UJD'=>0, 'EJD'=>0, 'JJD'=>0, 'UST'=>0, 'PAT'=>0),

			array('WG','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0, 'PCT'=>0, 'KPA'=>0, 'PAJ'=>0, 'INDIA'=>0, 'DOCDB'=>0, 
			'DE'=>0, 'GB'=>0, 'FR'=>0, 'RU'=>0, 'TW'=>0, '10CTR'=>0, 'KJD'=>0, 'UJD'=>0, 'EJD'=>0, 'JJD'=>0, 'UST'=>0, 'PAT'=>0),

			array('WC','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0, 'PCT'=>0, 'KPA'=>0, 'PAJ'=>0, 'INDIA'=>0, 'DOCDB'=>0, 
			'DE'=>0, 'GB'=>0, 'FR'=>0, 'RU'=>0, 'TW'=>0, '10CTR'=>0, 'KJD'=>0, 'UJD'=>0, 'EJD'=>0, 'JJD'=>0, 'UST'=>0, 'PAT'=>0),

			array('SK','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0, 'PCT'=>0, 'KPA'=>0, 'PAJ'=>0, 'INDIA'=>0, 'DOCDB'=>0, 
			'DE'=>0, 'GB'=>0, 'FR'=>0, 'RU'=>0, 'TW'=>0, '10CTR'=>0, 'KJD'=>0, 'UJD'=>0, 'EJD'=>0, 'JJD'=>0, 'UST'=>0, 'PAT'=>0)
		 	);			
		$memData=array(
			array('WO','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0 ), 
			array('WT','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0 ), 
			array('WG','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0 ), 
			array('WC','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0 ), 
			array('SK','KR'=>0, 'US'=>0, 'CN'=>0, 'JP'=>0, 'EP'=>0 )

			);


		$ctgoryData=array(
			'WO' => array ('KR'=> array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'US'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),	
							'EP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'CN'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'JP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 )
							),
			'WT' => array ('KR'=> array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'US'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),	
							'EP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'CN'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'JP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 )
							),
			'WG' => array ('KR'=> array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'US'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),	
							'EP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'CN'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'JP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 )
							),
			'WC' => array ('KR'=> array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'US'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),	
							'EP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'CN'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'JP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 )
							),
			'SK' => array ('KR'=> array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'US'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),	
							'EP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'CN'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 ),
							'JP'=>array( 'basic'=>0, 'number'=>0, 'step'=>0, 'integrated'=>0, 'easy'=>0, 'judge'=>0 )
							),


		);	


		$server=array("93","94","105");
#		$server=array("91", "92", "93", "94", "95", "96", "97", "98", "101", "102", "103", "104", "105", "106", "107", "235", "236", "237", "238", "239", "240", "243", "244", "141", "142", "143", "144", "145", "146", "147");
#		$server=array("91", "92", "93", "94", "95", "96", "97", "98", "101", "102", "243", "244");

#		$server=array("243");
		#$server=array("102","243");

		$service=array("WT","WO","WG","WC","SK");
#		$country=array("KR","US","EP","CN","JP");
		$country=array("US");
		$category= array('basic', 'number', 'step','integrated', 'easy', 'judge');		
		#$category= array('judge');		
		$ctr=0; $allctr=0;
		$i=99;
		$serN="";

		foreach($date as $dt){
			$nowdate=$year.$month.$dt;
			if(!empty($server)){
				foreach($server as $serv){
					echo "serv : $serv \n";
					foreach($service as $svc){
					if(file_exists("$root_dir/${serv}_server/service/${serv}_${svc}_${nowdate}")){
#						if(file_exists("$root_dir/query/${serv}/20180327_10")){
							$fp=fopen("$root_dir/${serv}_server/service/${serv}_${svc}_${nowdate}" , "r");
#							$fp=fopen("$root_dir/query/${serv}/20180327_10" , "r");
							while( $line = fgets($fp, 1024*10) ){
								if(strpos($line, "svc=WO" )){	
									$i=0;
									lineReadWOWT(trim($line), $ctrData, $ctgoryData , $i , $serv, $dt);
								}else if(strpos($line,"svc=WT")){
									$i=1;
									lineReadWOWT(trim($line), $ctrData, $ctgoryData, $i , $serv, $dt);
								}else if(strpos($line,"svc=WG")){
									$i=2;
									lineReadWOWT(trim($line), $ctrData, $ctgoryData, $i, $serv, $dt);
								}else if(strpos($line,"svc=WC")){
									$i=3;
									lineReadWOWT(trim($line), $ctrData, $ctgoryData, $i, $serv, $dt);
								}else if(strpos($line,"svc=SK")){
									$i=4;
									lineReadWOWT(trim($line), $ctrData, $ctgoryData, $i, $serv, $dt);
								}
							}
						fclose($fp);
						}
						$i=99;
					}
				}
			}


			//날짜 배열 조회해서 파일에 입력
			if($dt=='01')
				$sc = fopen("${root_dir}/country/count","w");
			else 
				$sc = fopen("${root_dir}/country/count","a");

			for ($i=0; $i<5 ; $i++){
				foreach($ctrName as $ctrn){
					$svc=$ctrData[$i][0];
					$arrCtr=$ctrData[$i][$ctrn];
					fwrite($sc, "$svc|$nowdate|$ctrn|$arrCtr\n");
					$ctrData[$i][$ctrn]=0;
				}		
			}

			//카테고리 배열 조회해서 파일에 입력
			if($dt=='01')
				$sc = fopen("${root_dir}/category/count","w");
			else
				$sc = fopen("${root_dir}/category/count","a");
			
			foreach( $service as $svc  ){
				foreach( $country as $ctr){
					foreach( $category as $tb){
						$arrCtr=$ctgoryData[$svc][$ctr][$tb];
						fwrite( $sc, "$svc|$nowdate|$ctr|$tb|$arrCtr\n"  );
						$ctgoryData[$svc][$ctr][$tb]=0;
					}
				}
			}


		}
	}//end of file_get_start function()

	file_get_start()


?>
