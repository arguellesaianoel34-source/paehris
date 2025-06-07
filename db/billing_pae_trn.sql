/*
 Navicat Premium Data Transfer

 Source Server         : LOCALHOST
 Source Server Type    : MySQL
 Source Server Version : 100411
 Source Host           : localhost:3306
 Source Schema         : pae

 Target Server Type    : MySQL
 Target Server Version : 100411
 File Encoding         : 65001

 Date: 25/11/2021 05:53:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for billing_pae_trn
-- ----------------------------
DROP TABLE IF EXISTS `billing_pae_trn`;
CREATE TABLE `billing_pae_trn`  (
  `sysid` int NOT NULL AUTO_INCREMENT,
  `billno` int NULL DEFAULT NULL,
  `appid` int NULL DEFAULT NULL,
  `years` int NULL DEFAULT NULL,
  `months` int NULL DEFAULT NULL,
  `duedate` date NULL DEFAULT NULL,
  `amount` decimal(20, 4) NULL DEFAULT NULL,
  `printed` int NULL DEFAULT NULL,
  `emailed` int NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT current_timestamp(0),
  `dateupdated` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `createdby` int NOT NULL,
  `updatedby` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int UNSIGNED NOT NULL DEFAULT 300,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of billing_pae_trn
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
