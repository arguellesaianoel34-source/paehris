/*
 Navicat Premium Data Transfer

 Source Server         : _MySQL_Local
 Source Server Type    : MySQL
 Source Server Version : 100132
 Source Host           : localhost:3306
 Source Schema         : pae

 Target Server Type    : MySQL
 Target Server Version : 100132
 File Encoding         : 65001

 Date: 04/03/2022 14:37:03
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for application_customer_system_parts
-- ----------------------------
DROP TABLE IF EXISTS `application_customer_system_parts`;
CREATE TABLE `application_customer_system_parts`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NULL DEFAULT NULL,
  `type` int(2) NULL DEFAULT NULL,
  `unitid` int(11) NULL DEFAULT NULL,
  `unitprice` decimal(10, 2) NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 59 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of application_customer_system_parts
-- ----------------------------
INSERT INTO `application_customer_system_parts` VALUES (1, 1, 1, 19, 8200.00, 1, '2022-03-04 11:58:46', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (2, 2, 1, 19, 1000.00, 1, '2022-03-04 11:58:46', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (3, 3, 1, 21, 60.00, 1, '2022-03-04 11:58:46', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (4, 4, 1, 19, 45.00, 1, '2022-03-04 11:58:46', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (5, 5, 1, 19, 45.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (6, 6, 1, 15, 60.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (7, 7, 1, 19, 810.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (8, 8, 1, 19, 995.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (9, 9, 1, 37, 60.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (10, 10, 1, 19, 16200.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (11, 11, 1, 19, 600.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (12, 12, 1, 19, 589.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (13, 13, 1, 19, 310.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (14, 14, 1, 19, 90.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (15, 15, 1, 19, 25620.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (16, 16, 1, 19, 21000.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (17, 20, 1, 19, 51361.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (18, 21, 1, 19, 41420.45, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (19, 22, 1, 19, 750.00, 1, '2022-03-04 11:58:47', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (20, 24, 1, 19, 35000.00, 1, '2022-03-04 11:58:48', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (21, 25, 1, 19, 1100.00, 1, '2022-03-04 11:58:48', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (22, 26, 1, 19, 35000.00, 1, '2022-03-04 11:58:48', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (23, 27, 1, 19, 35000.00, 1, '2022-03-04 11:58:48', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (24, 28, 1, 19, 1250.00, 1, '2022-03-04 11:58:48', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (25, 29, 2, 19, 8.00, 1, '2022-03-04 12:05:29', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (26, 30, 2, 19, 2.50, 1, '2022-03-04 12:05:29', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (27, 31, 2, 19, 6.85, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (28, 32, 2, 19, 1.75, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (29, 33, 2, 15, 100.00, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (30, 34, 2, 19, 16.50, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (31, 35, 2, 19, 8.50, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (32, 36, 2, 15, 29.50, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (33, 37, 2, 15, 29.50, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (34, 38, 2, 15, 29.50, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (35, 39, 2, 19, 720.00, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (36, 40, 2, 19, 75.00, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (37, 41, 2, 19, 2.00, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (38, 42, 2, 38, 495.00, 1, '2022-03-04 12:05:30', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (39, 43, 2, 19, 400.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (40, 44, 2, 19, 25.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (41, 45, 2, 15, 17.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (42, 46, 2, 15, 43.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (43, 47, 2, 19, 450.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (44, 48, 2, 19, 2.50, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (45, 49, 2, 19, 6.85, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (46, 50, 2, 15, 100.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (47, 52, 2, 19, 6.85, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (48, 55, 2, 15, 48.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (49, 56, 2, 15, 48.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (50, 57, 2, 15, 48.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (51, 58, 2, 15, 126.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (52, 59, 2, 15, 126.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (53, 60, 2, 15, 350.00, 1, '2022-03-04 12:05:31', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (54, 61, 2, 15, 350.00, 1, '2022-03-04 12:05:32', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (55, 62, 2, 15, 375.00, 1, '2022-03-04 12:05:32', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (56, 63, 2, 15, 375.00, 1, '2022-03-04 12:05:32', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (57, 64, 3, 39, 180.00, 1, '2022-03-04 12:05:32', NULL, NULL, 1);
INSERT INTO `application_customer_system_parts` VALUES (58, 65, 3, 30, 45.00, 1, '2022-03-04 12:05:32', NULL, NULL, 1);

-- ----------------------------
-- Table structure for customer_system_group_template
-- ----------------------------
DROP TABLE IF EXISTS `customer_system_group_template`;
CREATE TABLE `customer_system_group_template`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `systypeid` int(11) NULL DEFAULT NULL,
  `sptypeid` int(11) NULL DEFAULT NULL COMMENT 'solar_panel_types.sysid',
  `nop` int(11) NULL DEFAULT NULL COMMENT 'Number of Panels',
  `nos` int(11) NULL DEFAULT NULL COMMENT 'Number of Strings',
  `panelsperstring` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `invertersize` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of customer_system_group_template
-- ----------------------------
INSERT INTO `customer_system_group_template` VALUES (1, '900Wp Grid-Tied (Default)', 8, 3, 2, 1, '2', '1 kW', 1, '2022-03-04 13:41:07', NULL, '2022-03-04 13:41:24', 1);
INSERT INTO `customer_system_group_template` VALUES (2, '1.35kWp Grid-Tied (Default)', 9, 3, 3, 1, '3', '1', 1, '2022-03-04 14:08:12', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (3, '2.7kWp Grid-Tied (Default)', 10, 3, 6, 1, '6', '3', 1, '2022-03-04 14:08:12', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (4, '3.15kWp Grid-Tied (Default)', 11, 3, 7, 1, '7', '3', 1, '2022-03-04 14:08:12', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (5, '3.6kWp Grid-Tied (Default)', 12, 3, 8, 1, '8', '3', 1, '2022-03-04 14:08:13', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (6, '5.4kWp Grid-Tied (Default)', 13, 3, 12, 1, '12', '5', 1, '2022-03-04 14:08:13', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (7, '8.1kWp Grid-Tied (Default)', 14, 3, 18, 2, '7,11', '3,5', 1, '2022-03-04 14:08:13', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (8, '10.35kWp Grid-Tied (Default)', 15, 3, 23, 2, '11,12', '5', 1, '2022-03-04 14:08:13', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (9, '13.5kWp Grid-Tied (Default)', 16, 3, 30, 3, '7,11,12', '3,5', 1, '2022-03-04 14:08:13', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (10, '16.2kWp Grid-Tied (Default)', 17, 3, 36, 3, '12', '5', 1, '2022-03-04 14:08:13', NULL, NULL, 1);
INSERT INTO `customer_system_group_template` VALUES (11, '21.15kWp Grid-Tied (Default)', 18, 3, 47, 4, '3 x 12 + 11', '5', 1, '2022-03-04 14:08:13', NULL, NULL, 1);

-- ----------------------------
-- Table structure for customer_system_parts
-- ----------------------------
DROP TABLE IF EXISTS `customer_system_parts`;
CREATE TABLE `customer_system_parts`  (
  `sysid` int(11) NOT NULL,
  `appid` int(11) NULL DEFAULT NULL,
  `itemid` int(11) NULL DEFAULT NULL,
  `specid` int(11) NULL DEFAULT NULL,
  `unitid` int(11) NULL DEFAULT NULL,
  `qty` int(11) NULL DEFAULT NULL,
  `typesid` int(11) NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT 1,
  `datecreated` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for customer_system_parts_template
-- ----------------------------
DROP TABLE IF EXISTS `customer_system_parts_template`;
CREATE TABLE `customer_system_parts_template`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NULL DEFAULT NULL,
  `itemid` int(11) NULL DEFAULT NULL COMMENT 'items_main_description.sysid',
  `unitid` int(11) NULL DEFAULT NULL,
  `qty` int(11) NULL DEFAULT NULL,
  `type` int(11) NULL DEFAULT NULL,
  `unitprice` decimal(10, 2) NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT 1,
  `datecreated` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 395 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of customer_system_parts_template
-- ----------------------------
INSERT INTO `customer_system_parts_template` VALUES (1, 1, 1, 1, 2, 1, 8200.00, 1, '2022-03-04 13:13:58', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (2, 1, 2, 1, 2, 1, 1000.00, 1, '2022-03-04 13:15:03', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (3, 1, 3, 1, 2, 1, 60.00, 1, '2022-03-04 13:15:03', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (4, 1, 4, 1, 2, 1, 45.00, 1, '2022-03-04 13:15:03', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (5, 1, 5, 1, 4, 1, 45.00, 1, '2022-03-04 13:15:03', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (6, 1, 6, 1, 40, 1, 60.00, 1, '2022-03-04 13:15:03', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (7, 1, 7, 1, 1, 1, 810.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (8, 1, 8, 1, 1, 1, 995.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (9, 1, 9, 1, 2, 1, 60.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (10, 1, 10, 1, 1, 1, 16200.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (11, 1, 11, 1, 1, 1, 600.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (12, 1, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (13, 1, 13, 1, 2, 1, 310.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (14, 1, 14, 1, 2, 1, 90.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:09:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (15, 2, 1, 1, 3, 1, 8200.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:10:24', 1);
INSERT INTO `customer_system_parts_template` VALUES (16, 2, 2, 1, 2, 1, 1000.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:10:24', 1);
INSERT INTO `customer_system_parts_template` VALUES (17, 2, 3, 1, 6, 1, 60.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:10:24', 1);
INSERT INTO `customer_system_parts_template` VALUES (18, 2, 4, 1, 4, 1, 45.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (19, 2, 5, 1, 4, 1, 45.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (20, 2, 6, 1, 35, 1, 60.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (21, 2, 7, 1, 1, 1, 810.00, 1, '2022-03-04 13:15:04', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (22, 2, 8, 1, 1, 1, 995.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (23, 2, 9, 1, 2, 1, 60.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (24, 2, 10, 1, 1, 1, 16200.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (25, 2, 11, 1, 1, 1, 600.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (26, 2, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (27, 2, 13, 1, 2, 1, 310.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (28, 2, 14, 1, 2, 1, 90.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (29, 3, 1, 1, 6, 1, 8200.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:45', 1);
INSERT INTO `customer_system_parts_template` VALUES (30, 3, 2, 1, 4, 1, 1000.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:45', 1);
INSERT INTO `customer_system_parts_template` VALUES (31, 3, 3, 1, 12, 1, 60.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:45', 1);
INSERT INTO `customer_system_parts_template` VALUES (32, 3, 4, 1, 10, 1, 45.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:45', 1);
INSERT INTO `customer_system_parts_template` VALUES (33, 3, 5, 1, 4, 1, 45.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:45', 1);
INSERT INTO `customer_system_parts_template` VALUES (34, 3, 6, 1, 50, 1, 60.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:45', 1);
INSERT INTO `customer_system_parts_template` VALUES (35, 3, 7, 1, 1, 1, 810.00, 1, '2022-03-04 13:15:05', NULL, '2022-03-04 14:10:45', 1);
INSERT INTO `customer_system_parts_template` VALUES (36, 3, 8, 1, 1, 1, 995.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (37, 3, 9, 1, 4, 1, 60.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (38, 3, 15, 1, 1, 1, 25620.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (39, 3, 16, 1, 1, 1, 21000.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (40, 3, 11, 1, 1, 1, 600.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (41, 3, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (42, 3, 13, 1, 2, 1, 310.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (43, 3, 14, 1, 4, 1, 90.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (44, 4, 1, 1, 7, 1, 8200.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:11:11', 1);
INSERT INTO `customer_system_parts_template` VALUES (45, 4, 2, 1, 4, 1, 1000.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:11:11', 1);
INSERT INTO `customer_system_parts_template` VALUES (46, 4, 3, 1, 14, 1, 60.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:11:11', 1);
INSERT INTO `customer_system_parts_template` VALUES (47, 4, 4, 1, 12, 1, 45.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (48, 4, 5, 1, 4, 1, 45.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (49, 4, 6, 1, 50, 1, 60.00, 1, '2022-03-04 13:15:06', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (50, 4, 7, 1, 1, 1, 810.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (51, 4, 8, 1, 1, 1, 995.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (52, 4, 9, 1, 4, 1, 60.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (53, 4, 15, 1, 1, 1, 25620.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (54, 4, 16, 1, 1, 1, 21000.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (55, 4, 11, 1, 1, 1, 600.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (56, 4, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (57, 4, 13, 1, 2, 1, 310.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (58, 4, 14, 1, 4, 1, 90.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (59, 5, 1, 1, 8, 1, 8200.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (60, 5, 2, 1, 5, 1, 1000.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (61, 5, 3, 1, 16, 1, 60.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (62, 5, 4, 1, 14, 1, 45.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (63, 5, 5, 1, 4, 1, 45.00, 1, '2022-03-04 13:15:07', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (64, 5, 6, 1, 50, 1, 60.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (65, 5, 7, 1, 1, 1, 810.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (66, 5, 8, 1, 1, 1, 995.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (67, 5, 9, 1, 4, 1, 60.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (68, 5, 15, 1, 1, 1, 25620.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (69, 5, 16, 1, 1, 1, 21000.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (70, 5, 11, 1, 1, 1, 600.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (71, 5, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (72, 5, 13, 1, 2, 1, 310.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:31', 1);
INSERT INTO `customer_system_parts_template` VALUES (73, 5, 14, 1, 4, 1, 90.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (74, 6, 1, 1, 12, 1, 8200.00, 1, '2022-03-04 13:15:08', NULL, '2022-03-04 14:12:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (75, 6, 2, 1, 7, 1, 1000.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (76, 6, 3, 1, 24, 1, 60.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (77, 6, 4, 1, 22, 1, 45.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (78, 6, 5, 1, 4, 1, 45.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (79, 6, 6, 1, 60, 1, 60.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (80, 6, 7, 1, 1, 1, 810.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (81, 6, 8, 1, 1, 1, 995.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (82, 6, 9, 1, 5, 1, 60.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (83, 6, 20, 1, 1, 1, 51361.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (84, 6, 21, 1, 1, 1, 41420.45, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (85, 6, 22, 1, 1, 1, 750.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (86, 6, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (87, 6, 13, 1, 2, 1, 310.00, 1, '2022-03-04 13:15:09', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (88, 6, 14, 1, 6, 1, 90.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (89, 7, 1, 1, 18, 1, 8200.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (90, 7, 2, 1, 10, 1, 1000.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (91, 7, 3, 1, 36, 1, 60.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (92, 7, 4, 1, 34, 1, 45.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (93, 7, 5, 1, 8, 1, 45.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (94, 7, 6, 1, 60, 1, 60.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (95, 7, 7, 1, 2, 1, 810.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (96, 7, 8, 1, 2, 1, 995.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (97, 7, 9, 1, 9, 1, 60.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (98, 7, 16, 1, 1, 1, 21000.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (99, 7, 21, 1, 1, 1, 41420.45, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (100, 7, 15, 1, 1, 1, 25620.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:05', 1);
INSERT INTO `customer_system_parts_template` VALUES (101, 7, 20, 1, 1, 1, 51361.00, 1, '2022-03-04 13:15:10', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (102, 7, 11, 1, 1, 1, 600.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (103, 7, 22, 1, 1, 1, 750.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (104, 7, 12, 1, 2, 1, 589.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (105, 7, 13, 1, 4, 1, 310.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (106, 7, 14, 1, 8, 1, 90.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (107, 8, 1, 1, 23, 1, 8200.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (108, 8, 2, 1, 13, 1, 1000.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (109, 8, 3, 1, 46, 1, 60.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (110, 8, 4, 1, 44, 1, 45.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (111, 8, 5, 1, 8, 1, 45.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (112, 8, 6, 1, 120, 1, 60.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (113, 8, 7, 1, 2, 1, 810.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (114, 8, 8, 1, 2, 1, 995.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (115, 8, 9, 1, 10, 1, 60.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (116, 8, 20, 1, 2, 1, 51361.00, 1, '2022-03-04 13:15:11', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (117, 8, 21, 1, 2, 1, 41420.45, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (118, 8, 22, 1, 2, 1, 750.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (119, 8, 12, 1, 2, 1, 589.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (120, 8, 13, 1, 4, 1, 310.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (121, 8, 14, 1, 12, 1, 90.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:22', 1);
INSERT INTO `customer_system_parts_template` VALUES (122, 9, 1, 1, 30, 1, 8200.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (123, 9, 2, 1, 16, 1, 1000.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (124, 9, 3, 1, 60, 1, 60.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (125, 9, 4, 1, 58, 1, 45.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (126, 9, 5, 1, 12, 1, 45.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (127, 9, 6, 1, 170, 1, 60.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (128, 9, 7, 1, 3, 1, 810.00, 1, '2022-03-04 13:15:12', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (129, 9, 8, 1, 3, 1, 995.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (130, 9, 9, 1, 15, 1, 60.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (131, 9, 20, 1, 2, 1, 51361.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (132, 9, 15, 1, 1, 1, 25620.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (133, 9, 21, 1, 2, 1, 41420.45, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (134, 9, 16, 1, 1, 1, 21000.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:37', 1);
INSERT INTO `customer_system_parts_template` VALUES (135, 9, 24, 1, 1, 1, 35000.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (136, 9, 25, 1, 1, 1, 1100.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (137, 9, 22, 1, 2, 1, 750.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (138, 9, 11, 1, 1, 1, 600.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (139, 9, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (140, 9, 13, 1, 4, 1, 310.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (141, 9, 14, 1, 14, 1, 90.00, 1, '2022-03-04 13:15:13', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (142, 10, 1, 1, 36, 1, 8200.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:41', 1);
INSERT INTO `customer_system_parts_template` VALUES (143, 10, 2, 1, 19, 1, 1000.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:41', 1);
INSERT INTO `customer_system_parts_template` VALUES (144, 10, 3, 1, 72, 1, 60.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:41', 1);
INSERT INTO `customer_system_parts_template` VALUES (145, 10, 4, 1, 70, 1, 45.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:41', 1);
INSERT INTO `customer_system_parts_template` VALUES (146, 10, 5, 1, 12, 1, 45.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:41', 1);
INSERT INTO `customer_system_parts_template` VALUES (147, 10, 6, 1, 180, 1, 60.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (148, 10, 7, 1, 3, 1, 810.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (149, 10, 8, 1, 3, 1, 995.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (150, 10, 9, 1, 15, 1, 60.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (151, 10, 20, 1, 3, 1, 51361.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (152, 10, 21, 1, 3, 1, 41420.45, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (153, 10, 26, 1, 1, 1, 35000.00, 1, '2022-03-04 13:15:14', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (154, 10, 25, 1, 1, 1, 1100.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (155, 10, 22, 1, 3, 1, 750.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (156, 10, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (157, 10, 13, 1, 4, 1, 310.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:42', 1);
INSERT INTO `customer_system_parts_template` VALUES (158, 10, 14, 1, 18, 1, 90.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (159, 11, 1, 1, 47, 1, 8200.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (160, 11, 2, 1, 25, 1, 1000.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (161, 11, 3, 1, 94, 1, 60.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (162, 11, 4, 1, 92, 1, 45.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (163, 11, 5, 1, 16, 1, 45.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (164, 11, 6, 1, 240, 1, 60.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (165, 11, 7, 1, 4, 1, 810.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (166, 11, 8, 1, 4, 1, 995.00, 1, '2022-03-04 13:15:15', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (167, 11, 9, 1, 20, 1, 60.00, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:58', 1);
INSERT INTO `customer_system_parts_template` VALUES (168, 11, 20, 1, 4, 1, 51361.00, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (169, 11, 21, 1, 4, 1, 41420.45, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (170, 11, 27, 1, 1, 1, 35000.00, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (171, 11, 28, 1, 1, 1, 1250.00, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (172, 11, 22, 1, 4, 1, 750.00, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (173, 11, 12, 1, 1, 1, 589.00, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (174, 11, 13, 1, 3, 1, 310.00, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (175, 11, 14, 1, 24, 1, 90.00, 1, '2022-03-04 13:15:16', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (176, 1, 29, 2, 11, 2, 8.00, 1, '2022-03-04 13:17:59', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (177, 1, 30, 2, 28, 3, 2.50, 1, '2022-03-04 13:17:59', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (178, 1, 31, 2, 12, 4, 6.85, 1, '2022-03-04 13:17:59', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (179, 1, 32, 2, 23, 5, 1.75, 1, '2022-03-04 13:17:59', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (180, 1, 33, 2, 24, 6, 100.00, 1, '2022-03-04 13:17:59', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (181, 1, 34, 2, 4, 7, 16.50, 1, '2022-03-04 13:17:59', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (182, 1, 35, 2, 31, 8, 8.50, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (183, 1, 36, 2, 16, 9, 29.50, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (184, 1, 37, 2, 16, 10, 29.50, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (185, 1, 38, 2, 31, 11, 29.50, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (186, 1, 39, 2, 1, 12, 720.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (187, 1, 40, 2, 1, 13, 75.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (188, 1, 41, 2, 18, 14, 2.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (189, 1, 42, 2, 1, 15, 495.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:09:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (190, 1, 43, 2, 1, 16, 400.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:10:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (191, 1, 44, 2, 2, 17, 25.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:10:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (192, 1, 45, 2, 4, 18, 17.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:10:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (193, 1, 46, 2, 4, 19, 43.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:10:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (194, 1, 47, 2, 1, 20, 450.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:10:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (195, 2, 29, 2, 12, 21, 8.00, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (196, 2, 48, 2, 30, 22, 2.50, 1, '2022-03-04 13:18:00', NULL, '2022-03-04 14:10:25', 1);
INSERT INTO `customer_system_parts_template` VALUES (197, 2, 49, 2, 12, 23, 6.85, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (198, 2, 32, 2, 30, 24, 1.75, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (199, 2, 50, 2, 30, 25, 100.00, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (200, 2, 34, 2, 2, 26, 16.50, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (201, 2, 35, 2, 30, 27, 8.50, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (202, 2, 36, 2, 25, 28, 29.50, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (203, 2, 37, 2, 25, 29, 29.50, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (204, 2, 38, 2, 30, 30, 29.50, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (205, 2, 39, 2, 1, 31, 720.00, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (206, 2, 40, 2, 1, 32, 75.00, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (207, 2, 41, 2, 5, 33, 2.00, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (208, 2, 42, 2, 1, 34, 495.00, 1, '2022-03-04 13:18:01', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (209, 2, 43, 2, 1, 35, 400.00, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (210, 2, 44, 2, 2, 36, 25.00, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:26', 1);
INSERT INTO `customer_system_parts_template` VALUES (211, 2, 45, 2, 5, 37, 17.00, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:27', 1);
INSERT INTO `customer_system_parts_template` VALUES (212, 2, 46, 2, 5, 38, 43.00, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:27', 1);
INSERT INTO `customer_system_parts_template` VALUES (213, 2, 47, 2, 1, 39, 450.00, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:27', 1);
INSERT INTO `customer_system_parts_template` VALUES (214, 3, 29, 2, 20, 40, 8.00, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (215, 3, 30, 2, 34, 41, 2.50, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (216, 3, 31, 2, 18, 42, 6.85, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (217, 3, 32, 2, 28, 43, 1.75, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (218, 3, 33, 2, 30, 44, 100.00, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:46', 1);
INSERT INTO `customer_system_parts_template` VALUES (219, 3, 34, 2, 4, 45, 16.50, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (220, 3, 35, 2, 30, 46, 8.50, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (221, 3, 36, 2, 15, 47, 29.50, 1, '2022-03-04 13:18:02', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (222, 3, 37, 2, 15, 48, 29.50, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (223, 3, 38, 2, 30, 49, 29.50, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (224, 3, 39, 2, 1, 50, 720.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (225, 3, 40, 2, 1, 51, 75.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (226, 3, 41, 2, 10, 52, 2.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (227, 3, 42, 2, 1, 53, 495.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (228, 3, 43, 2, 1, 54, 400.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (229, 3, 44, 2, 2, 55, 25.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (230, 3, 45, 2, 5, 56, 17.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (231, 3, 46, 2, 5, 57, 43.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:10:47', 1);
INSERT INTO `customer_system_parts_template` VALUES (232, 4, 29, 2, 22, 58, 8.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (233, 4, 30, 2, 34, 59, 2.50, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:11:12', 1);
INSERT INTO `customer_system_parts_template` VALUES (234, 4, 31, 2, 20, 60, 6.85, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (235, 4, 32, 2, 30, 61, 1.75, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (236, 4, 33, 2, 30, 62, 100.00, 1, '2022-03-04 13:18:03', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (237, 4, 34, 2, 4, 63, 16.50, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (238, 4, 35, 2, 30, 64, 8.50, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (239, 4, 36, 2, 15, 65, 29.50, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (240, 4, 37, 2, 15, 66, 29.50, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (241, 4, 38, 2, 30, 67, 29.50, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (242, 4, 39, 2, 1, 68, 720.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (243, 4, 40, 2, 1, 69, 75.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (244, 4, 41, 2, 10, 70, 2.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (245, 4, 42, 2, 1, 71, 495.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (246, 4, 43, 2, 1, 72, 400.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (247, 4, 44, 2, 2, 73, 25.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:13', 1);
INSERT INTO `customer_system_parts_template` VALUES (248, 4, 45, 2, 5, 74, 17.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:14', 1);
INSERT INTO `customer_system_parts_template` VALUES (249, 4, 46, 2, 5, 75, 43.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:14', 1);
INSERT INTO `customer_system_parts_template` VALUES (250, 5, 29, 2, 22, 76, 8.00, 1, '2022-03-04 13:18:04', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (251, 5, 48, 2, 34, 77, 2.50, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (252, 5, 52, 2, 20, 78, 6.85, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (253, 5, 32, 2, 30, 79, 1.75, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (254, 5, 33, 2, 30, 80, 100.00, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (255, 5, 34, 2, 4, 81, 16.50, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (256, 5, 35, 2, 30, 82, 8.50, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (257, 5, 36, 2, 25, 83, 29.50, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (258, 5, 37, 2, 25, 84, 29.50, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (259, 5, 38, 2, 30, 85, 29.50, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (260, 5, 39, 2, 1, 86, 720.00, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (261, 5, 40, 2, 1, 87, 75.00, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:32', 1);
INSERT INTO `customer_system_parts_template` VALUES (262, 5, 41, 2, 10, 88, 2.00, 1, '2022-03-04 13:20:38', NULL, '2022-03-04 14:11:33', 1);
INSERT INTO `customer_system_parts_template` VALUES (263, 5, 42, 2, 1, 89, 495.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:11:33', 1);
INSERT INTO `customer_system_parts_template` VALUES (264, 5, 43, 2, 1, 90, 400.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:11:33', 1);
INSERT INTO `customer_system_parts_template` VALUES (265, 5, 44, 2, 2, 91, 25.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:11:33', 1);
INSERT INTO `customer_system_parts_template` VALUES (266, 5, 45, 2, 5, 92, 17.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:11:33', 1);
INSERT INTO `customer_system_parts_template` VALUES (267, 5, 46, 2, 5, 93, 43.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:11:33', 1);
INSERT INTO `customer_system_parts_template` VALUES (268, 5, 47, 2, 1, 94, 450.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:11:33', 1);
INSERT INTO `customer_system_parts_template` VALUES (269, 6, 29, 2, 28, 95, 8.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (270, 6, 48, 2, 55, 96, 2.50, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:12:48', 1);
INSERT INTO `customer_system_parts_template` VALUES (271, 6, 49, 2, 30, 97, 6.85, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (272, 6, 32, 2, 35, 98, 1.75, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (273, 6, 50, 2, 35, 99, 100.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (274, 6, 34, 2, 6, 100, 16.50, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (275, 6, 35, 2, 30, 101, 8.50, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (276, 6, 55, 2, 15, 102, 48.00, 1, '2022-03-04 13:20:39', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (277, 6, 56, 2, 15, 103, 48.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (278, 6, 38, 2, 40, 104, 29.50, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (279, 6, 39, 2, 1, 105, 720.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (280, 6, 40, 2, 1, 106, 75.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (281, 6, 41, 2, 10, 107, 2.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (282, 6, 42, 2, 1, 108, 450.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (283, 6, 43, 2, 1, 109, 400.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (284, 6, 44, 2, 2, 110, 25.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:49', 1);
INSERT INTO `customer_system_parts_template` VALUES (285, 6, 45, 2, 10, 111, 17.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:50', 1);
INSERT INTO `customer_system_parts_template` VALUES (286, 6, 46, 2, 10, 112, 43.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:50', 1);
INSERT INTO `customer_system_parts_template` VALUES (287, 6, 47, 2, 1, 113, 450.00, 1, '2022-03-04 13:20:40', NULL, '2022-03-04 14:12:50', 1);
INSERT INTO `customer_system_parts_template` VALUES (288, 7, 29, 2, 55, 114, 8.00, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (289, 7, 30, 2, 80, 115, 2.50, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (290, 7, 52, 2, 50, 116, 6.85, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (291, 7, 32, 2, 50, 117, 1.75, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (292, 7, 50, 2, 60, 118, 100.00, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (293, 7, 34, 2, 8, 119, 16.50, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (294, 7, 35, 2, 50, 120, 8.50, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:06', 1);
INSERT INTO `customer_system_parts_template` VALUES (295, 7, 36, 2, 15, 121, 29.50, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (296, 7, 37, 2, 15, 122, 29.50, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (297, 7, 55, 2, 15, 123, 48.00, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (298, 7, 56, 2, 15, 124, 48.00, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (299, 7, 38, 2, 70, 125, 29.50, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (300, 7, 39, 2, 1, 126, 720.00, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (301, 7, 40, 2, 1, 127, 75.00, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (302, 7, 41, 2, 10, 128, 2.00, 1, '2022-03-04 13:20:41', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (303, 7, 42, 2, 2, 129, 495.00, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (304, 7, 43, 2, 1, 130, 400.00, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (305, 7, 44, 2, 2, 131, 25.00, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (306, 7, 45, 2, 20, 132, 17.00, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (307, 7, 46, 2, 20, 133, 43.00, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (308, 7, 47, 2, 1, 134, 450.00, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:07', 1);
INSERT INTO `customer_system_parts_template` VALUES (309, 8, 29, 2, 56, 135, 8.00, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (310, 8, 30, 2, 110, 136, 2.50, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (311, 8, 31, 2, 60, 137, 6.85, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (312, 8, 32, 2, 70, 138, 1.75, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (313, 8, 33, 2, 60, 139, 100.00, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (314, 8, 34, 2, 12, 140, 16.50, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (315, 8, 35, 2, 60, 141, 8.50, 1, '2022-03-04 13:20:42', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (316, 8, 55, 2, 50, 142, 48.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (317, 8, 56, 2, 50, 143, 48.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (318, 8, 38, 2, 80, 144, 29.50, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (319, 8, 39, 2, 1, 145, 720.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (320, 8, 40, 2, 2, 146, 75.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (321, 8, 41, 2, 20, 147, 2.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (322, 8, 42, 2, 2, 148, 450.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (323, 8, 43, 2, 1, 149, 400.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (324, 8, 44, 2, 4, 150, 25.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (325, 8, 45, 2, 20, 151, 17.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (326, 8, 46, 2, 20, 152, 43.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (327, 8, 47, 2, 2, 153, 450.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:23', 1);
INSERT INTO `customer_system_parts_template` VALUES (328, 9, 29, 2, 78, 154, 8.00, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (329, 9, 30, 2, 144, 155, 2.50, 1, '2022-03-04 13:20:43', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (330, 9, 31, 2, 80, 156, 6.85, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (331, 9, 32, 2, 100, 157, 1.75, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (332, 9, 33, 2, 110, 158, 100.00, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (333, 9, 34, 2, 16, 159, 16.50, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (334, 9, 55, 2, 50, 160, 48.00, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (335, 9, 56, 2, 50, 161, 48.00, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (336, 9, 57, 2, 25, 162, 48.00, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (337, 9, 58, 2, 15, 163, 126.00, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (338, 9, 59, 2, 15, 164, 126.00, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (339, 9, 36, 2, 5, 165, 29.50, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (340, 9, 37, 2, 5, 166, 29.50, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (341, 9, 38, 2, 105, 167, 29.50, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (342, 9, 39, 2, 1, 168, 720.00, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (343, 9, 40, 2, 2, 169, 75.00, 1, '2022-03-04 13:20:44', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (344, 9, 41, 2, 30, 170, 2.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (345, 9, 42, 2, 3, 171, 450.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (346, 9, 43, 2, 1, 172, 400.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (347, 9, 44, 2, 2, 173, 25.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (348, 9, 45, 2, 45, 174, 17.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (349, 9, 46, 2, 45, 175, 43.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:13:38', 1);
INSERT INTO `customer_system_parts_template` VALUES (350, 9, 47, 2, 1, 176, 450.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:13:39', 1);
INSERT INTO `customer_system_parts_template` VALUES (351, 10, 29, 2, 84, 177, 8.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (352, 10, 30, 2, 165, 178, 2.50, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (353, 10, 31, 2, 70, 179, 6.85, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (354, 10, 32, 2, 105, 180, 1.75, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (355, 10, 33, 2, 105, 181, 100.00, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (356, 10, 34, 2, 18, 182, 16.50, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (357, 10, 35, 2, 90, 183, 8.50, 1, '2022-03-04 13:20:45', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (358, 10, 55, 2, 15, 184, 48.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (359, 10, 56, 2, 15, 185, 48.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (360, 10, 60, 2, 15, 186, 350.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (361, 10, 61, 2, 15, 187, 350.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (362, 10, 38, 2, 70, 188, 29.50, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:43', 1);
INSERT INTO `customer_system_parts_template` VALUES (363, 10, 57, 2, 15, 189, 48.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (364, 10, 39, 2, 1, 190, 720.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (365, 10, 40, 2, 1, 191, 75.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (366, 10, 41, 2, 10, 192, 2.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (367, 10, 42, 2, 3, 193, 450.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (368, 10, 43, 2, 1, 194, 400.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (369, 10, 44, 2, 2, 195, 25.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (370, 10, 45, 2, 45, 196, 17.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (371, 10, 46, 2, 45, 197, 43.00, 1, '2022-03-04 13:20:46', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (372, 10, 47, 2, 1, 198, 450.00, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:16:44', 1);
INSERT INTO `customer_system_parts_template` VALUES (373, 11, 29, 2, 112, 199, 8.00, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:16:59', 1);
INSERT INTO `customer_system_parts_template` VALUES (374, 11, 30, 2, 220, 200, 2.50, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (375, 11, 31, 2, 90, 201, 6.85, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (376, 11, 32, 2, 140, 202, 1.75, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (377, 11, 33, 2, 140, 203, 100.00, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (378, 11, 34, 2, 24, 204, 16.50, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (379, 11, 35, 2, 120, 205, 8.50, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (380, 11, 55, 2, 20, 206, 48.00, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (381, 11, 56, 2, 20, 207, 48.00, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (382, 11, 62, 2, 15, 208, 375.00, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (383, 11, 63, 2, 15, 209, 375.00, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (384, 11, 38, 2, 95, 210, 29.50, 1, '2022-03-04 13:20:47', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (385, 11, 57, 2, 15, 211, 48.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (386, 11, 39, 2, 1, 212, 720.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (387, 11, 40, 2, 1, 213, 75.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:00', 1);
INSERT INTO `customer_system_parts_template` VALUES (388, 11, 41, 2, 10, 214, 2.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:01', 1);
INSERT INTO `customer_system_parts_template` VALUES (389, 11, 42, 2, 4, 215, 450.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:01', 1);
INSERT INTO `customer_system_parts_template` VALUES (390, 11, 43, 2, 1, 216, 400.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:01', 1);
INSERT INTO `customer_system_parts_template` VALUES (391, 11, 44, 2, 2, 217, 25.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:01', 1);
INSERT INTO `customer_system_parts_template` VALUES (392, 11, 45, 2, 60, 218, 17.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:01', 1);
INSERT INTO `customer_system_parts_template` VALUES (393, 11, 46, 2, 60, 219, 43.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:01', 1);
INSERT INTO `customer_system_parts_template` VALUES (394, 11, 47, 2, 1, 220, 450.00, 1, '2022-03-04 13:20:48', NULL, '2022-03-04 14:17:01', 1);

-- ----------------------------
-- Table structure for customer_system_size
-- ----------------------------
DROP TABLE IF EXISTS `customer_system_size`;
CREATE TABLE `customer_system_size`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `amtmin` decimal(20, 2) NULL DEFAULT NULL,
  `amtmax` decimal(20, 2) NULL DEFAULT NULL,
  `amtequal` decimal(20, 2) NULL DEFAULT NULL,
  `descs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of customer_system_size
-- ----------------------------
INSERT INTO `customer_system_size` VALUES (1, 0.00, 2.00, NULL, '980wp Grid-Tied', 0);
INSERT INTO `customer_system_size` VALUES (2, 0.00, 0.00, 3.00, '1.47kwp Grid-Tied', 0);
INSERT INTO `customer_system_size` VALUES (3, 4.00, 9.00, NULL, '3.43kwp Grid-Tied', 0);
INSERT INTO `customer_system_size` VALUES (4, 10.00, 17.00, NULL, '5.39kwp Grid-Tied', 0);
INSERT INTO `customer_system_size` VALUES (5, 18.00, 27.00, NULL, '10.79kWp Grid-Tied', 0);
INSERT INTO `customer_system_size` VALUES (6, 28.00, 37.00, NULL, '15.68kWp Grid-Tied', 0);
INSERT INTO `customer_system_size` VALUES (7, 38.00, 48.00, NULL, '21.56kWp Grid-Tied', 0);
INSERT INTO `customer_system_size` VALUES (8, 0.00, 2.00, NULL, '900Wp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (9, 0.00, 0.00, 3.00, '1.35kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (10, 4.00, 6.00, NULL, '2.7kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (11, 0.00, 0.00, 7.00, '3.15kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (12, 0.00, 0.00, 8.00, '3.6kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (13, 9.00, 12.00, NULL, '5.4kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (14, 13.00, 18.00, NULL, '8.1kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (15, 19.00, 23.00, NULL, '10.35kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (16, 24.00, 30.00, NULL, '13.5kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (17, 31.00, 36.00, 0.00, '16.2kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (18, 37.00, 47.00, 0.00, '21.15kWp Grid-Tied', 1);

-- ----------------------------
-- Table structure for items_main
-- ----------------------------
DROP TABLE IF EXISTS `items_main`;
CREATE TABLE `items_main`  (
  `sysid` int(11) NOT NULL,
  `codes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for items_main_category
-- ----------------------------
DROP TABLE IF EXISTS `items_main_category`;
CREATE TABLE `items_main_category`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `serials` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `codes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `names` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `desc` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `types` int(11) NULL DEFAULT NULL,
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of items_main_category
-- ----------------------------
INSERT INTO `items_main_category` VALUES (1, NULL, 'Bolts', 'Bolts', 'Bolt', 1, 1);
INSERT INTO `items_main_category` VALUES (2, NULL, 'PC', 'Personal Computer', 'Personal Computer', 1, 1);
INSERT INTO `items_main_category` VALUES (3, NULL, 'MTR', 'Meter', 'Electric Meter', 1, 1);
INSERT INTO `items_main_category` VALUES (4, NULL, 'SPS', 'Solar Panels System', 'Solar Panels System', NULL, 1);
INSERT INTO `items_main_category` VALUES (5, NULL, 'Wires', 'Wires', 'Wires', NULL, 1);

-- ----------------------------
-- Table structure for items_main_components
-- ----------------------------
DROP TABLE IF EXISTS `items_main_components`;
CREATE TABLE `items_main_components`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `catid` int(11) NULL DEFAULT NULL,
  `codes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `names` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `qrcode` blob NULL,
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for items_main_description
-- ----------------------------
DROP TABLE IF EXISTS `items_main_description`;
CREATE TABLE `items_main_description`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NULL DEFAULT NULL,
  `typeid` int(11) NULL DEFAULT NULL,
  `specid` int(11) NULL DEFAULT NULL,
  `fulldescription` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `unitid` int(11) NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of items_main_description
-- ----------------------------
INSERT INTO `items_main_description` VALUES (1, NULL, NULL, NULL, 'Solar Panel, 450Wp, &ge;40Vmpp, ?50Voc', 19, 1, '2022-03-04 11:17:28', NULL, '2022-03-04 11:33:07', 1);
INSERT INTO `items_main_description` VALUES (2, NULL, NULL, NULL, 'Railing, Aluminum, 4.2m', 19, 1, '2022-03-04 11:17:28', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (3, NULL, NULL, NULL, 'L-feet w/ tekscrew', 21, 1, '2022-03-04 11:17:28', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (4, NULL, NULL, NULL, 'Mid Clamp', 19, 1, '2022-03-04 11:17:28', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (5, NULL, NULL, NULL, 'End Clamp', 19, 1, '2022-03-04 11:17:28', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (6, NULL, NULL, NULL, 'DC Solar Cable, 6mm2', 15, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (7, NULL, NULL, NULL, 'DC Breaker, 16AT, 600V', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (8, NULL, NULL, NULL, 'DC SPD, 600V', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (9, NULL, NULL, NULL, 'MC4 Connector', 37, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (10, NULL, NULL, NULL, 'PV Inverter 1 kWp', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (11, NULL, NULL, NULL, 'AC Breaker, 20 AT', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (12, NULL, NULL, NULL, 'AC SPD - 1 Phase', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (13, NULL, NULL, NULL, '8-Way Enclosure Box', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (14, NULL, NULL, NULL, 'Solar Rail Splice Kit', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (15, NULL, NULL, NULL, 'PV Inverter 3 kWp 10 yrs warranty', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (16, NULL, NULL, NULL, 'PV Inverter 3 kWp 5 yrs warranty', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (17, NULL, NULL, NULL, 'PV Inverter 3 kWp', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (18, NULL, NULL, NULL, 'PV Inverter 3 kWp 10 yrs', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (19, NULL, NULL, NULL, 'PV Inverter 3 kWp 5 yrs', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (20, NULL, NULL, NULL, 'PV Inverter 5 kWp 10 yrs warranty', 19, 1, '2022-03-04 11:17:29', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (21, NULL, NULL, NULL, 'PV Inverter 5 kWp 5yrs warranty', 19, 1, '2022-03-04 11:17:30', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (22, NULL, NULL, NULL, 'AC Breaker, 30 AT', 19, 1, '2022-03-04 11:17:30', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (23, NULL, NULL, NULL, 'PV Inverter 5 kWp 5 yrs warranty', 19, 1, '2022-03-04 11:17:30', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (24, NULL, NULL, NULL, 'Panel Board, 4-Branches, Center Main, 100 AT', 19, 1, '2022-03-04 11:17:30', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (25, NULL, NULL, NULL, 'AC Breaker, 100 AT', 19, 1, '2022-03-04 11:17:30', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (26, NULL, NULL, NULL, 'Panel Board, 4-Branches, Center Main, 125 AT', 19, 1, '2022-03-04 11:17:30', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (27, NULL, NULL, NULL, 'Panel Board, 4-Branches, Center Main, 225 AT', 19, 1, '2022-03-04 11:17:30', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (28, NULL, NULL, NULL, 'AC Breaker, 125 AT', 19, 1, '2022-03-04 11:17:30', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (29, NULL, NULL, NULL, 'Eye Terminal', 19, 1, '2022-03-04 11:18:20', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (30, NULL, NULL, NULL, 'Tox No.6', 19, 1, '2022-03-04 11:18:20', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (31, NULL, NULL, NULL, 'Stainless Steel Bolt', 19, 1, '2022-03-04 11:18:20', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (32, NULL, NULL, NULL, 'Stainless Steel Tie 6\"', 19, 1, '2022-03-04 11:18:20', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (33, NULL, NULL, NULL, 'LQT Hoose', 15, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (34, NULL, NULL, NULL, 'Polyflex Connector', 19, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (35, NULL, NULL, NULL, 'Metal Clamp', 19, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (36, NULL, NULL, NULL, '3.5mm.sq. THHN Cu.Wire (blue)', 15, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (37, NULL, NULL, NULL, '3.5mm.sq. THHN Cu.Wire (red)', 15, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (38, NULL, NULL, NULL, '3.5mm.sq. THHN Cu.Wire (green)', 15, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (39, NULL, NULL, NULL, 'Ground Rod, Copper, 5/8\"', 19, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (40, NULL, NULL, NULL, 'Ground Clamp', 19, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (41, NULL, NULL, NULL, 'Cable Clip', 19, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (42, NULL, NULL, NULL, 'PVC Gutter / Cable Duct', 38, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (43, NULL, NULL, NULL, 'NEMA 1 enclosure (FOR LIMITER AND ISOLATION SWITCH)', 19, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (44, NULL, NULL, NULL, 'Connector,Copper for (no.8 to 1/0 AWG)', 19, 1, '2022-03-04 11:18:21', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (45, NULL, NULL, NULL, 'Speaker Wire, No. 18', 15, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (46, NULL, NULL, NULL, 'Royal Cord, No. 12, (Three Wire)', 15, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (47, NULL, NULL, NULL, 'Pull Box 4\" x 6\" x 6\"', 19, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (48, NULL, NULL, NULL, 'Tox No. 6 w/ screw', 19, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (49, NULL, NULL, NULL, 'Stainless Steel Bolt w/ nut & lock washer 3/16 x ½', 19, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (50, NULL, NULL, NULL, 'LQT Hose', 15, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (51, NULL, NULL, NULL, 'Tox No.6 w/ screw', 19, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (52, NULL, NULL, NULL, 'Stainless Steel Bolt w/ nut w/ lockwasher', 19, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (53, NULL, NULL, NULL, 'Tox No.6  w/ screw', 19, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (54, NULL, NULL, NULL, 'Stainless Steel Bolt w/ nut w/ lock washer', 19, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (55, NULL, NULL, NULL, '5.5mm.sq. THHN Cu.Wire (blue)', 15, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (56, NULL, NULL, NULL, '5.5mm.sq. THHN Cu.Wire (red)', 15, 1, '2022-03-04 11:18:22', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (57, NULL, NULL, NULL, '5.5mm.sq. THHN Cu.Wire (green)', 15, 1, '2022-03-04 11:18:23', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (58, NULL, NULL, NULL, '14mm.sq. THHN Cu.Wire (red)', 15, 1, '2022-03-04 11:18:23', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (59, NULL, NULL, NULL, '14mm.sq. THHN Cu.Wire (blue)', 15, 1, '2022-03-04 11:18:23', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (60, NULL, NULL, NULL, '30mm.sq. THHN Cu.Wire (blue)', 15, 1, '2022-03-04 11:18:23', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (61, NULL, NULL, NULL, '30mm.sq. THHN Cu.Wire (red)', 15, 1, '2022-03-04 11:18:23', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (62, NULL, NULL, NULL, '38mm.sq. THHN Cu.Wire (blue)', 15, 1, '2022-03-04 11:18:23', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (63, NULL, NULL, NULL, '38mm.sq. THHN Cu.Wire (red)', 15, 1, '2022-03-04 11:18:23', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (64, NULL, NULL, NULL, 'Sealant', 39, 1, '2022-03-04 11:18:46', NULL, NULL, 1);
INSERT INTO `items_main_description` VALUES (65, NULL, NULL, NULL, 'Electrical Tape, Big, Black, 3M', 30, 1, '2022-03-04 11:18:46', NULL, NULL, 1);

-- ----------------------------
-- Table structure for items_main_spec
-- ----------------------------
DROP TABLE IF EXISTS `items_main_spec`;
CREATE TABLE `items_main_spec`  (
  `sysid` int(11) NOT NULL,
  `itemid` int(11) NULL DEFAULT NULL,
  `typeid` int(11) NULL DEFAULT NULL,
  `codes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for items_main_types
-- ----------------------------
DROP TABLE IF EXISTS `items_main_types`;
CREATE TABLE `items_main_types`  (
  `sysid` int(11) NOT NULL,
  `itemid` int(11) NULL DEFAULT NULL,
  `codes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
