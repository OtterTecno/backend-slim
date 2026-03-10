SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for examples
-- ----------------------------
DROP TABLE IF EXISTS `examples`;
CREATE TABLE `examples`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único del ejemplo',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del ejemplo',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Descripción del ejemplo',
  `activo` tinyint(1) NULL DEFAULT 1 COMMENT 'Estado del registro: 1 Activo, 0 Inactivo',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Fecha de eliminación logica',
  `created_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Usuario creador',
  `updated_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Usuario modificador',
  `deleted_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Usuario eliminador',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic COMMENT = 'Tabla de ejemplos base';

SET FOREIGN_KEY_CHECKS = 1;
