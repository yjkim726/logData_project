
rm */*.*.*.*
rm */service/*_*_*

1. collect_log.sh : crontab 자동 설정
각 서버에서 search, query 모아옴
??_server/ 아래 디렉토리 안에 파일 생성

2. get_query.php : 모아온 query를 이용하여, ??_server/service/ 디렉토리 안에 서비스 별로 파일 생성
service_count 도 생성


3. db_manage.php : db 연결해서 각 테이블에 넣는 작업


4. auto_increase.php : DATE table에 자동으로 어제날짜 데이터 입력 

5. get_country.php : 국가 서비스별 데이터를 country/count.txt에 저장 
   get_country4.php : 국가 서비스별 데이터를 country/count.txt에 저장 
                     + get_membrCd.php 역할 대신 

6. get_category.php : 국가 서비스내 카테고리별  데이터를 category/count.txt에 저장 
7. merge_query_logCounting.sh : membrCd 상위 50개만 나오도록 데이터 추출 > 소요시간 : 15분



실행순서 방법
add_server : 디렉토리에 91_server등 생성 하위 디렉토리에 service 생성
( rm -rf *_server daily country category )
collect_log.sh : 각 서버에서 log파일 가지고 오기 $1 = month
		-- collect_log_test.sh : 9월에서 10월로 넘어가는 부분이나 연도별
		-- 설정도 추가 ( 이거 쓰세요 )

db_initialize.php : db 정보 초기화
auto_increase.php : month별로 추가 argv[1]

rm -rf *_server/*/service_count
rm country/count ; rm category/count ; rm daily/count

./get_query.php 06 ; ./get_country2.php 06 ; ./get_category.php 06 ;
./get_membrCd.php 06

(get_query.php / get_country.php / get_category.php / get_membrCd.php  argv[1])
merge_query_logCounting.sh argv[1]
db_manage.php : db에 입력


일별 데이터 넣기
collect_log_day.sh : 각 서버에 log파일 전날치만 가지고 오기
auto_increase_day.php : month별로 추가 하는 것을 nowdate= 2017??01 일 경우에만 추가
get_query_day.php / get_country2_day.php / get_category_day.php / get_membrCd_day.php
merge_query_logCounting_month.sh 
db_manage.php



-------------------------------------
get_membrCd_somefile.php : 파일명 입력해서 membrCd 조회


2018-01-17 ver 실행방법
ex). 11월이라면 7월이라면 ./collect_log_test.sh만 7 이고 나머지는 07 이런식으로 입력

mkdir membrCd
nohup ./collect_log_test.sh 11 ; nohup ./get_query.php 11 ; nohup ./get_country3.php 11 ; nohup ./get_category.php 11 ; nohup ./get_membrCd.php 11 ; nohup ./merge_query_logCounting.sh 11 ; nohup ./db_manage.php ;  mv membrCd membrCd_11 ; mv nohup.out  nohup_log_201711


-------------------------------------
-------------------------------------
-------------------------------------
-------------------------------------
-------------------------------------

2018-02-02
./get_country3.php -> ./get_country4.php


get_country4에서는 서비스+멤버코드 둘다 가져옵니다. get_membrCd.php 쓰지
마시고 get_country4로 한번에 돌리시면 됩니다. > 해당 관련해서 한번에 스크립트
돌리는 것

nohup ./collect_log_test.sh 11 ; nohup ./get_query.php 11 ; nohup ./get_category.php 11 ; nohup ./start_all.sh ; 
