var express = require('express');
var app = express();
var mysql = require('mysql');
var connection = mysql.createConnection({

	host : '203.236.86.174',
	user : 'root',
	password : 'wips',
	database : 'SearchLogdb'

});

var bodyParser = require('body-parser');
app.use(bodyParser.json());
app.use(bodyParser.urlencoded());
app.set('views','./views_file');
app.set('view engine', 'jade');

connection.connect(function(err){
    if (err){
        console.log('mysql connection is fail ');
        console.log(err);
        throw err;
    } else {
        console.log('mysql connection is success\nhttp://203.236.86.149:8180/search/');
    }
});

app.get('/search/:id', function(req, res){
	var chartString ="";
	var id=req.params.id;
	var insert_flag=1
	var chartArr = new Array();
	for(var i = 0 ; i<31; i++){
		chartArr[i] = new Array(4);
	}
  
	console.log("id : "+id);
	switch(id){
		case 'date':
			//날짜 관련 검색
//			res.redirect('date');
			res.render('date');
			console.log("swtich : "+id);
			break ;
		case 'main' :
//main 화면, 네비게이션 클릭시 id 값
			res.render('main');
			break;
		case 'service':
			//서비스-국가별
			res.render('service');
			break;
		case 'topmember':
			//상위 멤버 조회
			res.render('topmember');
			break;
		case 'category':
			//카테고리별 카운트
			res.render('category');
			break;
		case 'server':
			res.render('server');
			break;
		case 'country':
			res.render('country');
			break;
		case 'daily':
			res.render('daily');
			console.log("swtich2 : "+id);
			break;
		default : 
			break;
	}
});

app.post('/search/:id', function(req, res){
	var chartString="";
	var id=req.params.id;
	var inputDate = "";
	var outputDate = "";
	var query_sql = "";

	var server = "";
	var country = "";
	var service = "";
	var multiFlag = 0;

	console.log("test1");
	switch(id){
		case 'daily':
			if(req.body.okFlag){
				inputDate = req.body.inDate;
				outputDate = req.body.outDate;
				servie = req.body.service;
				query_sql = 'SELECT  B.`daily_date` FROM  `DATE` AS B  WHERE B.`daily_date` BETWEEN "'+inputDate+'" and "'+outputDate+'"  ';

				connection.query(query_sql , function(err, dailyData, fields){
				if(!err){
					//1.DATE 데이터 가공		
					var totalDate = change_date( dailyData, 0 );
					console.log("날짜데이터가공 : ");
					console.log(totalDate);
					//2. WO / WT / WG 서비스 별로 분리한다
					total_servie_insert( totalDate, inputDate, outputDate, req, res );
				}
				else
					console.log('Error while performing query.', err);
				});
			}
			break;
		case 'category':
			if(req.body.okFlag){
				inputDate = req.body.inDate;
				outputDate = req.body.outDate;
				country = req.body.country;
				service = req.body.service;

//				if( (country==10CTR) || (country==LN) || )

				query_sql= 'SELECT A.`category_num` AS c_num , sum( A.count ) AS count FROM `CATEGORY_COUNT`     AS A INNER JOIN `DATE` AS B ON A.`dateSeq_num` = B.`dateSeq_num`';
				if(country=="ALL" && service=="ALL"){
					query_sql +='WHERE B.daily_date BETWEEN "'+inputDate+'" and "'+outputDate+'" GROUP BY A.`category_num` ' ;
				}else if(country=="ALL"){
					query_sql +='WHERE `service_num` = "'+service+'" AND B.daily_date BETWEEN "'+inputDate+'" and "'+outputDate+'" GROUP BY A.`category_num` ' ;
				}else if(service=="ALL"){
					query_sql +='WHERE `country_num` = "'+country+'" AND B.daily_date BETWEEN "'+inputDate+'" and "'+outputDate+'" GROUP BY A.`category_num` ' ;
				}else{
					query_sql+='WHERE `country_num` = "'+country+'" AND `service_num` = "'+service+'" AND B.daily_date BETWEEN "'+inputDate+'" and "'+outputDate+'" GROUP BY A.`category_num` ' ;
				}

				connection.query( query_sql, function(err, categoryData, fields){
				if(!err){
					res.json(categoryData);
				}else
					console.log('Error while performing query.', err);
				});
			}
			break;
		case 'country' : 
			if(req.body.okFlag){
				var allCountry = "";
				inputDate=req.body.inDate;	
				outputDate=req.body.outDate;
				country = req.body.country;
				service = req.body.service;
				
				country = country.split(",");
//SELECT `country_num`, sum(count) FROM `COUNTRY_COUNT` as A INNER JOIN `DATE` AS B ON A.`dateSeq_num` = B.dateSeq_num WHERE `service_num`= 'WO' and  B.daily_date BETWEEN '2017-04-01' AND '2017-04-31' group by `country_num`
				query_sql= 'SELECT sum( A.count ) AS count , A.`country_num` AS country_num , sum( A.count ) AS count FROM `COUNTRY_COUNT`     AS A INNER JOIN `DATE` AS B ON A.`dateSeq_num` = B.`dateSeq_num`';


				if(country[0]!="ALL"){
				//country 설정
					allCountry += "(";
					for(var i=0; i<country.length; i++){
						allCountry += "'"+country[i]+"'";	
						if((country.length -1)!=i)
							allCountry += ",";
						
					}
					allCountry += ")";
					console.log("allCountry:" +allCountry);
				}


				if(service=="ALL" && country[0]=="ALL"){
					query_sql +='WHERE B.`daily_date` BETWEEN "'+inputDate+'" AND "'+outputDate+'" group by `country_num`';
				}
				else if(service=="ALL"){
					query_sql +='WHERE `country_num` in '+allCountry+' and  B.`daily_date` BETWEEN "'+inputDate+'" AND "'+outputDate+'" group by `country_num`';

				}
				else if(country[0]=="ALL"){
					console.log("service"+service);
					query_sql += 'WHERE A.`service_num`= "'+service+'" AND  B.`daily_date` BETWEEN "'+inputDate+'" AND "'+outputDate+'" group by `country_num`';
				}else{
					console.log("service"+service);
					query_sql += 'WHERE `country_num` in '+allCountry+' and  A.`service_num`= "'+service+'" AND  B.`daily_date` BETWEEN "'+inputDate+'" AND "'+outputDate+'" group by `country_num` ';

				}


				connection.query( query_sql, function(err, countryData, fields){
				if(!err){
					res.json(countryData);
				}else
					console.log('Error while performing query.', err);
				});

			}
			break;
		case 'server' :
			if(req.body.okFlag){
				inputDate = req.body.inDate;
				outputDate = req.body.outDate;
				server = req.body.server;
			}
			break;

		case 'service' : 
			if(req.body.okFlag){
			}
			break;
  	}
});

//WO WG WT 모든 서비스를 선택 할 경우
function total_servie_insert( totalDate,  inputDate, outputDate , req, res){
	var query_sql ="";
	var count = 5;
	var loca = 0;
	var countSize = 5;
	var serviceArr = new Array(); 
	for(var i = 0 ; i<totalDate.length ; i++){
		//chage_date 값
		serviceArr[i] = new Array(3);
	}

	var chartArr = new Array();
	var dateArr = new Array();
	for(var i = 0 ; i<totalDate.length; i++){
		chartArr[i] = new Array(5);
		chartArr[i] = new Array(5);
		dateArr[i] = new Array(5);
	}
	var totalArray = new Array();
	var checkService = new Array();
	var service = req.body.service;
	var allService = "";
	if(service.length >0){
		console.log("service"+service);	
		service = service.split(",");
		console.log("service"+service[1]);	
		console.log("service"+service.length);	
		if(service[0]!="ALL"){
			//service 설정
			count = service.length -1;
			countSize = count;
			console.log("countSize: "+countSize);
			allService += "(";
			for(var i=0; i<service.length-1; i++){
				allService += "'"+service[i]+"'";	
				if((service.length -2)!=i)
					allService += ",";
			}
			allService += ")";
			console.log("aaaaaaaaaa : "+allService);
		}

		if(service=="ALL"){
			query_sql = 'SELECT A.`seq_num`, B.`daily_date`, A.`service_num`, A.`count` FROM `DAILY_COUNT` AS A INNER JOIN `DATE` AS B ON A.`dateSeq_num` = B.`dateSeq_num` WHERE A.`service_num` in ("WO", "WG", "WT","WC","SK") AND B.`daily_date`  BETWEEN "'+inputDate+'" and "'+outputDate+'" ORDER BY B.`daily_date` ASC , A.`service_num` DESC';
		}else{
			query_sql = 'SELECT A.`seq_num`, B.`daily_date`, A.`service_num`, A.`count` FROM `DAILY_COUNT` AS A INNER JOIN `DATE` AS B ON A.`dateSeq_num` = B.`dateSeq_num` WHERE A.`service_num` in '+allService+' AND B.`daily_date`  BETWEEN "'+inputDate+'" and "'+outputDate+'" ORDER BY B.`daily_date` ASC , A.`service_num` DESC';

		}
		connection.query( query_sql , function(err, selectdailyData, fields){
			if(!err){
				serviceArr=change_date( selectdailyData , 2 );
				for(var i=0; i<totalDate.length ; i++){
					chartArr[i][0] =totalDate[i][0];
					console.log("DATE집어넣기"+chartArr[i][0]);
					console.log("totalDate"+totalDate[i][0]);
					loca = 1;				
					console.log("count : "+count+"loca : "+loca);
					for(var j=i; j<count; j++){
						if( totalDate[i][0] == serviceArr[j][0] ){
							/*totalDate 배열은 X월 01일 부터 X월 31일까지 영문-> YYYY-MM-DD 식으로 표현
							 serviceArr [2017-09-01][WO][count1]
 							 serviceArr [2017-09-01][WT][count1]
								     	[2017-09-02][WO][count2] 이런 형식을
							            [2017-09-02][WT][count2]
							 chartArr[1][wo_count1][wt_count1]
							 chartArr[2][wo_count2][wt_count2]
							*/

							chartArr[i][loca]=serviceArr[j][2]; //WG -> WO -> WT

							/*

			total[i][3]=totalWTMonthCount;
			total[i][4]=totalWOMonthCount;
			total[i][5]=totalWGMonthCount;
			total[i][6]=totalWCMonthCount;
			total[i][7]=totalSKMonthCount;
			total[i][8]=month;
			total[i][9]=dates;
			total[i][10]=month_c;

							*/



							dateArr[i][1]=serviceArr[j][3];  
							dateArr[i][2]=serviceArr[j][4];  
							dateArr[i][3]=serviceArr[j][5];  
							dateArr[i][4]=serviceArr[j][6];  
							dateArr[i][5]=serviceArr[j][7]; 
							dateArr[i][6]=serviceArr[j][8];
							dateArr[i][7]=serviceArr[j][9];
							dateArr[i][8]=serviceArr[j][10];
 
							
							console.log("count집어넣기"+chartArr[i]);
							loca++;
						}
					}
					count = count+countSize;
					var totalJson = new Object();
					//totalJson : views_file->daily.jade에 보내는 형태
					if(service[0]=="ALL"){
						//service를 전체 클릭 했을 경우
						totalJson.dailyDate = chartArr[i][0];
						totalJson.serviceWT = chartArr[i][1];
						
						totalJson.MonthCountWT = dateArr[i][1];
						totalJson.serviceWO = chartArr[i][2];
						totalJson.MonthCountWO = dateArr[i][2];
						totalJson.serviceWG = chartArr[i][3];	
						totalJson.MonthCountWG = dateArr[i][3];
						totalJson.serviceWC = chartArr[i][4];	
						totalJson.MonthCountWC = dateArr[i][4];
						totalJson.serviceSK = chartArr[i][5];	
						totalJson.MonthCountSK = dateArr[i][5];

						totalJson.month = dateArr[i][6];
						totalJson.dates = dateArr[i][7];
						totalJson.monthCnt = dateArr[i][8];



					}else{
						for(var s=1; s<(service.length)+1; s++){
							console.log("chartArr: "+chartArr);
							totalJson.dailyDate = chartArr[i][0];
							console.log("service[s] : "+service[s-1]);
							if(service[s-1]=="WT"){
								totalJson.serviceWT=chartArr[i][s];
								totalJson.MonthCountWT = dateArr[i][1];
							}else if(service[s-1]=="WO"){
								totalJson.serviceWO=chartArr[i][s];
								totalJson.MonthCountWO = dateArr[i][2];
							}else if(service[s-1]=="WG"){
								totalJson.serviceWG=chartArr[i][s];
								totalJson.MonthCountWG = dateArr[i][3];
							}else if(service[s-1]=="WC"){
								totalJson.serviceWC=chartArr[i][s];
								totalJson.MonthCountWC = dateArr[i][4];
							}else if(service[s-1]=="SK"){
								totalJson.serviceSK=chartArr[i][s];
								totalJson.MonthCountSK = dateArr[i][5];
							}
							totalJson.month = dateArr[i][6];
							totalJson.dates = dateArr[i][7];
							totalJson.monthCnt = dateArr[i][8];
						}
					}
					console.log("totalJson: "+ totalJson.MonthCountWC);
					//totalJson을 totalArray로 묶어서 보냅니다.
					totalArray.push(totalJson);		
				}
//			console.log("chartArr: "+chartArr);
//			console.log("totalArray: "+ totalJson.MonthCountWC);
			}else{
				console.log('Error while performing query', err);
			}
			if(chartArr){
				res.json(totalArray);
			}
		});
	}
}


function change_date( dailyData , flag ){
	var totalWOMonthCount = 0;
	var totalWTMonthCount = 0;
	var totalWGMonthCount = 0;
	var totalWCMonthCount = 0;
	var totalSKMonthCount = 0;
	var lastmonth ="";


	console.log( "날짜 갯수"+ dailyData.length);

	var total = new Array();
	for( var i=0; i < dailyData.length; i++){
		//Array는 서비스 개수 WT WO WG WC SK 
		total[i] = new Array(10);
		/*
		total[3] = Month Count 30/31 아닌날
		total[4] = Month Count 30/31 인날
		*/
	}


	for( var i=0; i<dailyData.length; i++){
		for( var j=3; j<9; j++){
			total[i][j]=0;
		}
	}


	for( var i=0; i<dailyData.length; i++){
//		console.log(dailyData[i].daily_date);
		//2017-06-29T15:00:00.000Z
		//앞 10글자 자르기
		var dateString  =dailyData[i].daily_date.toString();
//		console.log(dateString);
		//Tue Jun 27 2017 00:00:00 GMT+0900 (KST)
		var dateArr = dateString.split(' ');
		var days = dateArr[0];  //Fri
		var month_s = dateArr[1]; //Jun
		var dates = dateArr[2]; //30
		var year = dateArr[3]; //2017
		var month;
		var month_c;
		var lastDate = ['31','28','31','30','31','30','31','31','30','31','30','31'];

        switch(month_s){
          case 'Jan' :
            month='01';
			month_c=0;
            break;
          case 'Feb' :
            month='02';
			month_c=1;
            break;
          case 'Mar' :
            month='03';
			month_c=2;
            break; 
          case 'Apr' : 
            month='04';
			month_c=3;
            break;
          case 'May' :
            month='05';
			month_c=4;
            break;
          case 'Jun' : 
            month='06';
			month_c=5;
            break;
          case 'Jul' :
            month='07';
			month_c=6;
            break;
          case 'Aug' : 
            month='08';
			month_c=7;
            break;
          case 'Sep' : 
            month='09';
			month_c=8;
            break;
          case 'Oct' :
            month='10';
			month_c=9;
            break;
          case 'Nov' :
            month='11';
			month_c=10;
            break;
          case 'Dec' :
            month='12';
			month_c=11;
            break;
        }

		// 3.월별 서비스 : 9월 -> 10월에서 넘어가는 구간 체크
		if(lastmonth==""){
			lastmonth=month;
		}

		// 1.일간 서비스 : 2017-01-01 형태로 데이터 표시
		var daily_date=year+"-"+month+"-"+dates;

		if(flag==0){
			total[i][0] = daily_date;
		}

		//WT,WO,WG,WC,SK All 5
		// 2017-03-01 WT count 
		// 2017-03-01 WO count 
		// 2017-03-01 WG count 
		// 2017-03-01 WC count 
		// 2017-03-01 SK count 이런 형식 
		else if(flag==2){
			total[i][0]=daily_date;
			total[i][1]=dailyData[i].service_num;
			total[i][2]=dailyData[i].count;

			//3. 월간 서비스 : 9월 10월로 넘어갔을때 총 합산 초기화
			if(lastmonth!=month){
				lastmonth=month;
				totalWOMonthCount = 0;
				totalWTMonthCount = 0;
				totalWGMonthCount = 0;
				totalWCMonthCount = 0;
				totalSKMonthCount = 0;
			}

			//3. 월간 서비스 : 각 서비스 월별 체크를 위해 총 합계 계산
			switch( total[i][1] ){
				case 'WO' :
					totalWOMonthCount += dailyData[i].count;
					break;
				case 'WT' :
					totalWTMonthCount += dailyData[i].count;
					break;
				case 'WG' :
					totalWGMonthCount += dailyData[i].count;
					break;
				case 'WC' :
					totalWCMonthCount += dailyData[i].count;
					break;
				case 'SK' :
					totalSKMonthCount += dailyData[i].count;
					break;
			}

			console.log(total[i][0]+","+ total[i][1]+":"+total[i][2]);
//			if(dates==lastDate[month_c]  ){
			total[i][3]=totalWTMonthCount;
			total[i][4]=totalWOMonthCount;
			total[i][5]=totalWGMonthCount;
			total[i][6]=totalWCMonthCount;
			total[i][7]=totalSKMonthCount;
			total[i][8]=month;
			total[i][9]=dates;
			total[i][10]=month_c;
//			}

			console.log("total WTMonthCount: "+ total[i][3]);
			console.log("total WOMonthCount: "+ total[i][4]);
			console.log("total WGMonthCount: "+ total[i][5]);
			console.log("total WCMonthCount: "+ total[i][6]);
			console.log("total SKMonthCount: "+ total[i][7]);
		}
	}
	return total;
}


app.listen(8180, function(){
	console.log('Connected, 8180 port!');
});
