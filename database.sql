/*
 Navicat Premium Dump SQL

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 80041 (8.0.41)
 Source Host           : localhost:3306
 Source Schema         : hackhunt

 Target Server Type    : MySQL
 Target Server Version : 80041 (8.0.41)
 File Encoding         : 65001

 Date: 04/05/2025 21:12:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `background_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `role_id` int NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admins_role_id_index`(`role_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admins
-- ----------------------------

-- ----------------------------
-- Table structure for friends
-- ----------------------------
DROP TABLE IF EXISTS `friends`;
CREATE TABLE `friends`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_one_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_two_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','accepted','blocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `friends_user_one_id_user_two_id_unique`(`user_one_id` ASC, `user_two_id` ASC) USING BTREE,
  INDEX `friends_user_two_id_foreign`(`user_two_id` ASC) USING BTREE,
  CONSTRAINT `friends_user_one_id_foreign` FOREIGN KEY (`user_one_id`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `friends_user_two_id_foreign` FOREIGN KEY (`user_two_id`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of friends
-- ----------------------------

-- ----------------------------
-- Table structure for hall_of_fame
-- ----------------------------
DROP TABLE IF EXISTS `hall_of_fame`;
CREATE TABLE `hall_of_fame`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rank` int NOT NULL,
  `points` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `hall_of_fame_program_id_foreign`(`program_id` ASC) USING BTREE,
  INDEX `hall_of_fame_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `hall_of_fame_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `hall_of_fame_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hall_of_fame
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2025_02_28_230741_create_roles_table', 1);
INSERT INTO `migrations` VALUES (3, '2025_02_28_230749_create_admins_table', 1);
INSERT INTO `migrations` VALUES (4, '2025_02_28_230805_create_permission_table', 1);
INSERT INTO `migrations` VALUES (5, '2025_02_28_230816_create_permission_role_table', 1);
INSERT INTO `migrations` VALUES (6, '2025_04_17_025353_create_reports_table', 1);
INSERT INTO `migrations` VALUES (7, '2025_04_17_025457_create_programs_table', 1);
INSERT INTO `migrations` VALUES (8, '2025_04_17_025555_create_program_invites_table', 1);
INSERT INTO `migrations` VALUES (9, '2025_04_17_025623_create_friends_table', 1);
INSERT INTO `migrations` VALUES (10, '2025_04_17_030943_create_report_comments_table', 1);
INSERT INTO `migrations` VALUES (11, '2025_04_17_031034_create_report_logs_table', 1);
INSERT INTO `migrations` VALUES (12, '2025_04_17_031633_create_hall_of_fame_table', 1);
INSERT INTO `migrations` VALUES (13, '2025_04_20_075121_create_program_user_table', 1);

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `device_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE,
  INDEX `password_reset_tokens_email_index`(`email` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------
INSERT INTO `password_reset_tokens` VALUES ('asamaaly70@gmail.com', '$2y$12$vbtt2FbozIKzybO0ZEoJLezEC/RPayu/mSp8sokXZ3Yl6tZE0ahZm', 0, NULL, NULL, '2025-05-03 22:09:50');

-- ----------------------------
-- Table structure for permission_role
-- ----------------------------
DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `permission_role_role_id_index`(`role_id` ASC) USING BTREE,
  INDEX `permission_role_permission_id_index`(`permission_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permission_role
-- ----------------------------

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permissions
-- ----------------------------

-- ----------------------------
-- Table structure for program_invites
-- ----------------------------
DROP TABLE IF EXISTS `program_invites`;
CREATE TABLE `program_invites`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Accepted','Rejected','Expired','Pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `invited_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `expire_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `program_invites_program_id_foreign`(`program_id` ASC) USING BTREE,
  INDEX `program_invites_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `program_invites_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `program_invites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of program_invites
-- ----------------------------
INSERT INTO `program_invites` VALUES (2, '93ed0cda-df8f-4618-ad6e-b087f25d08fe', '9052f6ac-04ff-4e8f-abbb-09101d454600', 'Accepted', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '2025-08-11 17:41:50', '2025-05-03 17:23:52', '2025-05-03 18:08:30');
INSERT INTO `program_invites` VALUES (7, '93ed0cda-df8f-4618-ad6e-b087f25d08fe', '67b4cd5d-dabd-4ae5-b96e-b252c4b6f29d', 'Pending', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '2025-05-10 17:46:55', '2025-05-03 17:40:48', '2025-05-03 17:57:53');
INSERT INTO `program_invites` VALUES (8, 'f74dc195-1407-4959-a186-29f78bbd1acf', '9052f6ac-04ff-4e8f-abbb-09101d454600', 'Pending', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '2025-05-24 21:07:12', NULL, NULL);
INSERT INTO `program_invites` VALUES (9, '93ed0cda-df8f-4618-ad6e-b087f25d08fe', 'd848ecf1-fc16-4585-ad87-6ff7691171fb', 'Rejected', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '2025-05-10 18:48:53', '2025-05-03 18:48:53', '2025-05-03 18:48:53');
INSERT INTO `program_invites` VALUES (10, '6db1a5b0-5a1a-4099-b562-2e6b9d072317', 'd848ecf1-fc16-4585-ad87-6ff7691171fb', 'Accepted', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '2025-05-10 20:58:29', '2025-05-03 20:58:29', '2025-05-03 20:59:15');

-- ----------------------------
-- Table structure for program_user
-- ----------------------------
DROP TABLE IF EXISTS `program_user`;
CREATE TABLE `program_user`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `program_user_program_id_foreign`(`program_id` ASC) USING BTREE,
  INDEX `program_user_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `program_user_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `program_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of program_user
-- ----------------------------
INSERT INTO `program_user` VALUES (1, '93ed0cda-df8f-4618-ad6e-b087f25d08fe', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '2025-05-01 21:09:59', NULL);
INSERT INTO `program_user` VALUES (4, 'bc29dd43-dc65-4f12-83de-bbcc39be444a', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', NULL, NULL);
INSERT INTO `program_user` VALUES (5, 'f8707968-2da9-475a-9deb-dae8f8a9e4b2', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', NULL, NULL);
INSERT INTO `program_user` VALUES (6, '5c96065c-19f2-4e65-adc2-1b0ebdfc4ebd', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', NULL, NULL);
INSERT INTO `program_user` VALUES (7, '5455639a-2034-4ba7-a819-320bd3608dfc', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', NULL, NULL);
INSERT INTO `program_user` VALUES (8, '17ee92d0-efad-4a6c-8719-5a3d0f0253e1', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', NULL, NULL);
INSERT INTO `program_user` VALUES (9, '1450e7f6-11db-4ec2-ab1a-03d59f949026', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', NULL, NULL);
INSERT INTO `program_user` VALUES (10, '5ee94b7d-194f-4d61-9570-675efe1f554c', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', NULL, NULL);
INSERT INTO `program_user` VALUES (13, '93ed0cda-df8f-4618-ad6e-b087f25d08fe', '9052f6ac-04ff-4e8f-abbb-09101d454600', NULL, NULL);
INSERT INTO `program_user` VALUES (14, '6db1a5b0-5a1a-4099-b562-2e6b9d072317', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', NULL, NULL);
INSERT INTO `program_user` VALUES (15, '6db1a5b0-5a1a-4099-b562-2e6b9d072317', 'd848ecf1-fc16-4585-ad87-6ff7691171fb', NULL, NULL);

-- ----------------------------
-- Table structure for programs
-- ----------------------------
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `bounty_range` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `number_of_reports` int NULL DEFAULT NULL,
  `avg_bounty` int NULL DEFAULT NULL,
  `validation_time` int NULL DEFAULT NULL,
  `vulnerabilities_rewarded` int NOT NULL DEFAULT 0,
  `started_at` timestamp NOT NULL,
  `fast_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `rewards` json NULL,
  `target_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `scope` json NULL,
  `description_rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `programs_program_id_unique`(`program_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of programs
-- ----------------------------
INSERT INTO `programs` VALUES (1, '93ed0cda-df8f-4618-ad6e-b087f25d08fe', 'Meta', 'logo/meta.jpg', '500$ - 100,000$', 1, 0, 0, 0, 0, '2025-04-20 10:29:03', 'Meta\'s bug bounty program rewards security researchers for discovering vulnerabilities across Facebook, Instagram, WhatsApp, and other Meta services.', '[{\"type\": \"cash\", \"amount\": \"500-1000\"}, {\"type\": \"cash\", \"amount\": \"1000-5000\"}, {\"type\": \"cash\", \"amount\": \"5000-15000\"}, {\"type\": \"cash\", \"amount\": \"15000-100000\"}]', 'Description of the target for the program.', '[\"https://facebook.com\", \"https://m.facebook.com\", \"graph.facebook.com\", \"ads.facebook.com\"]', 'Program rules and conditions.', 'Active', NULL, '2025-05-01 20:51:23');
INSERT INTO `programs` VALUES (5, '6b6beef7-4f99-45a8-9e09-db34f3613d36', 'Google', 'logo/google.png', '100$ - 31,337$', 0, 0, 0, 0, 0, '2025-05-01 20:44:24', 'Google invites researchers to report vulnerabilities in products like Android, Chrome, and Google Cloud through its long-standing Vulnerability Reward Program.', '[{\"type\": \"cash\", \"amount\": \"100-500\"}, {\"type\": \"cash\", \"amount\": \"500-2000\"}, {\"type\": \"cash\", \"amount\": \"2000-10000\"}, {\"type\": \"cash\", \"amount\": \"10000-31337\"}]', 'Description of the target for the program.', '[\"https://google.com\", \"https://accounts.google.com\", \"https://api.google.com\", \"play.google.com\"]', 'Program rules and conditions.', 'Active', '2025-05-01 20:44:24', '2025-05-01 20:44:24');
INSERT INTO `programs` VALUES (6, 'f74dc195-1407-4959-a186-29f78bbd1acf', 'Facebook', 'logo/facebook.png', '500$ - 40,000$', 0, 0, 0, 0, 0, '2025-05-01 20:45:13', 'Facebook\'s program offers bounties for issues across its main platform, as well as Messenger and Oculus, with a focus on user data protection.', '[{\"type\": \"cash\", \"amount\": \"500-1000\"}, {\"type\": \"cash\", \"amount\": \"1000-4000\"}, {\"type\": \"cash\", \"amount\": \"4000-15000\"}, {\"type\": \"cash\", \"amount\": \"15000-40000\"}]', 'Description of the target for the program.', '[\"https://facebook.com\", \"developers.facebook.com\", \"https://api.facebook.com\"]', 'Program rules and conditions.', 'Active', '2025-05-01 20:45:13', '2025-05-01 20:45:13');
INSERT INTO `programs` VALUES (8, 'bc29dd43-dc65-4f12-83de-bbcc39be444a', 'Microsoft', 'logo/microsoft.jpg', '500$ - 250,000$', 0, 0, 0, 0, 0, '2025-05-02 19:40:44', 'Microsoft’s program spans Azure, Office, Windows, and more, offering high rewards for critical issues that impact user security.', '[{\"type\": \"cash\", \"amount\": \"500-2000\"}, {\"type\": \"cash\", \"amount\": \"2000-10000\"}, {\"type\": \"cash\", \"amount\": \"10000-50000\"}, {\"type\": \"cash\", \"amount\": \"50000-250000\"}]', 'Description of the target for the program.', '[\"https://microsoft.com\", \"login.microsoftonline.com\", \"api.office.com\", \"https://support.microsoft.com\"]', 'Program rules and conditions.', 'Active', '2025-05-02 19:40:44', '2025-05-02 19:40:44');
INSERT INTO `programs` VALUES (9, 'f8707968-2da9-475a-9deb-dae8f8a9e4b2', 'Apple', 'logo/apple.png', '500$ - 1,000,000$', 0, 0, 0, 0, 0, '2025-05-02 19:40:50', 'Apple’s program accepts reports for vulnerabilities across iOS, macOS, iPadOS, and more, with high payouts for zero-click and kernel-level bugs.', '[{\"type\": \"cash\", \"amount\": \"500-5000\"}, {\"type\": \"cash\", \"amount\": \"5000-20000\"}, {\"type\": \"cash\", \"amount\": \"20000-100000\"}, {\"type\": \"cash\", \"amount\": \"100000-1000000\"}]', 'Description of the target for the program.', '[\"https://apple.com\", \"https://developer.apple.com\", \"itunes.apple.com\", \"api.apple.com\"]', 'Program rules and conditions.', 'Active', '2025-05-02 19:40:50', '2025-05-02 19:40:50');
INSERT INTO `programs` VALUES (10, '5c96065c-19f2-4e65-adc2-1b0ebdfc4ebd', 'GitHub', 'logo/github.jpg', '555$ - 30,000$', 0, 0, 0, 0, 0, '2025-05-02 19:40:51', 'GitHub encourages responsible disclosure of security issues within its platform, developer tools, and public infrastructure.', '[{\"type\": \"cash\", \"amount\": \"555-1000\"}, {\"type\": \"cash\", \"amount\": \"1000-3000\"}, {\"type\": \"cash\", \"amount\": \"3000-10000\"}, {\"type\": \"cash\", \"amount\": \"10000-30000\"}]', 'Description of the target for the program.', '[\"https://github.com\", \"api.github.com\", \"gist.github.com\"]', 'Program rules and conditions.', 'Active', '2025-05-02 19:40:51', '2025-05-02 19:40:51');
INSERT INTO `programs` VALUES (11, '5455639a-2034-4ba7-a819-320bd3608dfc', 'Twitter', 'logo/twitter.jpg', '140$ - 20,000$', 0, 0, 0, 0, 0, '2025-05-02 19:40:52', 'Twitter’s program welcomes vulnerability reports on Twitter’s website, mobile apps, and APIs, with an emphasis on user data security.', '[{\"type\": \"cash\", \"amount\": \"140-500\"}, {\"type\": \"cash\", \"amount\": \"500-2000\"}, {\"type\": \"cash\", \"amount\": \"2000-8000\"}, {\"type\": \"cash\", \"amount\": \"8000-20000\"}]', 'Description of the target for the program.', '[\"https://twitter.com\", \"api.twitter.com\", \"ads.twitter.com\"]', 'Program rules and conditions.', 'Active', '2025-05-02 19:40:52', '2025-05-02 19:40:52');
INSERT INTO `programs` VALUES (12, '17ee92d0-efad-4a6c-8719-5a3d0f0253e1', 'Airbnb', 'logo/airbnb.jpg', '250$ - 15,000$', 0, 0, 0, 0, 0, '2025-05-02 19:40:54', 'Airbnb rewards researchers for identifying vulnerabilities in their website, mobile apps, and internal tools that could impact guests or hosts.', '[{\"type\": \"cash\", \"amount\": \"250-750\"}, {\"type\": \"cash\", \"amount\": \"750-2000\"}, {\"type\": \"cash\", \"amount\": \"2000-6000\"}, {\"type\": \"cash\", \"amount\": \"6000-15000\"}]', 'Description of the target for the program.', '[\"https://airbnb.com\", \"api.airbnb.com\", \"community.withairbnb.com\"]', 'Program rules and conditions.', 'Active', '2025-05-02 19:40:54', '2025-05-02 19:40:54');
INSERT INTO `programs` VALUES (13, '1450e7f6-11db-4ec2-ab1a-03d59f949026', 'Uber', 'logo/uber.png', '500$ - 10,000$', 0, 0, 0, 0, 0, '2025-05-02 20:21:52', 'Uber’s program covers rider, driver, and delivery platforms, with rewards for impactful issues that could affect user safety or data.', '[{\"type\": \"cash\", \"amount\": \"500-1000\"}, {\"type\": \"cash\", \"amount\": \"1000-2500\"}, {\"type\": \"cash\", \"amount\": \"2500-7000\"}, {\"type\": \"cash\", \"amount\": \"7000-10000\"}]', 'Description of the target for the program.', '[\"https://uber.com\", \"riders.uber.com\", \"partners.uber.com\", \"api.uber.com\"]', 'Program rules and conditions.', 'Active', '2025-05-02 20:21:52', '2025-05-02 20:21:52');
INSERT INTO `programs` VALUES (14, '5ee94b7d-194f-4d61-9570-675efe1f554c', 'Okta', 'logo/okta.png', '100$-500$', 0, 0, 0, 0, 0, '2025-05-03 12:37:17', 'blabl', '[{\"type\": \"cash\", \"amount\": \"100-200\"}, {\"type\": \"cash\", \"amount\": \"200-300\"}, {\"type\": \"cash\", \"amount\": \"300-400\"}, {\"type\": \"cash\", \"amount\": \"400-500\"}]', 'blabl', '[\"https://otka.com\", \"login.otka.com\", \"api.otka.com\", \"labs.otka.com\"]', 'bbb', 'Active', '2025-05-03 12:37:17', '2025-05-03 12:37:17');
INSERT INTO `programs` VALUES (15, '6db1a5b0-5a1a-4099-b562-2e6b9d072317', 'Bedo-Dublin', 'logo/Bedo-Dublin.jpg', '200$-10000$', 1, 0, 0, 0, 0, '2025-05-03 20:54:13', 'Bedo is offering a high blabla and he is taking 10000$ euro per month', '[{\"type\": \"cash\", \"amount\": \"200-300\"}, {\"type\": \"cash\", \"amount\": \"400-900\"}, {\"type\": \"cash\", \"amount\": \"1000-5000\"}, {\"type\": \"cash\", \"amount\": \"5000-10000\"}]', 'Bedo is offering a high blabla and he is taking 10000$ euro per month', '[\"https://bedo.com\"]', 'Bedo is offering a high blabla and he is taking 10000$ euro per month', 'Active', '2025-05-03 20:54:13', '2025-05-03 20:54:13');

-- ----------------------------
-- Table structure for report_comments
-- ----------------------------
DROP TABLE IF EXISTS `report_comments`;
CREATE TABLE `report_comments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `report_comments_report_id_foreign`(`report_id` ASC) USING BTREE,
  INDEX `report_comments_user_id_foreign`(`user_id` ASC) USING BTREE,
  CONSTRAINT `report_comments_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `report_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of report_comments
-- ----------------------------

-- ----------------------------
-- Table structure for report_logs
-- ----------------------------
DROP TABLE IF EXISTS `report_logs`;
CREATE TABLE `report_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `performed_by` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` json NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `report_logs_report_id_foreign`(`report_id` ASC) USING BTREE,
  INDEX `report_logs_performed_by_foreign`(`performed_by` ASC) USING BTREE,
  CONSTRAINT `report_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `report_logs_report_id_foreign` FOREIGN KEY (`report_id`) REFERENCES `reports` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of report_logs
-- ----------------------------
INSERT INTO `report_logs` VALUES (5, '93685010-e8d3-419f-a314-60c8652469cb', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', 'published', '\"Report marked as published.\"', '2025-04-20 08:43:33', '2025-04-20 08:43:33');
INSERT INTO `report_logs` VALUES (6, '93685010-e8d3-419f-a314-60c8652469cb', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', 'published', '\"Report marked as published.\"', '2025-04-20 08:43:43', '2025-04-20 08:43:43');
INSERT INTO `report_logs` VALUES (10, '93685010-e8d3-419f-a314-60c8652469cb', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', 'status_updated', '\"Status changed from Triaged to Triaged. Points: 50, Bounty: 100\"', '2025-05-01 18:12:33', '2025-05-01 18:12:33');
INSERT INTO `report_logs` VALUES (11, '93685010-e8d3-419f-a314-60c8652469cb', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', 'status_updated', '\"Status changed from Triaged to Triaged. Points: 50, Bounty: 100\"', '2025-05-01 18:13:11', '2025-05-01 18:13:11');
INSERT INTO `report_logs` VALUES (12, '93685010-e8d3-419f-a314-60c8652469cb', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', 'status_updated', '\"Status changed from Triaged to Triaged. Points: 500, Bounty: 1000\"', '2025-05-01 18:13:21', '2025-05-01 18:13:21');

-- ----------------------------
-- Table structure for reports
-- ----------------------------
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reporter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `program_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` enum('P1','P2','P3','P4','P5') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('New','Triaged','Duplicate','Informative','Resolved','N/A') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'New',
  `bounty` int NULL DEFAULT NULL,
  `rewarded` tinyint(1) NOT NULL DEFAULT 0,
  `points` int NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachments` json NULL,
  `triaged_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `reports_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of reports
-- ----------------------------
INSERT INTO `reports` VALUES (1, '1febc1bc-c839-44a1-90c6-2afad481335c', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '93ed0cda-df8f-4618-ad6e-b087f25d08fe', 'P1', 'Resolved', 100, 1, 50, 'test Bug', 'RCE', 'blabla', '\"[]\"', '2025-05-01 18:08:05', NULL, 0, '2025-04-20 08:29:46', '2025-05-01 18:08:05');
INSERT INTO `reports` VALUES (2, '93685010-e8d3-419f-a314-60c8652469cb', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '93ed0cda-df8f-4618-ad6e-b087f25d08fe', 'P1', 'Triaged', 1000, 1, 500, 'test Bug', 'RCE', 'blabla', '\"[]\"', '2025-05-01 18:13:21', NULL, 1, '2025-04-20 08:34:39', '2025-05-01 18:13:21');
INSERT INTO `reports` VALUES (3, '99bdb1fe-2875-4e20-8ef3-cdbd09926eb6', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '93ed0cda-df8f-4618-ad6e-b087f25d08fe', 'P2', 'New', NULL, 0, 1000, 'Hblabla', 'SQLi', 'blabla', '\"[\\\"attachments\\\\/oBOw9cso4yol4o1Rk095GWa0i7JK9yIJTwRXpKzr.pdf\\\",\\\"attachments\\\\/VMjejKdPLjl0RFsdb5LBHbA6qFmzSFjD6NAvyNKC.pdf\\\"]\"', NULL, NULL, 0, '2025-05-02 20:42:57', '2025-05-02 20:42:57');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `last_activity` int NULL DEFAULT NULL,
  `remember_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_foreign`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE,
  CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('3dWVicra0QSmxgDDfUqOz3FbNovrOZqplAKVbh8q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR3cyWU4zWXEzVEk2bms0MHN2V2hFYmFiSXNKaEg1YUVQaHF0M0pQOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC8vYXBpL2FwaS9hdXRoL21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1746044267, NULL);
INSERT INTO `sessions` VALUES ('9052f6ac-04ff-4e8f-abbb-09101d454600', '9052f6ac-04ff-4e8f-abbb-09101d454600', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:136.0) Gecko/20100101 Firefox/136.0', '', 1746294877, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiI5MDUyZjZhYy0wNGZmLTRlOGYtYWJiYi0wOTEwMWQ0NTQ2MDAiLCJ0eXBlIjoicmVmcmVzaCIsImlhdCI6MTc0NjI5NDg3NywiZXhwIjoxNzQ4ODg2ODc3fQ.Nq7T5KANdXokVaVmTLfqvbfVxTWQXyUVrSR7DJtfuFV_OdgFPkYrVA1jBKoV_aRlLOdb7BujK2QMLe1ivGWA0t_1oKD-YVifpAV0jzjEcCeZkVnkTX3yxtxgNj_VWdeXOVb-O9zGqwWorPAZGOD2FtuLJ3231ifk3-b9hUr7rc3scHllRv4cY9jMe3avVXaSIRuufaHbj4xTex7f9Brfg3I7-SsdoZzWTvbDmlhWaXcSNnYnhQAPLNQycvzcxzohWONdJCjF3H9DTUgu4AcuOxOPJLo93ao2D4CSqHnZp-2aBCWxVyfMeq5fQBDSumZw1jGCHfw2P0eXtBUQ4YFOfw');
INSERT INTO `sessions` VALUES ('ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:136.0) Gecko/20100101 Firefox/136.0', '', 1746305862, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJhZTlkZWYxYi1mMWI4LTRlMWQtYTBhOC0yZmQ4MmI0YjZlZjUiLCJ0eXBlIjoicmVmcmVzaCIsImlhdCI6MTc0NjMwNTg2MiwiZXhwIjoxNzQ4ODk3ODYyfQ.ZUmevZnrssiJJ65Iw8Ru6YonGSZNfyDjMcngwVSIHLTYloT5epAs8Y4GpT4XMGF5VHK4qLg3aOpu4IYANMXMK21as-Whkoq9hFGLQEYwBaNzEBBCcIEWdltPCJc9elEFsd-aXpOPr6ab35A3yiwH81iIsDs1xF17kUTi3f5sN8StwXXIMJLhq--ILX8DWK-9LvmxzfKtllXiQDIEAwpts6KslRG4tIwR8FOkCs9MbHd7806H4H0MA_Iw_-YG1dYRJeS2CVt1UHZ10fHLBvg7hsnS4711VKmQ7HXnP8iopnV-6FquIz7nOx_WvlO0DqtqTV48XVSVz3e7K1tQyq8rMA');
INSERT INTO `sessions` VALUES ('d848ecf1-fc16-4585-ad87-6ff7691171fb', 'd848ecf1-fc16-4585-ad87-6ff7691171fb', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '', 1746382272, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJkODQ4ZWNmMS1mYzE2LTQ1ODUtYWQ4Ny02ZmY3NjkxMTcxZmIiLCJ0eXBlIjoicmVmcmVzaCIsImlhdCI6MTc0NjM4MjI3MiwiZXhwIjoxNzQ4OTc0MjcyfQ.uvvUCzj-_upYHJP0vI5gpVBrIS1JvykF1M85-s2YJj7_wj1nYNFq44d0HciPULHSYkw3jFBm1xBiu4mlVJqPUC7b8yul873E-M7l5vNozRc5I0re2M2Q3kf3NfoSvphsmkyf47iM7eTpoQWY-qYSkLjnXvkpabbmSAcWW0XT4QZhNMSZj0Tofue1mfYfatvDck9sornLJy69JHkhzu4K_ZMbwpkGqREtvqSijZM-NnLgQdh7Lh-VvBhBYiQGRlKIAUWMfr390Xf6BU5nM0UWDu-xhHgW559VTU7Kojxq2asui3sCiJXbBv9wZ5DOQIMTg1hGJK9StQGylA0sLdCz2Q');
INSERT INTO `sessions` VALUES ('FLdQqYTX7IG7HTI7nN4kzOialNg6etXnhSrafFZM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjYydmpCbU5YWVdWMzk1WWJRZlE3ZFNzWndkRTBFMlQ5c1BGRzJhVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC8vYXBpL2F1dGgvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1746044230, NULL);
INSERT INTO `sessions` VALUES ('Im2MX41OAAed5gVmovw3umeunZQWBxLVdnHgZsYT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjcyOXptbUZjS1BoZXhCc2k2WVRWcXpZOVdtOUtMbFpoUEd4NHRZcCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC8vYXBpL2F1dGgvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1746044196, NULL);
INSERT INTO `sessions` VALUES ('Jt4aFcVCzXDo3f3QbVFnw7IogHuJ3tcIPgO8cUcT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTlhsM3hzT1QzZWlkZ2lBZnYybXExbE1walhmOHdTbldybW1TMVNaTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC8vYXBpL2FwaS9hdXRoL21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1746044288, NULL);
INSERT INTO `sessions` VALUES ('mRfPJTzE2jNPxHhVornUvJDgNuFYENjDQQi64Xiu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVFUcDExNVBnbVBFbXhWRTNVbFlCODQzcmQ3T1FLUlZ3SGhrTWVEWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC8vYXBpL2F1dGgvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1746044297, NULL);
INSERT INTO `sessions` VALUES ('smZlgVIveh9K8dAdPUxHMBIprLVJHeX7LWnAh7vW', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRTZzUE9vcDhZOWUzb0VNckJVamp3WldVVjVaTkptUzQ3akMzdllRSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9maWxlcy9NamVqS2RQTGpsMFJGc2RiNUxCSGJBNnFGbXpTRmpENk5BdnlOS0MucGRmIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1746226668, NULL);
INSERT INTO `sessions` VALUES ('UTGvvaenyBBDCZap2ffXbinoWdGZ72YfVNGaWqcA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:136.0) Gecko/20100101 Firefox/136.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOEs1OFhVNnl1bzRwVndHRGdqV2U2c096YXl2ZzhQMUpEZVV2MXBWciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1745136950, NULL);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `about_me` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default.png',
  `background_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default_background.png',
  `role_id` int NOT NULL DEFAULT 1,
  `rank` int NOT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `total_points` bigint NOT NULL DEFAULT 0,
  `accuracy` double NOT NULL DEFAULT 0,
  `links` json NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthday` date NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `vulnerabilities_count` int NOT NULL DEFAULT 0,
  `engagement_count` int NOT NULL DEFAULT 0,
  `authenticated` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `users_uuid_unique`(`uuid` ASC) USING BTREE,
  UNIQUE INDEX `users_nickname_unique`(`nickname` ASC) USING BTREE,
  UNIQUE INDEX `users_email_unique`(`email` ASC) USING BTREE,
  UNIQUE INDEX `users_phone_number_unique`(`phone_number` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'ae9def1b-f1b8-4e1d-a0a8-2fd82b4b6ef5', 'Osama', 'I am a security researcher.', 'W4lT3R', 'profile.jpg', 'background.jpg', 2, 0, 'Egypt', 1, 0, 0, '\"[\\\"https:\\\\/\\\\/linkedin.com\\\\/in\\\\/johndoe\\\"]\"', 'walter@example.com', '+201007985244', '1998-05-10', NULL, '$2y$12$r39Xy0aZEN5sZ3ajIpQTceu9IDMNfCLXghUtK52nomnlPqxYtg8P6', 0, 0, 0, 1, NULL, '2025-04-20 08:13:25', '2025-05-03 20:57:42');
INSERT INTO `users` VALUES (2, '9052f6ac-04ff-4e8f-abbb-09101d454600', 'Osama', 'I am a security researcher.', 'W4lT3R2', 'profile.jpg', 'background.jpg', 1, 0, 'Egypt', 1, 0, 0, '\"[\\\"https:\\\\/\\\\/linkedin.com\\\\/in\\\\/johndoe\\\"]\"', 'walter2@example.com', '+201097985244', '1998-05-10', NULL, '$2y$12$ZPxgphQA4hWSjTe/gzpv6.1fNMOVLpmc9FFuvXOG6FasgrECJF6lO', 0, 0, 0, 1, NULL, '2025-04-20 12:44:55', '2025-05-03 17:54:37');
INSERT INTO `users` VALUES (3, '1deb6a5a-d556-473d-8113-1c380d02ff7f', 'Hecker3', NULL, 'Updated', 'null', 'null', 1, 0, 'EG', 1, 0, 0, '\"null\"', 'admin@gmail.com', '+201007985212', '1998-05-10', '2025-05-01 22:27:39', '$2y$12$x7q6wKNu/.qlnHMvVz0l6ut/ImCaDxJg54ZM9jujLqFtz5oRRWS7W', 1, 0, 0, 0, NULL, '2025-05-01 22:27:39', '2025-05-02 15:56:35');
INSERT INTO `users` VALUES (5, '67b4cd5d-dabd-4ae5-b96e-b252c4b6f29d', 'Osama', 'I am a security researcher.', 'W4lT3R3', 'profile.jpg', 'background.jpg', 1, 0, 'Egypt', 1, 0, 0, '\"[\\\"https:\\\\/\\\\/linkedin.com\\\\/in\\\\/johndoe\\\"]\"', 'walter3@example.com', '+201098985244', '1998-05-10', NULL, '$2y$12$rXozZOh40RNsks79QWPose7kTMSC3kA2YztjkYRho/0QebNWB0biy', 0, 0, 0, 0, NULL, '2025-05-03 17:39:37', '2025-05-03 17:39:37');
INSERT INTO `users` VALUES (6, 'd848ecf1-fc16-4585-ad87-6ff7691171fb', 'Osama', 'I am a security researcher.', 'W4lT3R-RE', 'profile.jpg', 'background.jpg', 1, 0, 'Egypt', 1, 0, 0, '\"[\\\"https:\\\\/\\\\/linkedin.com\\\\/in\\\\/johndoe\\\"]\"', 'asamaaly70@gmail.com', '+201088985244', '1998-05-10', NULL, '$2y$12$E2AXuBpmQEo7951TKCmX8.fKBZejNbYMslF.owC5jpvwcIm2ns8mO', 0, 0, 0, 1, NULL, '2025-05-03 18:48:26', '2025-05-03 22:10:07');

SET FOREIGN_KEY_CHECKS = 1;
