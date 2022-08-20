
-- start trigger dept_category
	-- table log_dept_category for trigger
		CREATE TABLE `log_dept_category` (
		  `id` int(9) NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`),
		  `kd_ruh` char(1) COLLATE utf8_unicode_ci NOT NULL,
		  `com_ruh` char(50) COLLATE utf8_unicode_ci NOT NULL,
		  `tgl_ruh` datetime NOT NULL,
		  `id_dept_old` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		  `id_dept_new` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		  `dept_category_name_old` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
		  `dept_category_name_new` varchar(100) COLLATE utf8_unicode_ci NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

				-- trigger_insert_dept_category_before
				CREATE TRIGGER `trigger_insert_dept_category_before` BEFORE INSERT ON `dept_category`
				 FOR EACH ROW INSERT INTO log_dept_category(kd_ruh, 
				                                            com_ruh, 
				                                            tgl_ruh, 
				                                            id_dept, 
				                                            dept_category_name) 
				values('i', 
				        user(), 
				        now(), 
				        new.id, 
				        new.dept_category_name)

				-- trigger_update_dept_category_before
				CREATE TRIGGER `trigger_update_dept_category_before` BEFORE UPDATE ON `dept_category`
				 FOR EACH ROW INSERT INTO log_dept_category(kd_ruh, 
				 											com_ruh, 
				 											tgl_ruh, 
				 											id_dept_old, 
				 											id_dept_new, 
				 											dept_category_name_old, 
				 											dept_category_name_new) 
				values('u', 
						user(), 
						now(), 
						old.id, 
						new.id, 
						old.dept_category_name, 
						new.dept_category_name)

				-- trigger_delete_dept_category_before
				CREATE TRIGGER `trigger_delete_dept_category_before` BEFORE DELETE ON `dept_category`
				 FOR EACH ROW INSERT INTO log_dept_category(kd_ruh, 
							                                com_ruh, 
							                                tgl_ruh, 
							                                id_dept, 
							                                dept_category_name) 
				values('d', 
				        user(), 
				        now(), 
				        old.id, 
				        old.dept_category_name)
-- end trigger dept_category


-- update initial_annual contract
UPDATE users 
SET initial_annual = TIMESTAMPDIFF(MONTH, join_date, DATE_ADD(end_date, INTERVAL 3 DAY)) 
WHERE users.emp_status = 'Contract'

-- master_user view
CREATE VIEW master_user AS
SELECT id, nik, first_name, last_name, emp_status, dept_category_id, end_date, initial_annual
FROM users
GROUP BY users.id

-- annual_taken view
CREATE VIEW annual_taken AS
SELECT users.id,
SUM(CASE WHEN leave_transaction.leave_category_id = 1 AND ver_hr = 1 AND ap_hd = 1 AND ap_gm = 1 Then total_day Else 0 END) AS annual_total_day_taken
FROM users
LEFT JOIN leave_transaction 
ON users.id = leave_transaction.user_id
GROUP BY users.id

-- exdo_taken
CREATE VIEW exdo_taken AS
SELECT users.id,
SUM(CASE WHEN leave_transaction.leave_category_id = 2 AND ver_hr = 1 AND ap_hd = 1 AND ap_gm = 1 Then total_day Else 0 END) AS exdo_total_day_taken
FROM users
LEFT JOIN leave_transaction 
ON users.id = leave_transaction.user_id
GROUP BY users.id

-- exdo_entitled view
CREATE VIEW exdo_entitled AS
SELECT users.id, 
SUM(IF( (initial_leave.leave_category_id) = 2, initial, 0)) AS total_exdo_entitled
FROM users
LEFT JOIN initial_leave
ON users.id = initial_leave.user_id
GROUP BY users.id

-- all_leave_entitled view
CREATE VIEW all_leave_entitled AS
SELECT master_user.id, master_user.nik,
CONCAT_WS(' ', master_user.first_name, master_user.last_name) AS name,
master_user.emp_status, master_user.dept_category_id, master_user.end_date,
master_user.initial_annual AS entitled_leave,
exdo_entitled.total_exdo_entitled AS entitled_day_off,
master_user.initial_annual + exdo_entitled.total_exdo_entitled AS total_leave_and_day_off,
annual_taken.annual_total_day_taken AS leave_taken,
exdo_taken.exdo_total_day_taken AS day_off_taken,
annual_taken.annual_total_day_taken + exdo_taken.exdo_total_day_taken AS total_leave_and_day_off_taken,
master_user.initial_annual - annual_taken.annual_total_day_taken AS annual_leave_balance,
exdo_entitled.total_exdo_entitled - exdo_taken.exdo_total_day_taken AS day_off_balance,
(master_user.initial_annual - annual_taken.annual_total_day_taken) + (exdo_entitled.total_exdo_entitled - exdo_taken.exdo_total_day_taken) AS total_leave_and_day_off_balance
FROM master_user
INNER JOIN exdo_entitled 
ON master_user.id = exdo_entitled.id
INNER JOIN annual_taken
ON master_user.id = annual_taken.id
INNER JOIN exdo_taken
ON master_user.id = exdo_taken.id
GROUP BY master_user.id

-- join master_user, exdo_entitled, annual_taken, exdo_taken
SELECT master_user.id, master_user.nik, master_user.first_name, master_user.last_name, master_user.emp_status, master_user.dept_category_id, master_user.end_date, master_user.initial_annual, exdo_entitled.total_exdo_entitled, annual_taken.annual_total_day_taken, exdo_taken.exdo_total_day_taken
FROM master_user
INNER JOIN exdo_entitled 
ON master_user.id = exdo_entitled.id
INNER JOIN annual_taken
ON master_user.id = annual_taken.id
INNER JOIN exdo_taken
ON master_user.id = exdo_taken.id
GROUP BY master_user.id

SELECT TIMESTAMPDIFF(MONTH, '2012-05-05', '2012-06-04')
SELECT DATE_ADD("2017-06-15", INTERVAL 10 DAY);

-- add foreign key
ALTER TABLE users 
ADD CONSTRAINT FK_user_project_id 
FOREIGN KEY (project) REFERENCES user_project(id)

select 
 `user_project`.`id`, 
 `users`.`nik`, 
 `users`.`first_name`, 
 `users`.`last_name`, 
 `p1`.`project_name`, 
 `p2`.`project_name`,
 `p3`.`project_name`
 from `user_project` 
 left join `users` on `users`.`id` = `user_project`.`user_id` 
 left join `project_category` p1 on `user_project`.`proj1` = p1.`id`
 left join `project_category` p2 on `user_project`.`proj2` = p2.`id`
 left join `project_category` p3 on `user_project`.`proj3` = p3.`id`

$date1 = strtotime($value->join_date);
$date2 = strtotime($value->end_date);
$initial_annual = round(($date2 - $date1) / 60 / 60 / 24 / 30);
User::where(['apa' => 'apa'])->update(['initial_annual' => $initial_annual]);

CREATE TABLE `log_dept_category` (
    `id` int(9) NOT NULL AUTO_INCREMENT,PRIMARY KEY (`id`),
    `kd_ruh` char(1) NOT NULL,
	`com_ruh` char(50) NOT NULL,
	`tgl_ruh` datetime NOT NULL,
	`id_dep_category` int(9) NOT NULL,
	`dept_category_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `id_dep_category0` int(9) NOT NULL,
	`dept_category_name0` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`created_at0` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at0` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
	) ENGINE =InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DELIMITER $$
DROP TRIGGER aa.log_dept_category_u $$
CREATE TRIGGER aa.log_dept_category_u AFTER UPDATE ON aa.log_dept_category_u
FOR EACH ROW BEGIN
   INSERT INTO aa.log_dept_category_u(kd_ruh, com_ruh, tgl_ruh, id_dep_category, dept_category_name, created_at, updated_at, id_dep_category0, dept_category_name0, created_at0, updated_at0) values 
   ('u', user(), now(), old.id_dep_category, old.dept_category_name, old.created_at, old.updated_at, new.id_dep_category0, new.created_at0, new.updated_at0);
END;
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER log_dept_category_u AFTER UPDATE ON log_dept_category_u
FOR EACH ROW BEGIN
   INSERT INTO log_dept_category(kd_ruh, com_ruh, tgl_ruh, id_dep_category, dept_category_name, id_dep_category0, dept_category_name0) values 
   ('u', user(), now(), old.id_dep_category, old.dept_category_name, new.id_dep_category0, new.dept_category_name0);
END;
$$
DELIMITER ;

INSERT INTO log_dept_category(kd_ruh, com_ruh, tgl_ruh, id_dep_category, dept_category_name, id_dep_category0, dept_category_name0) values ('u', user(), now(), old.id_dep_category, old.dept_category_name, old.created_at, old.updated_at, new.id_dep_category0)

CREATE TRIGGER `trigger_u` AFTER UPDATE ON `log_dept_category` FOR EACH ROW INSERT INTO log_dept_category(kd_ruh, com_ruh, tgl_ruh, id_dep_category, dept_category_name, id_dep_category0, dept_category_name0) values ('u', user(), now(), old.id_dep_category, old.dept_category_name, new.id_dep_category0, new.dept_category_name0)

SELECT users.id, leave_transaction.total_day FROM users 
LEFT JOIN leave_transaction 
ON users.id = leave_transaction.user_id 
ORDER BY users.id 

SELECT users.id, SUM(leave_transaction.total_day) sum_total_day 
FROM users 
LEFT JOIN leave_transaction 
ON users.id = leave_transaction.user_id 
GROUP BY users.id 

SELECT users.id, SUM(CASE WHEN leave_category_id = 1 AND ver_hr = 0 Then total_day Else 0 END) AS annual_total_day
FROM users
LEFT JOIN leave_transaction 
ON users.id = leave_transaction.user_id
GROUP BY users.id

SELECT users.id, users.first_name,
SUM(CASE WHEN leave_transaction.leave_category_id = 1 AND ver_hr = 1 Then total_day Else 0 END) AS annual_total_day,
SUM(IF( (initial_leave.leave_category_id) = 2, initial, 0)) AS exdo_entitled
FROM users
LEFT JOIN leave_transaction 
ON users.id = leave_transaction.user_id
LEFT JOIN initial_leave
ON users.id = initial_leave.user_id
GROUP BY users.id

--to do
ALTER TABLE `users` ADD `project_category_id_1` INT (9) AFTER `project`, 
ADD `project_category_id_2` INT (9) AFTER `project_category_id_1`, 
ADD `project_category_id_3` INT (9) AFTER `project_category_id_2`;
--

ALTER TABLE users
ADD CONSTRAINT FK_project_category_id1
FOREIGN KEY (project_category_id_1) REFERENCES project_category(id),
ADD CONSTRAINT FK_project_category_id2
FOREIGN KEY (project_category_id_2) REFERENCES project_category(id),
ADD CONSTRAINT FK_project_category_id3
FOREIGN KEY (project_category_id_3) REFERENCES project_category(id);

Users::leftJoin('user_project as up', 'users.id', '=', up.user_id')->leftJoin('project_category as pc1', 'up.proj1, '=', 'pc1.id')->leftJoin('project_category as pc2', 'up.proj2, '=', 'pc2.id')->leftJoin('project_category as pc3', 'up.proj3, '=', 'pc3.id')->get(['users.nik', 'pc1.project_name', 'pc2.project_name', 'pc3.project_name']);

CREATE TABLE `log_users` (
	`id` int(9) NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`),
	`nik` varchar(100) COLLATE utf8_unicode_ci,
	`nik_new` varchar(100) COLLATE utf8_unicode_ci,
	`first_name` varchar(100) COLLATE utf8_unicode_ci,
	`first_name_new` varchar(100) COLLATE utf8_unicode_ci,
	`last_name` varchar(100) COLLATE utf8_unicode_ci,
	`last_name_new` varchar(100) COLLATE utf8_unicode_ci,
	`dept_category_id` int(9),
	`dept_category_id_new` int(9),
	`position` varchar(100) COLLATE utf8_unicode_ci,
	`position_new` varchar(100) COLLATE utf8_unicode_ci,
	`emp_status` varchar(10) COLLATE utf8_unicode_ci,
	`emp_status_new` varchar(10) COLLATE utf8_unicode_ci,
	`join_date` date,
	`join_date_new` date,
	`end_date` date,
	`end_date_new` date,
	`dob` date,
	`dob_new` date,
	`email` varchar(100) COLLATE utf8_unicode_ci,
	`email_new` varchar(100) COLLATE utf8_unicode_ci,
	`rusun` varchar(50) COLLATE utf8_unicode_ci,
	`rusun_new` varchar(50) COLLATE utf8_unicode_ci,
	`rusun_stat` varchar(50) COLLATE utf8_unicode_ci,
	`rusun_stat_new` varchar(50) COLLATE utf8_unicode_ci,
	`project_category_id_1` int(9),
	`project_category_id_1_new` int(9),
	`project_category_id_2` int(9),
	`project_category_id_2_new` int(9),
	`project_category_id_3` int(9),
	`project_category_id_3_new` int(9),
	`initial_annual` float,
	`initial_annual_new` float,
	`active` tinyint(1),
	`active_new` tinyint(1),
	`sp` tinyint(1),
	`sp_new` tinyint(1),
	`ticket` tinyint(1),
	`ticket_new` tinyint(1),
	`request_ip` varchar(100) COLLATE utf8_unicode_ci,
	`created_by` varchar(100) COLLATE utf8_unicode_ci,
	`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
	) ENGINE =InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`, `nik`, `first_name`, `last_name`, `dept_category_id`, `position`, `emp_status`, `nationality`, `join_date`, `end_date`, `dob`, `pob`, `province`, `maiden_name`, `gender`, `id_card`, `email`, `phone`, `address`, `area`, `city`, `education`, `marital_status`, `npwp`, `kk`, `religion`, `dependent`, `bpjs_ketenagakerjaan`, `bpjs_kesehatan`, `rusun`, `rusun_stat`, `race`, `source_company`, `global_id`, `init_date`, `tax_cut_in`, `tax_cut_off`, `reason_off_leaving`, `reentry_to_company`, `reentry_to_otherco`, `remark`, `jpk`, `cob`, `project_category_id_1`, `project_category_id_2`, `project_category_id_3`, `initial_annual`, `active`, `admin`, `hr`, `hd`, `gm`, `user`, `sp`, `ticket`, `prof_pict`, `request_ip`, `created_by`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, 'hr', '$2y$10$hlYfrbIVGUuJb6fuFk8MwuQDNBfaZRkFSnrfa9.lxI4EFO9YPqhi.', NULL, 'HR', 'Test', 3, 'HR Manager', 'Contract', '0', '2018-07-17', '2018-09-16', '2018-07-18', '0', '0', '0', '0', '0', 'defridhining@gmail.com', '0', '0', '0', '0', '0', '0', '0', '0', '0', 0, '0', '0', NULL, NULL, '0', '0', '0', NULL, '0', '0', '0', '0', '0', '0', '0', '0', NULL, NULL, NULL, 2, 1, 0, 1, 0, 0, 0, 0, 0, 'no_avatar.jpg', NULL, NULL, '2018-04-23 00:51:00', '2018-08-01 03:04:43', 'MMSfnEzwyeDgMBigki2qYrWuvjsX2e1qVhB9LYCO1u18ZEdkkQ0aHQBAkK4z'),
(2, 'admin', '$2y$08$6zD6wtKu7V.DiMlfgJqEIezhLLydiTIt9c3ASUrkrEpBoGPnkdvZy', NULL, 'Administrator', NULL, 1, '-', '-', '-', NULL, NULL, NULL, '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', NULL, '-', '-', NULL, NULL, '-', '-', '-', NULL, '-', '-', '-', '-', '-', '-', '-', '-', NULL, NULL, NULL, 0, 1, 1, 0, 0, 0, 0, 0, 0, 'no_avatar.jpg', NULL, NULL, '0000-00-00 00:00:00', '2018-07-26 08:39:44', 'IwIdn4wfWsW6wfNqyjrkt4R1oZjxHFQyFE4fibfoqCxIlnhQOIVlHsIZ9W2b')
