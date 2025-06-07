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

 Date: 04/02/2022 14:01:03
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for prime_transaction_flow_main_stages
-- ----------------------------
DROP TABLE IF EXISTS `prime_transaction_flow_main_stages`;
CREATE TABLE `prime_transaction_flow_main_stages`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `flowid` int(11) NULL DEFAULT NULL,
  `moduleid` int(11) NULL DEFAULT NULL,
  `levels` int(11) NULL DEFAULT NULL,
  `desc` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `types` int(11) NULL DEFAULT NULL,
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE,
  INDEX `moduleid_s`(`moduleid`) USING BTREE,
  INDEX `flowid_s`(`flowid`) USING BTREE,
  CONSTRAINT `prime_transaction_flow_main_stages_ibfk_1` FOREIGN KEY (`flowid`) REFERENCES `prime_transaction_flow_main` (`sysid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `prime_transaction_flow_main_stages_ibfk_2` FOREIGN KEY (`moduleid`) REFERENCES `prime_module_navigations_main` (`sysid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 92 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of prime_transaction_flow_main_stages
-- ----------------------------
INSERT INTO `prime_transaction_flow_main_stages` VALUES (1, 2, 187, 1, 'Profile Data Entry', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (2, 2, 36, 2, 'Inspection', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (3, 2, 15, 4, 'Assessment', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (4, 2, 35, 11, 'CAD', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (5, 2, 35, 5, 'AM', 1, 0);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (6, 2, 35, 3, 'AM', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (7, 2, 41, 2, 'CAD-Requirements', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (8, 2, 47, 8, 'Installations', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (9, 4, 19, 2, 'Analysis', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (10, 4, 42, 3, 'Billing', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (11, 5, 38, 3, 'BOS Officer', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (12, 5, 38, 2, 'Exec Approval', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (13, 5, 51, 4, 'PCEO Approval', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (14, 2, 13, 9, 'CAD', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (15, 2, 85, 10, 'Accomplishment', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (17, 4, 18, 1, 'Reading Entry', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (18, 6, 90, 1, 'CNC Head', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (19, 5, 38, 1, 'Department Head', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (20, 7, 56, 1, 'New Apprehension', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (22, 7, 57, 2, 'Apprehension Inspection', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (23, 2, 35, 7, 'Send To Customer', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (24, 8, 35, 1, 'Profile Data Entry', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (25, 8, 36, 2, 'Inspection', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (26, 8, 13, 5, 'Evaluation', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (27, 8, 35, 6, 'Audit', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (28, 8, 20, 7, 'MRD Lot & Book', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (29, 8, 35, 8, 'VPMCC', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (30, 8, 41, 9, 'Verifications', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (31, 8, 47, 10, 'Power Plant', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (32, 8, 19, 4, 'Analysis', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (33, 8, 18, 11, 'Accomplishment', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (34, 8, 59, 3, 'Legal Verification', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (35, 4, 73, 3, 'Compute BIlling', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (36, 6, 87, 2, 'Audit Cash', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (38, 9, 92, 1, 'Department Head', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (39, 9, 93, 2, 'Approval', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (40, 10, 101, 1, 'Draft', NULL, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (41, 10, 39, 2, 'P-CEO Approval', NULL, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (42, 11, 111, 1, 'Draft', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (43, 11, 111, 2, 'P-CEO Approval', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (44, 12, 74, 1, 'Approvals', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (45, 14, 54, 1, 'Budget Approval', NULL, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (46, 15, 75, 1, 'Audit', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (47, 15, 75, 2, 'Billing', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (48, 16, 160, 1, 'CMO Data', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (52, 16, 117, 2, 'Utility Accomplishment', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (54, 17, 161, 1, 'OIMR Data', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (56, 17, 117, 2, 'Utility Accomplishment', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (58, 18, 162, 1, 'FDO Data', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (60, 18, 117, 2, 'CAD Accomplishment', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (62, 19, 163, 1, 'MRO Data', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (64, 19, 117, 2, 'Utility Accomplishment', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (65, 20, 7, 1, 'Tech Log Data', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (66, 21, 188, 1, 'Profile Data Entry', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (67, 21, 36, 2, 'Inspection', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (68, 21, 59, 6, 'Legal Verification', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (69, 21, 13, 5, 'Assessments', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (70, 21, 13, 3, 'Analysis', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (71, 21, 35, 6, 'Audit', 1, 0);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (72, 21, 20, 4, 'MRD Lot & Book', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (73, 21, 188, 7, 'AM', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (74, 21, 41, 8, 'CAD-Transmital', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (75, 21, 47, 10, 'Power Plant', 1, 0);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (76, 21, 85, 11, 'Accomplishment', 1, 0);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (77, 22, 189, 1, 'Profile Data Entry', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (78, 22, 36, 2, 'Inspection', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (79, 22, 59, 6, 'Legal Verification', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (80, 22, 13, 5, 'Assessments', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (81, 22, 13, 3, 'Analysis', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (82, 22, 35, 6, 'Audit', 1, 0);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (83, 22, 20, 4, 'MRD Lot & Book', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (84, 22, 189, 7, 'AM', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (85, 22, 41, 8, 'CAD-Transmital', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (86, 22, 47, 10, 'Power Plant', 1, 0);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (87, 22, 85, 11, 'Accomplishment', 1, 0);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (88, 23, 184, 1, 'TNO', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (89, 23, 117, 2, 'Utility Accomplishment', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (90, 2, 35, 12, 'Billing', 1, 1);
INSERT INTO `prime_transaction_flow_main_stages` VALUES (91, 2, 35, 6, 'Analysis and Design', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
