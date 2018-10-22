#! /bin/sh

#server='91 92 93 94 95 96 97 98 101 102 103 104 105 243 244 235 236 237 238 239 240 141 142 143 144 145 146 147'
server='93 94 105'
#date='01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31'
date='03'
#nowdate=`date '+%Y%m%d' -d 'yesterday'`
year='2018'
month='04'



for serv in $server
	do 
	for nd in $date
		do
			echo "server : $serv "
			nowdate=${year}${month}${nd}
			query_file=/data/wipsnext/nexten/log/query.log.$nowdate
			query_bak_file=/data/wipsnext/nexten/log/log_bak/query.log.$nowdate
			search_file=/data/wipsnext/nexten/log/search.log.$nowdate
			search_bak_file=/data/wipsnext/nexten/log/log_bak/search.log.$nowdate

#			if [ -e scp -r wipsnext@10.1.1.$serv:"$query_file" ] || [ -e scp -r wipsnext@10.1.1.$serv:"$search_file" ] ; then
#				scp -r wipsnext@10.1.1.$serv:~wipsnext/nexten/log/query.log.$nowdate /data/wipsplus/search_log 
#				scp -r wipsnext@10.1.1.$serv:~wipsnext/nexten/log/search.log.$nowdate /data/wipsplus/search_log
#			else
#				echo "$query_bak_file and $search_bak_file not exists";
				echo "${yesr}${tarmonth1} no";
				if [ "$nd" = '01' ] ; then
					#ssh -t -t wipsnext@10.1.1.$serv "cd ~wipsnext/nexten/log/log_bak && tar -zxvf query_log201804.tgz && tar -zxvf search_log201804.tgz"
					scp -r wipsnext@10.1.1.$serv:~wipsnext/nexten/log/log_bak/query.log.$nowdate /data/wipsplus/search_log/
				fi
				scp -r wipsnext@10.1.1.$serv:~wipsnext/nexten/log/log_bak/query.log.$nowdate /data/wipsplus/search_log/
				scp -r wipsnext@10.1.1.$serv:~wipsnext/nexten/log/log_bak/search.log.$nowdate /data/wipsplus/search_log/
				
#			fi
			wait
				mv /data/wipsplus/search_log/query.log.$nowdate /data/wipsplus/search_log/$serv.query.log.$nowdate
				mv /data/wipsplus/search_log/search.log.$nowdate /data/wipsplus/search_log/$serv.search.log.$nowdate
				mv /data/wipsplus/search_log/$serv.query.log.$nowdate /data/wipsplus/search_log/${serv}_server;
				mv /data/wipsplus/search_log/$serv.search.log.$nowdate /data/wipsplus/search_log/${serv}_server;
			echo "-----------------------------------------------"
		

	done


done


