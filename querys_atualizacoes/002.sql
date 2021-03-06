INSERT INTO `TREDE_USUADMIN` (`REDE_SEQUSUA`, `REDE_NOMEUSU`, `REDE_ADMINUS`, `REDE_TIPOUSU`, `REDE_USUBLOC`, `REDE_EMAILUS`, `REDE_LOGUSUA`,`REDE_SENHAUS` ) VALUES ('1', 'Joao Admin', 's', '1', 'n', 'joao@admin', 'joao_admin','219751113eff0fb3c396969b8d52d5fd');

INSERT INTO `TREDE_USUADMIN` (`REDE_SEQUSUA`, `REDE_NOMEUSU`, `REDE_ADMINUS`, `REDE_SENHAUS`, `REDE_TIPOUSU`, `REDE_USUBLOC`, `REDE_EMAILUS`, `REDE_LOGUSUA`) VALUES ('2', 'Manu Admin', 's', 'c56a885c1735f9c3de9dc27eca149952', '1', 'n', 'manu@admin', 'manu_admin');

INSERT INTO `TREDE_USUADMIN` (`REDE_SEQUSUA`, `REDE_NOMEUSU`, `REDE_ADMINUS`, `REDE_TIPOUSU`, `REDE_USUBLOC`, `REDE_EMAILUS`, `REDE_LOGUSUA`,`REDE_SENHAUS`) VALUES ('3', 'Marcelo Admin', 's', '1', 'n', 'marcelo@admin', 'marcelo_admin','254243d7eeb8d6d4f50c51b37bb0fab3');

CREATE TABLE `TREDE_RULES` ( `NNUMERULE` INT(11) AUTO_INCREMENT COMMENT 'SEQUENCIA DA TABELA', `REDE_SEQUSUA` INT(11) COMMENT 'NUMERO DO USUARIO TABELA TREDE_USUAADMIN', `NPERMRULE` INT(11) COMMENT 'NUMERO DA PERMISSÃO 1 -  ADMIN MAJORITARIO, 2 - DEVELOPERS, 3 - ADMIN CLIENTE NIVEL 1,  4 - ADMIN CLIENTE NIVEL 2, 5 - ADMIN CLIENTE NIVEL 2, 6 - USUARIO SIMPLES', `CSITURULE` CHAR(1) COMMENT 'SE ESTA ATIVO OU NÃO', KEY(`NNUMERULE`) );

INSERT INTO `TREDE_RULES` (`REDE_SEQUSUA`,`NPERMRULE`,`CSITURULE`) VALUES ('1','1','A');
INSERT INTO `TREDE_RULES` (`REDE_SEQUSUA`,`NPERMRULE`,`CSITURULE`) VALUES ('2','1','A');
INSERT INTO `TREDE_RULES` (`REDE_SEQUSUA`,`NPERMRULE`,`CSITURULE`) VALUES ('3','1','A');

CREATE TABLE `TREDE_PERMISSAO` ( `SEQPERMIS` INT AUTO_INCREMENT, `MENUS` VARCHAR(300) COMMENT 'NOME DOS MENUS', `SITUACAO` INT COMMENT '0- INATIVO | 1 - ATIVO', KEY(`SEQPERMIS`) );

CREATE TABLE `TREDE_ADMINS` ( `NNUMEADMIN` INT(11) AUTO_INCREMENT, `EMAILADMIN` VARCHAR(400) COMMENT 'email',`SENHADMIN` VARCHAR(400) COMMENT 'SENHA', `NIVELADMIN` INT(11) COMMENT 'nivel de permissão', KEY(`NNUMEADMIN`) );

ALTER TABLE `TREDE_ADMINS` ADD COLUMN `SENHAADMIN` VARCHAR(400) NULL COMMENT 'SENHA' AFTER `NIVELADMIN`;


CREATE TABLE `TREDE_EXTRATO_USUA` ( `NNUMEXTRA` INT(11) AUTO_INCREMENT, `NNUMEUSUA` INT(11) COMMENT 'NUMERO DO USUARIO', `DEBITO` VARCHAR(20), `CREDITO` VARCHAR(20), `DTRAEXTRA` DATETIME COMMENT 'DATA DA TRANSAÇÃO', `NPATEXTRA` INT(11) COMMENT 'NUMERO DO PATROCINADOR', `CTIPEXTRA` VARCHAR(30) COMMENT 'TIPO DE TRANSAÇÃO',`CTPOEXTRA` CHAR(1) NULL COMMENT 'SE É DEBITO OU CREDITO', KEY(`NNUMEXTRA`) );

ALTER TABLE `TREDE_EXTRATO_USUA` ADD COLUMN `SEQUPAGPLAN` INT(11) NULL COMMENT 'SEQUENCIA DO PAGAPLANO';

ALTER TABLE `TREDE_EXTRATO_USUA` ADD COLUMN `NNUMEUSUA1` INT(11) NULL COMMENT 'NUMERO USUSARIO 1' AFTER `SEQUPAGPLAN`;

ALTER TABLE `TREDE_PAGAGERACASH` ADD COLUMN `CSITUPGPAG` INT(11) NULL COMMENT 'SITUAÇÃO DO PAGAMENTO' AFTER `DDATAPGPAG`;

ALTER TABLE `TREDE_PAGAGERACASH` ADD COLUMN `CTIPOOPPAG` CHAR(1) NULL COMMENT 'TIPO DE TRANSAÇÃO D OU C' AFTER `CSITUPGPAG`;

INSERT INTO `TREDE_PERMISSAO` (`MENUS`, `SITUACAO`) VALUES ('MENU_AFILIADOS', '0');



CREATE TABLE `TREDE_SOLICITASAQUE_UNI` ( `SEQUENCIA` INT(11), `REDE_SEQUSUA` INT(11) COMMENT 'ID SO USUARIO', `VALORSAQUE` VARCHAR(50) COMMENT 'VALOR DO SAQUE', `DATASAQUE` DATETIME COMMENT 'DATA DA SOLICITAÇÃO', `DATAPAGO` DATETIME COMMENT 'DATA DA APROVAÇÃO', `SITUSAQUE` VARCHAR(10) COMMENT 'SITUAÇÃO DO SAQUE', `DATACANCELADO` DATETIME COMMENT 'DATA DO CANCELAMENTO' );


CREATE TABLE `TREDE_SAQUEMIN_UNI` ( `SEQ` INT AUTO_INCREMENT, `VALOR` VARCHAR(30), `EMPRESA` INT, KEY(`SEQ`) );