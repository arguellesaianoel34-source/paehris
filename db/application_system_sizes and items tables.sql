/*
 Navicat Premium Data Transfer

 Source Server         : _PAE_ERP
 Source Server Type    : MySQL
 Source Server Version : 100414
 Source Host           : localhost:3306
 Source Schema         : pae_erp

 Target Server Type    : MySQL
 Target Server Version : 100414
 File Encoding         : 65001

 Date: 02/03/2022 21:56:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for application_customers_system_size
-- ----------------------------
DROP TABLE IF EXISTS `application_customers_system_size`;
CREATE TABLE `application_customers_system_size`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `appid` int(11) NULL DEFAULT NULL,
  `sysize` int(11) NULL DEFAULT NULL,
  `l1l2` decimal(20, 4) NULL DEFAULT NULL,
  `l1l3` decimal(20, 4) NULL DEFAULT NULL,
  `l2l3` decimal(20, 4) NULL DEFAULT NULL,
  `l1g` decimal(20, 4) NULL DEFAULT NULL,
  `l2g` decimal(20, 4) NULL DEFAULT NULL,
  `l3g` decimal(20, 4) NULL DEFAULT NULL,
  `l1l2a` decimal(20, 4) NULL DEFAULT NULL,
  `l1l3a` decimal(20, 4) NULL DEFAULT NULL,
  `l2l3a` decimal(20, 4) NULL DEFAULT NULL,
  `power` decimal(20, 4) NULL DEFAULT NULL,
  `nop` decimal(20, 4) NULL DEFAULT NULL,
  `rateclass` int(11) NULL DEFAULT NULL,
  `paneltype` int(11) NULL DEFAULT NULL,
  `roofinclination` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `inspectiondate` date NULL DEFAULT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT current_timestamp(0),
  `dateupdated` timestamp(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  `createdby` int(11) NOT NULL,
  `updatedby` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int(10) UNSIGNED NOT NULL DEFAULT 307,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of application_customers_system_size
-- ----------------------------
INSERT INTO `application_customers_system_size` VALUES (1, 1, 3, 230.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 10.0000, 0.0000, 0.0000, 2300.0000, 5.0000, 3, 1, '30deg', '2021-08-04', '', '2021-08-09 01:05:22', '2021-08-09 01:05:22', 1, '1', 305);
INSERT INTO `application_customers_system_size` VALUES (2, 1, 3, 230.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 10.0000, 0.0000, 0.0000, 0.0000, 0.0000, 3, 1, '30deg', '2021-08-04', '', '2021-08-09 02:10:41', '2021-08-09 02:10:41', 1, '1', 305);
INSERT INTO `application_customers_system_size` VALUES (3, 1, 3, 230.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 10.0000, 0.0000, 0.0000, 0.0000, 0.0000, 3, 1, '30deg', '2021-08-04', '', '2021-08-09 02:12:51', '2021-08-09 02:12:51', 1, '1', 307);
INSERT INTO `application_customers_system_size` VALUES (4, 1, 3, 230.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 10.0000, 0.0000, 0.0000, 2300.0000, 5.0000, 3, 1, '30deg', '2021-08-04', '', '2021-08-09 02:16:53', '2021-08-09 02:16:53', 1, '1', 305);
INSERT INTO `application_customers_system_size` VALUES (5, 1, 3, 230.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 10.0000, 0.0000, 0.0000, 0.0000, 0.0000, 3, 1, '30deg', '2021-08-04', '', '2021-08-09 02:16:55', '2021-08-09 02:16:55', 1, '1', 307);
INSERT INTO `application_customers_system_size` VALUES (6, 4, 3, 230.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 10.0000, 0.0000, 0.0000, 2300.0000, 5.0000, 1, 1, '45deg', '2021-08-12', 'Need authentication', '2021-08-09 10:26:13', '2021-08-09 10:26:13', 1, '1', 307);
INSERT INTO `application_customers_system_size` VALUES (7, 4, 3, 230.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 10.0000, 0.0000, 0.0000, 2300.0000, 5.0000, 1, 1, '45deg', '2021-08-12', 'Need authentication', '2021-08-09 10:26:15', '2021-08-09 10:26:15', 1, '1', 305);
INSERT INTO `application_customers_system_size` VALUES (8, 203, 3, 212.0000, 0.0000, 0.0000, 210.0000, 0.0000, 0.0000, 5.0000, 0.0000, 0.0000, 2110.0000, 5.0000, 1, 1, '30', '0000-00-00', '', '2022-02-25 17:19:46', '2022-02-27 08:31:37', 1, '1', 307);
INSERT INTO `application_customers_system_size` VALUES (9, 203, 10, 212.0000, 0.0000, 0.0000, 210.0000, 0.0000, 0.0000, 5.0000, 0.0000, 0.0000, 2110.0000, 5.0000, 1, 3, '30', '0000-00-00', '', '2022-02-27 08:38:54', '2022-02-27 08:38:54', 1, '1', 305);

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
  `datecreated` timestamp(0) NULL DEFAULT current_timestamp(0),
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
  `sysid` int(11) NOT NULL,
  `systypeid` int(11) NULL DEFAULT NULL,
  `itemid` int(11) NULL DEFAULT NULL,
  `specid` int(11) NULL DEFAULT NULL,
  `unitid` int(11) NULL DEFAULT NULL,
  `qty` int(11) NULL DEFAULT NULL,
  `typesid` int(11) NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT 1,
  `datecreated` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

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
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

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
INSERT INTO `customer_system_size` VALUES (10, 4.00, 8.00, NULL, '3.6kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (11, 9.00, 12.00, NULL, '5.4kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (12, 13.00, 18.00, NULL, '8.1kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (13, 19.00, 23.00, NULL, '10.35kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (14, 24.00, 30.00, NULL, '13.5kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (15, 31.00, 36.00, NULL, '16.2kWp Grid-Tied', 1);
INSERT INTO `customer_system_size` VALUES (16, 37.00, 47.00, NULL, '21.15kWp Grid-Tied', 1);

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
  `datecreated` timestamp(0) NULL DEFAULT current_timestamp(0),
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
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of items_main_components
-- ----------------------------
INSERT INTO `items_main_components` VALUES (1, 2, 'M', 'Mouse', 'Mouse', NULL, 1);
INSERT INTO `items_main_components` VALUES (2, 2, 'K', 'Keybaord', 'Keybaord', NULL, 1);
INSERT INTO `items_main_components` VALUES (3, 1, 'C', 'Cariage', 'Cariage', NULL, 1);
INSERT INTO `items_main_components` VALUES (4, 4, 'P', 'Panel', 'Panel', NULL, 1);
INSERT INTO `items_main_components` VALUES (5, 4, 'W', 'Wires', 'Wires', NULL, 1);
INSERT INTO `items_main_components` VALUES (6, 4, 'B', 'Bars', 'Bars', NULL, 1);
INSERT INTO `items_main_components` VALUES (7, 4, 'B', 'Battery', 'Battery', NULL, 1);
INSERT INTO `items_main_components` VALUES (8, 4, 'I', 'Inverter', 'Inverter', NULL, 1);

-- ----------------------------
-- Table structure for items_main_description
-- ----------------------------
DROP TABLE IF EXISTS `items_main_description`;
CREATE TABLE `items_main_description`  (
  `sysid` int(11) NOT NULL,
  `itemid` int(11) NULL DEFAULT NULL,
  `typeid` int(11) NULL DEFAULT NULL,
  `specid` int(11) NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `createdby` int(11) NULL DEFAULT NULL,
  `datecreated` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for items_main_details
-- ----------------------------
DROP TABLE IF EXISTS `items_main_details`;
CREATE TABLE `items_main_details`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NULL DEFAULT NULL,
  `itemgrpid` int(11) NULL DEFAULT NULL,
  `specid` int(11) NULL DEFAULT NULL,
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '1',
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for items_main_spec
-- ----------------------------
DROP TABLE IF EXISTS `items_main_spec`;
CREATE TABLE `items_main_spec`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NULL DEFAULT NULL,
  `codes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `names` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `descs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `typeid` int(11) NULL DEFAULT NULL,
  `unitid` int(11) NULL DEFAULT NULL,
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of items_main_spec
-- ----------------------------
INSERT INTO `items_main_spec` VALUES (1, 2, 'MKWNP', 'Mechanical Keybaord w/ Num Pads', 'Mechanical Keybaord w/ Num Pads', NULL, 35, 1);
INSERT INTO `items_main_spec` VALUES (2, 2, 'MKWNPG', 'Mechanical Keybaord w/ Num Pads (green)', 'Mechanical Keybaord w/ Num Pads (green)', NULL, 35, 1);
INSERT INTO `items_main_spec` VALUES (3, 2, 'MKWNP', 'Mechanical Keybaord w/ Num Pads (red)', 'Mechanical Keybaord w/ Num Pads (red)', NULL, 35, 1);
INSERT INTO `items_main_spec` VALUES (4, 2, 'MKWNPB', 'Mechanical Keybaord w/ Num Pads (black)', 'Mechanical Keybaord w/ Num Pads (black)', NULL, 35, 1);
INSERT INTO `items_main_spec` VALUES (5, 2, 'GMRR', 'Gaming Mouse RoG (red)', 'Gaming Mouse RoG (red)', NULL, 35, 1);
INSERT INTO `items_main_spec` VALUES (6, 1, '3DX41', 'Bolt(s) 3/8\" dia. x 4 1/2\"', 'Bolt (s) 3/8\" dia. x 4 1/2\"', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (7, 4, '25M', '200cm by 500cm - Monocrystalline Panels', '200cm by 500cm - Monocrystalline Panels', NULL, 16, 1);
INSERT INTO `items_main_spec` VALUES (8, 4, 'TW', 'Tinsel Wire (10 gauge, 30amps)', 'Tinsel Wire (10 gauge, 30amps)', NULL, 15, 1);
INSERT INTO `items_main_spec` VALUES (9, 4, 'AB1A', 'Aluminum Bar(s) (1/2in Angle)', 'Aluminum Bar(s) (1/2in Angle)', NULL, 15, 1);
INSERT INTO `items_main_spec` VALUES (10, 4, 'B2', 'Gel Battery 12v 200Ah', 'Gel Battery 12v 200Ah', NULL, 35, 1);
INSERT INTO `items_main_spec` VALUES (11, 4, 'PI22MHF', ' Power Inverter 2kVA 2000W MPPT High Frequency', ' Power Inverter 2kVA 2000W MPPT High Frequency', NULL, 35, 1);
INSERT INTO `items_main_spec` VALUES (12, 4, 'EPMP', 'Export Power Manager (3 Phase)', 'Export Power Manager (3 Phase)', NULL, 20, 1);
INSERT INTO `items_main_spec` VALUES (13, 4, '6SC', '6mm2 DC Solar Cable', '6mm2 DC Solar Cable', NULL, 15, 1);
INSERT INTO `items_main_spec` VALUES (14, 4, 'B1', 'DC Breaker 16 AT', 'DC Breaker 16 AT', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (15, 4, 'B3', 'DC Breaker 32 AT', 'DC Breaker 32 AT', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (16, 4, 'B', 'AC Breaker 20 AT', 'AC Breaker 20 AT', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (17, 4, 'B', 'AC Breaker 32 AT', 'AC Breaker 32 AT', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (18, 4, 'P', 'AC SPD - 3 Phase', 'AC SPD - 3 Phase', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (19, 4, 'P', 'AC SPD - 1 Phase', 'AC SPD - 1 Phase', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (20, 4, '', 'DC SPD', 'DC SPD', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (21, 4, '8EC', '8-Way Enclosure Box / Combiner Box', '8-Way Enclosure Box / Combiner Box', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (22, 4, 'C', 'MC4 Connector', 'MC4 Connector', NULL, 37, 1);
INSERT INTO `items_main_spec` VALUES (23, 4, '4R', '4.4mm Railings', '4.4mm Railings', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (24, 4, 'FS', 'L Feet Screw', 'L Feet Screw', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (25, 4, 'C', 'Mid Clamp', 'Mid Clamp', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (26, 4, 'C', 'End Clamp', 'End Clamp', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (27, 4, 'WDLS', 'Wi-Fi Data Logging Stick', 'Wi-Fi Data Logging Stick', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (28, 4, 'C', 'CT-45', 'CT-45', NULL, 19, 1);
INSERT INTO `items_main_spec` VALUES (29, 4, 'L', 'Limiter', 'Limiter', NULL, 19, 1);

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
  `datecreated` timestamp(0) NULL DEFAULT current_timestamp(0),
  `updatedby` int(11) NULL DEFAULT NULL,
  `dateupdated` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
