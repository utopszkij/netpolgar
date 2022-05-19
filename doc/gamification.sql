select max(darab) as maxdb, tipus
from
(
/* javasolt csoportok száma userenként */
select created_by as userid, count(teams.id) as darab, "team" as tipus
from teams
group by created_by
/* javasolt projektek száma userenként */
union
select created_by as userid, count(projects.id) as darab, "project" as tipus
from projects
group by created_by
/* javasolt viták száma userenként */
union
select created_by as userid, count(polls.id) as darab, "poll" as tipus
from polls
group by created_by
/* inditott események száma userenként */
union
select created_by as userid, count(events.id) as darab, "event" as tipus
from events
group by created_by
/* feltöltött fájlok száma userenként */
union
select created_by as userid, count(files.id) as darab, "file" as tipus
from files
group by created_by
/* üzenetek */
union
select user_id as userid, count(messages.id) as darab, "message" as tipus
from messages
group by user_id
/*  tagságok */
union
select user_id as userid, count(`members`.`id`) as darab, "member" as tipus
from `members`
where `members`.`rank` = "member" and status = 'active'
group by user_id
/* tisztségek */
union
select user_id as userid, count(`members`.`id`) as darab, "rank" as tipus
from `members`
where `members`.`rank` <> "member" and status = 'active'
group by user_id
/* leadott szavazatok */
union
select voted.user_id as userid, count(distinct voted.poll_id) as darab, "vote" as tipus
from voted
group by voted.user_id
order by tipus
) as points
group by tipus



