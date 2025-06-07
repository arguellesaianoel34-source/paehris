select table_schema as database_name,
       table_name,
       create_time,
			 update_time
from information_schema.tables
where (create_time > adddate(current_date,INTERVAL -1 DAY)
or update_time > adddate(current_date,INTERVAL -1 DAY))
      and table_schema not in('information_schema', 'mysql',
                              'performance_schema','sys')
      and table_type ='BASE TABLE'
      and table_schema = 'pae' 
order by create_time desc,
         table_schema;