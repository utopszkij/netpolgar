TABLE `contributors` 
COMMENT='projektben közremüködő önkéntesek';
  `project_id` bigint unsigned DEFAULT NULL COMMENT 'projects táblába mutató pointer',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'users táblába mutató pointer',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'projekt leírása',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'applicant|active|inactive|exited',
  `evaluation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'szöveges értékelés',
  `grade` int DEFAULT NULL COMMENT 'érdemjegy 1-5',
  `start` date DEFAULT NULL COMMENT 'közremüködés kezdete',
  `end` date DEFAULT NULL COMMENT 'közremüködés vége',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,

TABLE `profile_skills` 
COMMENT='profile-skills 1:n kapcsoló tábla';
  `profile_id` bigint unsigned NOT NULL COMMENT 'pointer a profile táblára',
  `skill_id` bigint unsigned NOT NULL COMMENT 'pointer a skills táblára',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `level` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,

TABLE `profiles` 
COMMENT='user profil adatok';
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sysadmin` tinyint(1) NOT NULL,
  `voluntary` tinyint(1) NOT NULL,
  `project_owner` tinyint(1) NOT NULL,
  `publicinfo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
 
TABLE `project_skills` 
COMMENT='project - skills 1:n kapcsoló tábla';
  `project_id` bigint unsigned DEFAULT NULL COMMENT 'pointer a projects táblára',
  `skill_id` bigint unsigned DEFAULT NULL COMMENT 'pointer a skills táblára',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,

TABLE `projects` 
COMMENT='projektek';
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'projekt rövid megnevezése',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'projekt leírása',
  `organisation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'projekt gazda szervezet',
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'szervezet web site-ja',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL 
        COMMENT 'projekt vagy szervezet avatar url',
  `deadline` date DEFAULT NULL COMMENT 'határidő',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL 
        COMMENT 'plan|task|inprogress|suspended|closed|canceled',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'project gazda user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,

TABLE `skills` 
COMMENT='képességeek, fa szerkezet';
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'tulajdonsás megnevezése',
  `parent` bigint NOT NULL COMMENT 'fa szerkezet, pointer a felsőbb szintre',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order` int DEFAULT NULL,


