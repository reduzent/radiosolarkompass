select
  (
    select count(*)
    from radios r 
    join cities c on c.id = r.city_id 
    join countries l on l.iso = c.country_code 
    where r.active = 1 
      and r.operable = 1
      and l.iso = o.iso
  ) as count, 
  name
from countries o
order by count desc, o.name;

