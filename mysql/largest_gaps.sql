select 
  concat(cf.name, '(', lf.name, ')') as city_from,
  timediff(from_unixtime(ds.sunrise_time), (
    select from_unixtime(i.sunrise_time)
    from daily_schedule i 
    where i.sunrise_time < ds.sunrise_time
    order by i.sunrise_time desc
    limit 1
    )
  ) as gap,
  concat(ct.name, '(', lt.name, ')') as city_to 
from daily_schedule ds
join cities ct on ct.id = ds.city_id
join cities cf on cf.id = (
  select i.city_id
  from daily_schedule i
  where i.sunrise_time < ds.sunrise_time
  order by i.sunrise_time desc
  limit 1
  )
join countries lf on lf.iso = cf.country_code
join countries lt on lt.iso = ct.country_code
order by gap desc
limit 20;
