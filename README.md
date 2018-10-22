
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

