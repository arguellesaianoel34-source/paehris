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

 Date: 04/02/2022 14:00:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for prime_transaction_flow_main
-- ----------------------------
DROP TABLE IF EXISTS `prime_transaction_flow_main`;
CREATE TABLE `prime_transaction_flow_main`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `moduleid` int(11) NULL DEFAULT NULL COMMENT 'THE STARTING MODULE',
  `codes` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `names` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `desc` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '1',
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of prime_transaction_flow_main
-- ----------------------------
INSERT INTO `prime_transaction_flow_main` VALUES (1, 21, 'ASSETS_NEW', 'Asssets New', 'For Creation of New Assets', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (2, 187, 'APPLICATIONS', 'New Account Application', 'For Creation of New Account', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (3, 26, 'EPRS', 'ePRS', 'ePRS', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (4, 19, 'MRDREADER', 'MRD Meter Reader', 'For Reading', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (5, 37, 'BOSNEW', 'BOS Creation', 'Budget Creation New', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (6, 8, 'ORVOID', 'OR Void', 'OR Void', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (7, 56, 'APPREHENSION', 'New Apprehension', 'New Apprehension', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (8, 60, 'APPLICATIONS', 'New Account Application Corp.', 'New Account Application for Corporation', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (9, 92, 'LEAVEAPP', 'Leave Application', 'New Leave Application', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (10, 39, 'PAYROLL', 'Payroll Reports', 'Payroll Reports', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (11, 111, 'SALARYINC', 'Salary Increase Transaction', 'Salary Increase Transaction', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (12, 74, 'EMPSHIFTING', 'Employee Workshift Shifting', 'Employee Workshift Shifting', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (14, 54, 'BUDGETAPPROVAL', 'Budget Approval', 'Approval for budget', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (15, 75, 'BILLRATESAPPROVAL', 'Billing Rates Approval', 'Billing Rates Approval', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (16, 160, 'JOBORDERCMO', 'CMO', 'Change Meter Order', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (17, 161, 'JOBORDEROIMR', 'OIMR', 'Order of Immediate Meter Replacement', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (18, 162, 'JOBORDERTFDO', 'FDO', 'Final Disconnection Order', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (19, 163, 'JOBORDERMRO', 'MRO', 'Meter Replacement Order', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (20, 7, 'TECHLOGS', 'IT Tech Log', 'ITD Technical Logs', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (21, 188, 'APPLICATIONS', 'New Account Applicaiton Commercial', 'For Creation of New Account Commercial Customer Type', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (22, 189, 'APPLICATIONS', 'New Account Applicaiton Government', 'For Creation of New Account Government Customer Type', '1');
INSERT INTO `prime_transaction_flow_main` VALUES (23, 184, 'JOBORDERTNO', 'TNO', 'Turn-On Order', '1');

SET FOREIGN_KEY_CHECKS = 1;
