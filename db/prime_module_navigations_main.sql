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

 Date: 04/02/2022 14:01:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for prime_module_navigations_main
-- ----------------------------
DROP TABLE IF EXISTS `prime_module_navigations_main`;
CREATE TABLE `prime_module_navigations_main`  (
  `sysid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `parent` int(11) NULL DEFAULT NULL,
  `levels` int(11) NULL DEFAULT NULL,
  `type` int(11) NULL DEFAULT NULL,
  `sorting` int(11) NULL DEFAULT NULL,
  `htmlclass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `htmlid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `hashcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `pagefile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `withpay` tinyint(4) NULL DEFAULT 0,
  `status` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`sysid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 201 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of prime_module_navigations_main
-- ----------------------------
INSERT INTO `prime_module_navigations_main` VALUES (1, 'CC', 'Customer Care', 'Customer Care', 0, 1, 1, 1, 'info', '1', NULL, 'fa-umbrella', '356a192b7913b04c54574d18c28d46e6395428ab', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (2, 'Administration', 'Administration', 'Administration', 0, 1, 1, 2, 'info', '2', NULL, 'fa-scissors', 'da4b9237bacccdf19c0760cab7aec4a8359010b0', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (3, 'Finance', 'Finance', 'Financial Operation System', 0, 1, 1, 3, 'info', NULL, NULL, 'fa-columns', '77de68daecd823babbb58edb1c8e14d7106e83bb', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (4, 'OPP', 'Operations', 'Operations System', 0, 1, 1, 4, 'info', NULL, NULL, 'fa-wrench', '1b6453892473a467d07372d45eb05abc2031647a', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (5, 'REPORTS', 'Reports ', 'Transactions and Reports', 0, 1, 1, 5, 'warning', '0', '', 'fa-files-o', '511a418e72591eb7e33f703f04c3fa16df6c90bd', 'reports', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (7, 'ITD', 'ITD Menu', 'Information Department Menu', 2, 2, 2, 1, 'info', '0', NULL, 'fa-arrows', '25293f2761d658cc70c19515861842d712751bdc', 'itd', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (8, 'TELLER', 'Tellering', 'Teller Payment System', 1, 2, 3, 2, 'info', '3', 'user/tellering', 'fa-circle-o', 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'payment', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (9, 'CAD', 'Customer Applications', 'Customer Applications', 1, 2, 2, 3, 'info', '4', NULL, 'fa-circle-o', '0ade7c2cf97f75d009975f4d720d1fa6c19f4897', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (10, 'ASSET', 'Asset', 'Asset Management', 2, 2, 2, 2, 'info', '5', NULL, 'fa-circle-o', 'b1d5781111d84f7b3fe45a0852e59758cd7a87e5', NULL, 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (11, 'METER', 'Meter Reading', 'Meter Reading', 1, 2, 2, 4, 'info', '6', NULL, 'fa-circle-o', 'ac3478d69a3c81fa62e60f5c3696165a4e5e6ac4', 'meterreading', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (12, 'BILLING', 'Billing', 'Billing', 1, 2, 2, 5, 'info', '7', NULL, 'fa-circle-o', 'c1dfd96eea8cc2b62785275bca38ac261256e278', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (13, 'CUSTINFO', 'Customer Information', 'Customer Information System', 1, 2, 2, 8, 'info', '8', NULL, 'fa-circle-o', '902ba3cda1883801594b6e1b452790cc53948fda', 'custinfo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (14, 'HRIS', 'HR Info Sys', 'Human Resource Information System', 2, 2, 2, 3, 'info', '9', NULL, 'fa-circle-o', 'fa35e192121eabf3dabf9f5ea6abdbcbc107ac3b', 'hris', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (15, 'PLANASSESS', 'Planning & Assessment', 'Planning and Assessment', 1, 2, 2, 7, 'info', '10', 'list', 'fa-circle-o', 'f1abd670358e036c31296e66b3b66c382ac00812', 'planassess', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (16, 'LIS', 'Legal', 'Legal', 1, 2, 2, 6, 'info', '11', NULL, 'fa-circle-o', '1574bddb75c78a6fd2251d61e2993b5146201319', 'legal', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (17, 'MREADER', 'Meter Reader', 'Meter Reader', 11, 3, 2, 1, 'warning', '12', NULL, 'fa-angle-double-right', '0716d9708d321ffb6a00818614779e779925365c', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (18, 'MREADERENTRY', 'Data Entry', 'Meter Reading Entry', 17, 4, 2, 1, 'danger', '13', 'entry', 'fa-file', '9e6a55b6b4563e652a23be9d623ca5055c356940', 'reading', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (19, 'MREADERREPORT', 'Main Office', 'Main Office Analysis', 76, 4, 2, 1, 'danger', '14', 'table', 'fa-file-text', 'b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f', 'analysis', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (20, 'MRDAPP', 'MRD Lot & Book', 'MRD Lot & Book Cust. Application', 11, 3, 2, 4, 'warning', '15', NULL, 'fa-angle-double-right', '91032ad7bbcb6cf72875e8e8207dcfba80173f7c', 'mrdlotbook', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (21, 'ASSET', 'New Asset', 'New Asset Entry', 10, 3, 2, 1, 'warning', '16', 'new', 'fa-angle-double-right', '77de68daecd823babbb58edb1c8e14d7106e83bb', 'asset', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (22, 'ASSETREP', 'Asset Report', 'Asset Reports', 10, 3, 2, 2, 'warning', '17', NULL, 'fa-angle-double-right', '12c6fc06c99a462375eeb3f43dfd832b08ca9e17', 'assetreports', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (24, 'PURCH', 'Purchasing', 'Purchasing', 2, 2, 2, 4, 'info', NULL, NULL, 'fa-circle-o', '4d134bc072212ace2df385dae143139da74ec0ef', 'eprs', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (25, 'PRS', 'PRS', 'Purchase Request System', 24, 3, 2, 1, 'warning', NULL, NULL, 'fa-angle-double-right', 'f6e1126cedebf23e1463aee73f9df08783640400', 'eprs', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (26, 'PRSNEW', 'Create', 'Create PRS', 25, 4, 2, 1, 'danger', NULL, 'new', 'fa-file', '887309d048beef83ad3eabf2a79a64a389ab1c9f', 'eprs', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (27, 'PRSREP', 'My PRS', 'My PRS', 25, 4, 2, 2, 'danger', NULL, 'list', 'fa-file-text', 'bc33ea4e26e5e1af1408321416956113a4658763', 'eprs', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (28, 'PO', 'PO', 'PO', 24, 3, 2, 2, 'warning', NULL, NULL, 'fa-angle-double-right', '0a57cb53ba59c46fc4b692527a38a87c78d84028', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (29, 'BO', 'BOS', 'Budget Operation', 2, 2, 2, 5, 'info', NULL, NULL, 'fa-circle-o', '7719a1c782a1ba91c031a682a0a2f8658209adbf', 'bos', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (30, 'INV', 'Inventory', 'Inventory System', 2, 2, 2, 6, 'info', NULL, NULL, 'fa-circle-o', '22d200f8670dbdb3e253a90eee5098477c95c23d', 'inventory', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (31, 'ACCT', 'Accounting', 'Accounting System', 3, 2, 2, 1, 'info', NULL, NULL, 'fa-circle-o', '632667547e7cd3e0466547863e1207a8c0c0c549', 'acctg', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (32, 'EMP', 'Employee', 'Profiles (201) File', 14, 3, 2, 1, 'warning', NULL, NULL, 'fa-angle-double-right', 'cb4e5208b4cd87268b208e49452ed6e89a68e0b8', 'hris', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (33, 'ADD', 'New', 'New Account Profile', 32, 4, 2, 1, 'danger', NULL, 'new', 'fa-file', 'b6692ea5df920cad691c20319a6fffd7a4a766b8', 'hris', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (34, 'LIST', 'List', 'View Employee List', 32, 4, 2, 1, 'danger', NULL, 'list', 'fa-reorder', 'f1f836cb4ea6efb2a0b1b99f41ad8b103eff4b59', 'hris', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (35, 'NEWAPPIND', 'Residential', 'Residential', 187, 4, 2, 1, 'success', NULL, 'new', 'fa-user', '972a67c48192728a34979d9a35164c1295401b71', 'newaccount', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (36, 'PLAN', 'App. Inspection', 'Application Inspection', 15, 3, 2, 1, 'warning', NULL, 'list', 'fa-check', 'fc074d501302eb2b93e2554793fcaf50b3bf7291', 'inspection', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (38, 'BOLIST', 'Budgets', 'Budgets', 29, 3, 2, 2, 'warning', NULL, 'list', 'fa-reorder', '5b384ce32d8cdef02bc3a139d4cac0a22bb029e8', 'bos', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (39, 'PAYROLL', 'Payroll', 'Payroll', 31, 3, 2, 1, 'warning', NULL, 'list', 'fa-file', 'ca3512f4dfa95a03169c5a670a4c91a19b3077b4', 'payroll', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (40, 'MRDSCHED', 'Reading Schedule', 'Reading Schedule', 82, 4, 2, 3, 'info', NULL, 'new', 'fa-reorder', 'af3e133428b9e25c55bc59fe534248e6a0c0f17b', 'reading', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (41, 'SEARCH', 'Customer Inquiry', 'Customer Inquiry', 1, 2, 2, 2, 'info', '12', 'inquiry', 'fa-search', '761f22b2c1593d0bb87e0b606f990ba4974706de', 'custinfo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (42, 'BILLINGREP', 'Billing Reports', 'Billing Reports', 141, 3, 2, 1, 'warning', NULL, 'list', 'fa-file-text', '92cfceb39d57d914ed8b14d0e37643de0797ae56', 'billing', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (43, 'BILLPROC', 'Billing Process', 'Billing Process', 12, 3, 2, 2, 'warning', NULL, 'data', 'fa-file', '0286dd552c9bea9a69ecb3759e7b94777635514b', 'billing', 1, 1);
INSERT INTO `prime_module_navigations_main` VALUES (44, 'CRMMAIN', 'CRM', 'Customer Relation Management', 1, 2, 2, 1, 'info', NULL, NULL, 'fa-wrench', '98fbc42faedc02492397cb5962ea3a3ffc0a9243', 'ticketing/data', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (46, 'ENGINEERING', 'Engineering', 'Engineering Department', 4, 2, 2, 1, 'info', NULL, NULL, 'fa-gear', 'fe2ef495a1152561572949784c16bf23abb28057', 'engineering', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (47, 'ENGMTR', 'MTR Releasing', 'Meter Releasing', 46, 3, 2, 1, 'warning', NULL, 'new', 'fa-paper-plane', '827bfc458708f0b442009c9c9836f7e4b65557fb', 'mtrreleasing', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (48, 'GDLBM', 'Maintenance', 'Maintenance', 11, 3, 2, 5, 'warning', NULL, NULL, 'fa-gear', '64e095fe763fc62418378753f9402623bea9e227', 'gdlbmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (49, 'GDLBNEW', 'GDLB New', 'GDLB New', 48, 4, 2, 1, 'danger', NULL, 'new', 'fa-file', '2e01e17467891f7c933dbaa00e1459d23db3fe4f', 'gdlbmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (50, 'GDLBLIST', 'GDLB List', 'GDLB List', 48, 4, 2, 2, 'danger', NULL, 'list', 'fa-list', 'e1822db470e60d090affd0956d743cb0e7cdf113', 'gdlbmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (51, 'BOSPCEOVIEW', 'BOS PCEO VIEW', 'BOS PCEO VIEW', 29, 3, 2, 1, 'warning', NULL, 'data', 'fa-list', 'b7eb6c689c037217079766fdb77c3bac3e51cb4c', 'bospceo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (52, 'BILLADMIN', 'Billing Admin', 'Billing Administration', 12, 3, 2, 5, 'warning', NULL, NULL, 'fa-file-text', 'a9334987ece78b6fe8bf130ef00b74847c1d3da6', 'billing', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (53, 'BILLRATES', 'Billing Rates Main.', 'Billing Rates Maintenance', 52, 4, 2, 1, 'danger', NULL, 'table', 'fa-circle-o', 'c5b76da3e608d34edb07244cd9b875ee86906328', 'billing', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (54, 'BOSREQUEST', 'BOS Requests', 'BOS Requests', 29, 3, 2, 4, 'warning', NULL, 'list', 'fa-file-text', '80e28a51cbc26fa4bd34938c5e593b36146f5e0c', 'bos', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (55, 'DISCONAPP', 'Disconnection', 'Discconection Application', 9, 3, 2, 4, 'danger', '', 'new', 'fa-unlink', '8effee409c625e1a2d8f5033631840e6ce1dcb64', 'fdo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (56, 'LISNEW', 'Apprehension', 'Legal Apprehension', 16, 3, 2, 1, 'warning', NULL, 'new', 'fa-file', '54ceb91256e8190e474aa752a6e0650a2df5ba37', 'legal', 1, 0);
INSERT INTO `prime_module_navigations_main` VALUES (57, 'LISINS', 'Legal Inspection', 'Legal Inspection', 16, 3, 2, 2, 'warning', NULL, 'data', 'fa-search', '9109c85a45b703f87f1413a405549a2cea9ab556', 'legalinspection', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (58, 'LISLIST', 'Apprehension List', 'Apprehension List', 16, 3, 2, 3, 'warning', NULL, 'list', 'fa-table', '667be543b02294b7624119adc3a725473df39885', 'legal', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (59, 'LISVERIFY', 'Apprehension Verification', 'Apprehension Verification', 16, 3, 2, 4, 'warning', NULL, 'list', 'fa-search', '5a5b0f9b7d3f8fc84c3cef8fd8efaaa6c70d75ab', 'legalver', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (60, 'RECONAPP', 'Reconnection', 'Reconnection Application', 9, 3, 2, 3, 'warning', NULL, 'new', 'fa-link', 'e6c3dd630428fd54834172b8fd2735fed9416da4', 'custinfo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (61, 'PECOCALENDAR', 'Calendar', 'PECO Calendar', 2, 2, 4, 7, 'info', NULL, 'calendar', 'fa-calendar', 'calendar', 'calendar/main', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (62, 'CAD Maintenance', 'CAD Maintenance', 'CAD Maintenance', 9, 3, 2, 8, 'warning', '0', '', 'fa-gear', '6c1e671f9af5b46d9c1a52067bdf0e53685674f7', 'cadmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (64, 'APT', 'APT Reports', 'Application Process Time Reports', 5, 2, 2, 1, 'danger', '0', 'view', 'fa-files-o', 'a17554a0d2b15a664c0e73900184544f19e70227', 'reportsapt', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (65, 'USER', 'Users Report', 'Users Audit and Report', 5, 2, 2, 3, 'danger', '0', 'view', 'fa-files-o', 'c66c65175fecc3103b3b587be9b5b230889c8628', 'reportsusers', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (66, 'BUDGET', 'Budget Reports', 'Budget Reports', 5, 2, 2, 4, 'danger', '0', 'view', 'fa-files-o', '2a459380709e2fe4ac2dae5733c73225ff6cfee1', 'reportsbudget', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (67, 'COLLECTION', 'Collection Reports', 'Collection Reports', 5, 2, 2, 5, 'danger', '0', 'view', 'fa-files-o', '59129aacfb6cebbe2c52f30ef3424209f7252e82', 'reportscollection', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (68, 'ATTENDANCE', 'Daily Attendance', 'Daily Attendance Monitoring', 96, 4, 2, 2, 'danger', '0', 'list', 'fa-navicon', '4d89d294cd4ca9f2ca57dc24a53ffb3ef5303122', 'attendance', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (69, 'CUSTLIST', 'Customers List', 'Customers/Client List', 13, 3, 2, 2, 'warning', '0', 'list', 'fa-table', 'b4c96d80854dd27e76d8cc9e21960eebda52e962', 'custinfo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (70, 'CUSTCHART', 'Customers Chart', 'Customers/Client Chart', 13, 3, 2, 3, 'warning', '0', 'table', 'fa-bar-chart', 'a72b20062ec2c47ab2ceb97ac1bee818f8b6c6cb', 'custinfo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (71, 'MRDAREP', 'Analysis Reports', 'Meter Reading Analysis Reports', 141, 3, 2, 3, 'warning', NULL, 'view', 'fa-file-text', 'b7103ca278a75cad8f7d065acda0c2e80da0b7dc', 'mrdreports', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (73, 'MRDADDBILL', 'MRD Add Bill', 'Meter Readng Department Add Bill', 82, 4, 2, 5, 'info', '0', 'data', 'fa-calculator', 'd02560dd9d7db4467627745bd6701e809ffca6e3', 'mrdaddbill', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (74, 'HRMAIN', 'HR Maintenance', 'HR Maintenance', 14, 3, 2, 3, 'warning', '0', 'table', 'fa-wrench', '35e995c107a71caeb833bb3b79f9f54781b33fa1', 'hrmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (75, 'CHARGESMAIN', 'Charges Maintenance', 'Charges Maintenance', 52, 4, 2, 2, 'warning', '0', 'table', 'fa-tag', '1f1362ea41d1bc65be321c0a378a20159f9a26d0', 'billingmain', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (76, 'MRDANALYSIS', 'Analysis', 'Reading Analysis', 11, 3, 2, 2, 'warning', '0', '', 'fa-search', '450ddec8dd206c2e2ab1aeeaa90e85e51753b8b7', 'analysis', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (77, 'MREADERREPORT', 'Outsource', 'Outsource', 76, 4, 2, 2, 'danger', '14', 'records', 'fa-file-text', 'b3f0c7f6bb763af1be91d9e74eabfeb199dc1f1f', 'analysis', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (78, 'TICKETENTRY', 'Outages', 'Outages / Trouble Call Reports', 5, 2, 2, 2, 'danger', '0', 'table', 'fa-files-o', 'd321d6f7ccf98b51540ec9d933f20898af3bd71e', 'tsmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (79, 'TICKETREPORTS', 'CRM Dashboard', 'Customer Relation Management List / Dashboard', 44, 3, 2, 3, 'warning', '0', 'list', 'fa-bar-chart-o', 'eb4ac3033e8ab3591e0fcefa8c26ce3fd36d5a0f', 'crmmenu', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (82, 'MRDMENU', 'MRD Menu', 'Meter Reading Department Menu', 11, 3, 2, 3, 'warning', '0', '', 'fa-circle-o', 'b74f5ee9461495ba5ca4c72a7108a23904c27a05', 'mrdmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (83, 'READERACCOMP', 'Accomplishment', 'Reader Accomplishment', 82, 3, 2, 1, 'warning', '0', 'table', 'fa-angle-double-right', '76546f9a641ede2beab506b96df1688d889e629a', 'mrdmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (84, 'READERACCOMPENTRY', 'Accomp Entry', 'Reader Accomplishment Data Entry', 82, 3, 2, 2, 'warning', '0', 'data', 'fa-angle-double-right', '7d7116e23efef7292cad5e6f033d9a962708228c', 'mrdmenu/data', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (85, 'APPACCOMP', 'Application Accomplishment', 'Application Accomplishment', 9, 3, 2, 6, 'success', '0', NULL, 'fa-check', 'be461a0cd1fda052a69c3fd94f8cf5f6f86afa34', 'appaccomp', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (86, 'AUDIT', 'Audit', 'Audit Menu', 2, 2, 2, 9, 'danger', '0', 'data', 'fa-wrench', '1352246e33277e9d3c9090a434fa72cfa6536ae2', 'auditmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (87, 'ORVOID', 'OR Void', 'OR Void / Repair', 86, 3, 2, 2, 'info', '0', 'data', 'fa-refresh', '3c26dffc8a2e8804dfe2c8a1195cfaa5ef6d0014', 'auditmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (88, 'HRREP', 'HRIS Reports', 'HRIS Reports', 14, 3, 2, 5, 'warning', '0', '', 'fa-file', 'b37f6ddcefad7e8657837d3177f9ef2462f98acf', 'hrrep', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (89, 'EMPTARD', 'Tardiness Reports', 'Employee Tardiness Reports', 88, 4, 2, 4, 'info', '0', '', 'fa-file', 'e62d7f1eb43d87c202d2f164ba61297e71be80f4', 'hrrep/tardrep', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (90, 'CNCMENU', 'CNC Menu', 'CNC Menu', 1, 2, 2, 13, 'info', '0', 'list', 'fa-circle-o', '16b06bd9b738835e2d134fe8d596e9ab0086a985', 'cncmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (92, 'LEAVEREQNEW', 'New', 'New Leave Application', 74, 4, 2, 7, 'info', '0', 'new', 'fa-file-o', '4cd66dfabbd964f8c6c4414b07cdb45dae692e19', 'hrisleave', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (93, 'APPROVAL', 'Leave Reports', 'Leave Reports', 74, 4, 2, 8, 'warning', '0', 'list', 'fa-file', '8ee51caaa2c2f4ee2e5b4b7ef5a89db7df1068d7', 'hrisleave', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (94, 'CWDO', 'CWDO', 'Customer\'s Welfare Desk Officer', 1, 2, 2, 9, 'info', '0', 'data', 'fa-circle-o', '08a35293e09f508494096c1c1b3819edb9df50db', 'cwdo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (95, 'CWDOREP', 'CWDO Reports', 'CWDO Reports', 5, 2, 2, 7, 'danger', '0', 'table', 'fa-files-o', '215bb47da8fac3342b858ac3db09b033c6c46e0b', 'cwdorep', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (96, 'ATTENDANCE', 'Attendance', 'Attendance', 14, 3, 2, 6, 'warning', '0', 'table', 'fa-circle-o', '8e63fd3e77796b102589b1ba1e4441c7982e4132', 'attendance', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (97, 'CONTTBL', 'Contribution Table', 'Contribution Table', 31, 3, 2, 2, 'warning', '0', 'table', 'fa-reorder', '6fb84aed32facd1299ee1e77c8fd2b1a6352669e', 'conttbable', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (98, 'PAYROLLCA', 'Confidential', 'Confidential', 39, 4, 2, 1, 'danger', '0', 'new', 'fa-files-o', '812ed4562d3211363a7b813aa9cd2cf042b63bb2', 'payrollconfi', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (99, 'PAYROLLRF', 'Rank And File', 'Rank And File', 39, 4, 2, 2, 'info', '0', 'new', 'fa-files-o', '31bd9b9f5f7b338e41b56183a2f3008b541d7c84', 'payrollranknfile', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (100, 'PAYROLLMR', 'Meter Reader', 'Meter Reader', 39, 4, 2, 6, 'warning', '0', 'new', 'fa-files-o', '9a79be611e0267e1d943da0737c6c51be67865a0', 'payrollmeterreader', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (101, 'PAYROLLREP', 'Payroll Reports', 'Payroll Reports', 31, 3, 2, 4, 'info', '0', 'table', 'fa-files-o', '310b86e0b62b828562fc91c7be5380a992b2786a', 'payrollreports', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (102, 'CUSTTRACKER', 'Customer Tracker', 'Customer Geo Tracker', 1, 2, 2, 10, 'info', '0', 'search', 'fa-search', 'dbc0f004854457f59fb16ab863a3a1722cef553f', 'custtracker/search', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (103, 'ECALES', 'ECALES', 'ECALES Menu', 46, 3, 2, 2, 'warning', '0', 'data', 'fa-gears', 'c8306ae139ac98f432932286151dc0ec55580eca', 'ecalesmenu', 1, 0);
INSERT INTO `prime_module_navigations_main` VALUES (104, 'ECALESITEMS', 'Items Main.', 'Items Maintenance', 103, 4, 2, 2, 'danger', '0', 'table', 'fa-reorder', '934385f53d1bd0c1b8493e44d0dfd4c8e88a04bb', 'ecalesmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (105, 'ECALESTRANS', 'Transactions', 'Transactions', 103, 4, 2, 3, 'danger', '0', 'list', 'fa-calculator', '78a8efcbaaa1a9a30f9f327aa89d0b6acaaffb03', 'ecalesmenu', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (106, 'SERVICEFEE', 'Services Assessments', 'Services Assessments', 9, 3, 2, 5, 'warning', '0', 'new', 'fa-calculator', 'e114c448f4ab8554ad14eff3d66dfeb3965ce8fc', 'servfee', 1, 0);
INSERT INTO `prime_module_navigations_main` VALUES (107, 'NEWTICKET', 'New Ticket', 'New Ticket for CWDO', 94, 3, 2, 2, 'warning', '0', 'new', 'fa-file', '7224f997fc148baa0b7f81c1eda6fcc3fd003db0', 'cwdo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (108, 'LISTTICKET', 'Complaints Ticket Lists', 'Complaints Ticket Lists (CWD)', 94, 3, 2, 3, 'info', '0', 'list', 'fa-reorder', '524e05dc77239f3a15dab766aaa59a9e432efde7', 'cwdo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (109, 'SCHEDULES', 'Schedules', 'Schedules', 96, 4, 2, 3, 'danger', '0', 'table', 'fa-reorder', '17503a6b2326f09fbc4e3a7c03874c7333002038', 'attendance', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (110, 'SALARYENTRY', 'Salary Entry', 'Salary Entry', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5e796e48332af4142b10ca0f86e65d9bfdb05884', NULL, 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (111, 'SALARYENTRY', 'Salary Entry', 'Salary Entry', 32, 4, 2, 1, 'danger', NULL, 'new', 'fa-edit', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2', 'employeesalary', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (112, 'TCVIEW', 'TC Report View', 'TC Report View', 44, 3, 2, 5, 'info', '0', 'table', 'fa-table', '601ca99d55f00a2e8e736676b606a4d31d374fdd', 'tsmenu', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (113, 'TSSHIFT', 'SWB / TS Scheduling Shifting', 'SWB / TS Scheduling Shifting', 44, 3, 2, 6, 'warning', '0', 'records', 'fa-edit', '601ca99d55f00a2e8e736676b606a4d31d374fdd', 'tsmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (114, 'LINETEAM', 'Line Engineering', 'Line Engineering', 46, 3, 2, 3, 'info', '0', 'table', 'fa-flag-o', 'e993215bfdaa515f6ea00fafc1918f549119f993', 'engineering', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (115, 'BILLINQ', 'Account Inquiry', 'Account Inquiry', 1, 2, 2, 1, 'info', '0', 'inquiry', 'fa-search', 'ecb7937db58ec9dea0c47db88463d85e81143032', 'billing', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (117, 'USDASHBOARD', 'Utility Dashboard', 'Utility Dashboard', 46, 3, 2, 4, 'info', '0', 'list', 'fa-tag', '683e725c03a87baaad2623231644e944e537acab', 'utility', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (118, 'EMPPAYDATA', 'Payroll Data Entry', 'Payroll Data Entry', 32, 4, 2, 2, 'danger', '0', 'records', 'fa-table', 'd0e2dbb0bac1917d360aaf52c01a2a4b669e8cdb', 'hris', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (121, 'RVDASBHOARD', 'Request for Verification', 'Request for Verification Dashboard', 15, 3, 2, 2, 'warning', '0', 'list', 'fa-search', '12f0de3dc76e067d21ed85125716e02e9f1e69f0', 'rvmenu', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (122, 'BILLPROCT', 'Billing Process (CT)', 'Billing Process (CT-Gov)', 12, 3, 2, 4, 'warning', '0', 'new', 'fa-tag', '8bd7954c40c1e59a900f71ea3a266732609915b1', 'billingct', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (127, 'Meter Reading Encoder', 'Reading Encode', 'MRD Encoder', 17, 4, 2, 2, 'danger', '0', 'new', 'fa-file', '114d4eefde1dae3983e7a79f04c72feb9a3a7efd', 'mrdenc', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (128, 'MRDREADERTAG', 'Meter Reading Tagging', 'Meter Reading Tagging for Individual', 82, 4, 2, 3, 'info', '0', 'table', 'fa-table', '008451a05e1e7aa32c75119df950d405265e0904', 'mrdmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (129, 'HRMAINEMPCREDITS', 'Report', 'Emp. Leave Credits Report', 132, 5, 2, 2, 'warning', '0', 'table', 'fa-table', 'b4182bff4b3cf75f9e54f4990f9bd153c0c2973c', 'hrmainempcredits', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (130, 'HRMAINEMPCREDITSENTRY', 'Credits', 'Employee Leave Credits Entry', 132, 5, 2, 3, 'warning', '0', 'new', 'fa-table', '8b7471f4ae0bf59f5f0a425068c05d96f4801b9e', 'hrmainempcredits', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (131, 'HRMAINEMPLEAVEREQ', 'Request', 'Employee Leave Request', 132, 5, 2, 4, 'warning', '0', 'request', 'fa-table', '2a7541babb57434e5631ffa2b5639e24f8ce84fc', 'hrmainempcredits', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (132, 'HRMAINLEAVE', 'Emp. Leave', 'Employee Leave', 74, 4, 2, 5, 'info', '0', NULL, 'fa-table', 'e794a80eb109162d579df51db6d52e223bb0e9be', 'hrmainempcredits', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (133, 'WORKSHIFT', 'Workshift', 'Workshift', 74, 4, 2, 6, 'info', '0', '', 'fa-table', '91dfde1d6e005e422f64a59776234f1f4c80b5e4', 'workshiftmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (134, 'WORKSHIFTLIST', 'Workshift List', 'Workshift List', 133, 5, 2, 2, 'warning', '0', 'list', 'fa-table', 'd30f79cf7fef47bd7a5611719f936539bec0d2e9', 'workshiftmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (135, 'WORKSHIFTREQ', 'Workshift Requests', 'Workshift Requests', 133, 5, 2, 3, 'warning', '0', 'data', 'fa-table', '95e815d1541bf6f358cfffbe66ab3af0d0c09d09', 'workshiftmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (136, 'ATTENDANCEAPPROVAL', 'Attendance Approval', 'Attendance Approval', 96, 4, 2, 4, 'warning', '0', 'data', 'fa-table', '40f7c01f4189510031adccd9c604a128adaf9b00', 'attendance', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (137, 'ATTENDANCEREPORT', 'Attendance Report', 'Attendance Report', 96, 4, 2, 5, 'info', '0', 'table', 'fa-list', '9e071a3a594a8964cbefe784f8a6afaa94c0de17', 'report', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (138, 'PAYROLLPAYSLIP', 'Email Payslips', 'Email Payslips', 14, 3, 2, 7, 'warning', '0', 'reports', 'fa-envelope', 'e1a864f0b77f6c89794827a9035355dc8d052622', 'payrollreports', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (141, 'MRDREP', 'MRD Reports', 'MRD Reports', 5, 2, 2, 8, 'danger', '0', '', 'fa-files-o', '56ad4d4deaec98465c419b4a8ea7bfc1ed38c4d9', 'mrdreports', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (142, 'READINGREP', 'Reading Reports', 'Reading Reports', 141, 3, 2, 2, 'warning', NULL, 'list', 'fa-file-text', 'c9ca442765657fc90e9e779c34d0d2259d2c3c5b', 'mrdreports', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (143, 'MRDDATAFIX', 'MRD Data Fixing', 'MRD Data Fixing Error Records', 82, 4, 2, 6, 'danger', '0', 'table', 'fa-database', '2a2b47bf21a372f267deccbb420567f3d450b3c0', 'mrddata', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (144, 'ADDBILL', 'Add Bill Process', 'Add Bill Process', 76, 4, 2, 3, 'warning', '0', 'list', 'fa-file', 'f47aea8bdcbd1179a1f3d91e6afeeb259488f2d1', 'addbill', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (145, 'FINDINGSMAIN', 'Findings Maintenance', 'Findings Maintenance', 82, 4, 2, 7, 'danger', '0', 'list', 'fa-search', '7320828c9153b2a9848d6bc45d3544236b22fc48', 'findingsmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (146, 'CADASBOARD', 'Customer Applications Dashboard', 'Customer Applications Dashboard', 9, 3, 2, 1, 'info', '0', 'list', 'fa-table', '50336bc687eb161ee9fb0ddb8cf2b7e65bad865f', 'cad', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (147, 'BONUSES', 'Admin', 'Admin', 3, 2, 2, 2, 'warning', '0', 'admin', 'fa-table', '3fcfb99ec010d4a8ba364f43169465d91ca39ada', 'table', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (148, 'PS', 'Profit Share', 'Profit Share', 147, 3, 2, 13, 'warning', '0', 'table', 'fa-list', 'b3c0730cf3f50613e40561e67c871fdb92820cf9', 'ps', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (149, '13th', '13th Month', '13th Month', 147, 3, 2, 14, 'warning', '0', 'table', 'fa-list', '536fb6934062440c464ca2eef82b0be8e6b36cc8', '13th', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (150, '14th', '14th Month', '14th Month', 147, 3, 2, 15, 'warning', '0', 'table', 'fa-list', '39dfc9ffd3253c48c9af5dd55c4b3e4b4b5e6229', '14th', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (151, 'EMPEVAL', 'Employee Evaluation', 'Employee Evaluation', 32, 4, 2, 3, 'danger', '0', 'table', 'fa-table', '13682ac418603aa0966369d46bbf282f562acf47', 'eval', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (152, 'EMPPAYCLASS', 'Employee Payclass', 'Employee Payclass', 74, 4, 2, 7, 'info', '0', 'payclass', 'fa-list', 'b16a457a3302d7c1f4563df2ffc96dccf3779af7', 'employee', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (153, 'PAYROLLTIERD2', 'Tier 2', 'Tier 2', 39, 4, 2, 3, 'info', '0', 'new', 'fa-files-o', 'ac2646028f5b8b9bbf7a967f4ac71b8866135211', 'payrolltierd2', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (154, 'PAYROLLTIERD1', 'Tier 1', 'Tier 1', 39, 4, 2, 4, 'info', '0', 'new', 'fa-files-o', 'a6f16ab483da9847d431a822e6c85e144dc54f30', 'payrolltierd1', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (155, 'SL', 'SL Convertion', 'Sick Leave Convertion', 147, 3, 2, 16, 'warning', '0', 'sl', 'fa-table', '06349be70bd2d5dd98d36b9b8dba0a057500fdac', 'slconvert', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (156, 'MIS', 'Meter Issuance', 'Meter Issuance System', 10, 3, 2, 3, 'warning', '0', 'data', 'fa-angle-double-right', '9d8974baddfc0e53300829f37e5fc88b0f5ce61b', 'mis', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (157, 'MISLIST', 'List', 'Lists of Meters', 156, 4, 2, 2, 'danger', '0', 'list', 'fa-reorder', '6052521b7625e31d4ee9cc706732484fcf850877', 'mis', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (158, 'JO', 'Job Orders', 'Job Orders', 1, 2, 2, 14, 'info', '0', 'table', 'fa-circle-o', '097ccd4f03d962011101c1221009e53461a0993f', 'jodash', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (159, 'JODASH', 'Dashboard', 'Job Order Dashboard', 158, 3, 2, 1, 'danger', '0', 'table', 'fa-angle-double-right', 'be057d4ca44c10a0fc1dfcffd99cce1490291dc7', 'jodash', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (160, 'CMO', 'CMO', 'Change Meter Order', 158, 3, 2, 2, 'danger', '0', 'new', 'fa-angle-double-right', 'a3d12597f93e80f7f6a229cebb1c3e10d4f34ec3', 'cmo', 1, 0);
INSERT INTO `prime_module_navigations_main` VALUES (161, 'OIMR', 'OIMR', 'Order Immediate Meter Re-connection', 158, 3, 2, 3, 'danger', '0', 'new', 'fa-angle-double-right', '6b6277afcb65d33525545904e95c2fa240632660', 'oimr', 1, 0);
INSERT INTO `prime_module_navigations_main` VALUES (162, 'FDO', 'FDO', 'Final Disconnection Order', 158, 3, 2, 4, 'danger', '0', 'new', 'fa-angle-double-right', '0159a99ed28b0581890608d24ada9decc4874197', 'fdo', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (163, 'MRO', 'MRO', 'Meter Replacement Order', 158, 3, 2, 5, 'danger', '0', 'new', 'fa-angle-double-right', 'ae1e7198bc3074ff1b2e9ff520c30bc1898d038e', 'mro', 1, 0);
INSERT INTO `prime_module_navigations_main` VALUES (164, 'MISNEW', 'New', 'New Meter Issuance', 0, 4, 2, 1, 'info', '0', 'new', 'fa-files-o', 'fd93751649ac3ea8f8772ba49c8c1fe068002835', 'mis', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (165, 'UTILJO', 'Utility Job Orders', 'Utility Job Orders', 46, 3, 2, 5, 'info', '0', 'table', 'fa-table', 'a929eb33e338738d2a91e955ce7623764480253c', 'utility', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (166, 'MER', 'Month Employee', 'Monthly Employee Report', 88, 4, 2, 5, 'warning', '0', 'report', 'fa-list', '74cbd2c215c2c13c4b6110ada96de8891b355dda', 'list', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (167, 'UMTS', 'Utility MTS ', 'Utility Meter Transmittal System', 46, 3, 2, 6, 'info', '0', 'list', 'fa-table', '69e56976fc9bee70c1d2eaa85c0c8dea9f722a2f', 'umts', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (168, 'HRDHPROCPAYROLL', 'Process Payroll', 'Process Payroll (HRDH)', 14, 3, 2, 4, 'warning', '0', 'list', 'fa-refresh', '708a77db476d737e54b8bf4663fc79b346d696d2', 'hrdprocpay', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (169, 'PAYROLLCA', 'Confidential', 'Confidential', 168, 4, 2, 1, 'danger', '0', 'new', 'fa-files-o', '812ed4562d3211363a7b813aa9cd2cf042b63bb2', 'payrollconfi', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (170, 'PAYROLLRF', 'Rank And File', 'Rank And File', 168, 4, 2, 2, 'info', '0', 'new', 'fa-files-o', '31bd9b9f5f7b338e41b56183a2f3008b541d7c84', 'payrollranknfile', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (172, 'PAYROLLTIERD2', 'Tier 2', 'Tier 2', 168, 4, 2, 3, 'info', '0', 'new', 'fa-files-o', 'ac2646028f5b8b9bbf7a967f4ac71b8866135211', 'payrolltierd2', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (173, 'PAYROLLTIERD1', 'Tier 1', 'Tier 1', 168, 4, 2, 4, 'info', '0', 'new', 'fa-files-o', 'a6f16ab483da9847d431a822e6c85e144dc54f30', 'payrolltierd1', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (174, 'LEAVEAPPROVAL', 'Leave Approval', 'Leave Approval Transactions', 14, 3, 2, 8, 'warning', '0', 'table', 'fa-table', '572e20738130fddc7c389f2ab14f4e4b22a97c39', 'leaveapp', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (175, 'EMPLEAVEREQ', 'Employee Leave Requests', 'Dashboard or List of employee leave requests', 14, 3, 2, 2, 'warning', '0', 'list', 'fa-table', 'd094700e379f0fb3b543e25c77f8e4b3e068f057', 'empleavereq', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (176, 'REPPAYROLL', 'Payroll Reports', 'Payroll Reports', 5, 2, 2, 9, 'danger', '0', 'table', 'fa-files-o', '04f1241ed2b1b531c2c853ce1eeff952cd0f40f3', 'reppayroll', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (178, 'PAYROLLPAYSLIPS', 'Payslips', 'Printing and Sending of Payslips', 32, 4, 2, 4, 'danger', '0', 'list', 'fa-list-alt', '5c8f5ac0b7ad23c110793ad1fcf4d3c8d41344d5', 'hrrep', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (180, 'TECHLOG', 'Tech. Log', 'IT Technical Logs', 7, 3, 2, 2, 'warning', '0', 'list', 'fa-reorder', '25293f2761d658cc70c19515861842d712751bdc', 'itd', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (181, 'LEGALMENU', 'Legal Menu', 'Legal Menu', 16, 3, 2, 5, 'warning', '0', 'table', 'fa-arrows', 'ec7f1f65067126f3b2bd1037de8a18d0db2ec84b', 'legalmenu', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (182, 'LEGALSYNC', 'Data Sync', 'Data Syncing with Legacy', 181, 4, 2, 2, 'danger', '0', 'table', 'fa-refresh', 'aee544ceddfe7ab69a02f82bdf8ce6ea3862ff02', 'legalmenu', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (184, 'TNO', 'TNO', 'Turn On Order', 158, 3, 2, 6, 'danger', '0', 'new', 'fa-angle-double-right', '58f0744907ea8bd8e0f51e568f1536289ceb40a5', 'tno', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (185, 'ONLINEAPP', 'Online Applications', 'Online Applications', 9, 3, 2, 7, 'info', '0', 'list', 'fa-cloud-upload', 'bcf814ab41506290ab1b8158ebda6ee61b4bb579', 'onlineapplication', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (186, 'CCID', 'CC Maintenance', 'CC Maintenance', 14, 3, 2, 3, 'warning', '0', 'table', 'fa-building-o', 'cfa2ed2aac6d61f44ca9cba73e1e8946b7cd7d22', 'ccidmain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (187, 'NEWAPP', 'New Customer', 'New Customer', 9, 3, 2, 2, 'success', NULL, 'new', 'fa-file', 'f67462663a512121ffada791890b558ee8b38773', 'cad', 1, 1);
INSERT INTO `prime_module_navigations_main` VALUES (188, 'NEWAPPCOMM', 'Commercial', 'Commercial Accounts Application', 187, 4, 2, 2, 'success', NULL, 'new', 'fa-building', 'acf1fffc01dc0193aa07d0b1de723c292a2c826d', 'newaccountcomm', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (189, 'NEWAPPGOVT', 'Government', 'Government Connections', 187, 4, 2, 3, 'success', NULL, 'new', 'fa-globe', 'e54183e2a040e6c09e61eb22d542e3d57074b351', 'newaccountgovt', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (190, 'PAYROLLSP', 'Special', 'Special', 39, 4, 2, 5, 'success', '0', 'new', 'fa-copy', 'c05994e1e6c9378b407d0377cc8949ba76e9cf17', 'payrollspecial', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (191, 'PAYROLLSP', 'Special', 'Special', 168, 4, 2, 5, 'success', '0', 'new', 'fa-copy', 'c05994e1e6c9378b407d0377cc8949ba76e9cf17', 'payrollspecial', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (192, 'PRSAPPR', 'PRS Approvals', 'PRS Approvals', 25, 4, 2, 3, 'danger', '0', 'list', 'fa-files-o', '2fcc820fc1d95b1e8a3a219c7e3689bb8d65042c', 'eprsappr', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (193, 'PRSQ', 'Quotations', 'EPRS Item Quotations', 25, 4, 2, 4, 'danger', '0', 'list', 'fa-list', '19a448c01aa2e7d55979473b647e282459995b85', 'eprsquote', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (194, 'MNGINV', 'Manage Inventory', 'Manage Inventory', 30, 3, 2, 2, 'info', '0', 'table', 'fa-tag', '14bb99f81147d2705f53a1d75337b2ec3e10d23a', 'inventory', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (195, 'REPGINV', 'Reports Inventory', 'Reports Inventory', 30, 3, 2, 3, 'info', '0', 'list', 'fa-reorder', '2a79f14120945873482b7823caabe2fcde848722', 'inventory', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (196, 'PURCHASEMAIN', 'Maintenance', 'Purchasing Maintenance', 24, 3, 2, 3, 'info', '0', '', 'fa-gear', '752ae7bdbb96bf25280b55990570beabf2048ce0', 'purchasemain', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (197, 'SUPPLIERS', 'Suppliers List', 'Suppliers List and Maintenance', 196, 4, 2, 2, 'info', '0', 'list', 'fa-table', '4dea1daedbe9dc1d643b0f0eb8ab57c7d532f771', 'suppliers', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (198, 'MATREQ', 'Material Request Form', 'Material Request Form', 30, 3, 3, 2, 'info', '3', 'inventory/materialrequest', 'fa-circle-o', NULL, 'inventory', 0, 1);
INSERT INTO `prime_module_navigations_main` VALUES (199, 'REF', 'Referrals', 'Customer Application Referrals', 44, 3, 2, 7, 'info', '0', 'table', '', 'c837307a9a2ad4d08ca61a4f1bd848ba3d6890fc', 'custref', 0, 0);
INSERT INTO `prime_module_navigations_main` VALUES (200, 'CUSTASSESSMENT', 'Customer Assessment', 'Customer Application Assessment', 44, 3, 2, 8, 'info', '0', 'table', '5', '2952aeca0fe15cf310ede96c437acb94b2b208f1', 'custassessment', 0, 0);

SET FOREIGN_KEY_CHECKS = 1;
