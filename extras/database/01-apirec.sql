/**!
 * @package   ReceiptAPI
 * @filename  01-apirec.sql
 * @version   1.0
 * @autor     Díaz Urbaneja Víctor Eduardo Diex <diazvictor@tutamail.com>
 * @date      21.11.2023 17:05:17 -04
 */

-- -----------------------------------------------------
-- Schema apirec
-- -----------------------------------------------------
-- create schema if not exists elretenciondb default character set latin1;
-- use elretenciondb ;

-- -----------------------------------------------------
-- Table apirec_recibo
-- -----------------------------------------------------
drop table if exists apirec_recibo_adjunto cascade;
drop table if exists apirec_recibo cascade;

--id_recibo			int not null auto_increment primary key 		comment 'id de este recibo',
create table if not exists apirec_recibo (
	id_recibo			varchar(80) not null primary key         		comment 'id de este recibo YYYYMMDDHHMMSS',
	rif_agente			varchar(80) not null 							comment 'rif del comprador',
	rif_sujeto			varchar(80) default null 						comment 'rif del vendedor',
	cod_recibo 			varchar(80) not null 							comment 'yyyymmddhhmmss codigo unico de retencion interno',
	num_recibo 			varchar(80) not null 							comment 'yyyymmdd+nnnnnnnn numero unico desde el seniat',
	num_control 		varchar(80) not null,
	fecha_factura		varchar(80) default null 						comment 'yyyymmdd del recibo en el momento de concretar la compra..  indicado en el documento',
	fecha_compra		varchar(80) default null 						comment 'yyyymmdd en el momento que se empezo la compra, fecha actual al crear el recibo',
	monto_imponible		decimal(40,2) default null 						comment 'base imponible o monto sin el iva permite 0',
	monto_excento 		decimal(40,2) default null 						comment 'monto al cual el iva no se aplica y admite 0',
	monto_iva 			decimal(40,2) default 0.00	 					comment 'monto del iva o de la retencion si es islr',
	tasa_iva 			decimal(40,2) default 0.00 						comment 'monto del iva o de la retencion si es islr',
	tipo_recibo 		enum('factura', 'nota') null default 'factura'	comment 'factura | nota',
	sessionflag 		varchar(80) default null 						comment 'yyyymmddhhmmss.entidad.usuario quien altero',
	sessionficha		varchar(80) default null 						comment 'id del mismo adjunto pero si esta en db tambien'
)
comment = 'tabla centralizada de registros de recibos';

alter table apirec_recibo drop index if exists idx_cod_recibo;
alter table apirec_recibo drop index if exists idx_num_recibo;
alter table apirec_recibo drop index if exists idx_num_control;

create unique index idx_cod_recibo on apirec_recibo(cod_recibo);
create unique index idx_num_recibo on apirec_recibo(num_recibo);
create unique index idx_num_control on apirec_recibo(num_control);

-- -----------------------------------------------------
-- Table apirec_recibo_adjunto
-- -----------------------------------------------------

create table if not exists apirec_recibo_adjunto (
	id_adjunto			int not null auto_increment primary key 		comment 'id de este adjunto',
	id_recibo			varchar(80) not null							comment 'id que hace referencia a un recibo',
	adjunto				longtext default null 							comment 'adjunto escaneado pero guardado en db',
	ruta				varchar(80) default null 						comment 'ruta absoluta del recibo.. separador de directorios es barra dividir',
	sessionflag 		varchar(80) default null 						comment 'yyyymmddhhmmss.entidad.usuario quien altero',
	sessionficha		varchar(80) default null 						comment 'yyyymmddhhmmss.entidad.usuario quien creo',
	foreign key (id_recibo) references apirec_recibo(id_recibo)
)
comment = 'ubicacion del recibo en db o en sistema de ficheros';

alter table apirec_recibo_adjunto drop index if exists idx_id_recibo;
create unique index idx_id_recibo on apirec_recibo_adjunto(id_recibo);

-- -----------------------------------------------------
-- Table apirec_usuarios
-- -----------------------------------------------------
-- drop table if exists apirec_usuarios ;
drop table if exists apirec_usuarios_permisos cascade;
drop table if exists apirec_usuarios cascade;

create table if not exists apirec_usuarios (
	id_usuario			int not null auto_increment primary key 		comment 'id de este usuario',
	username			varchar(80) not null 							comment 'login del usuario, id del correo para este sistema especifico',
	userkey				varchar(80) null default null 					comment 'sincronia con al calve del usuario',
	status				enum('activo', 'inactivo')	default 'activo'	comment 'activo|inactivo',
	sessionflag			varchar(80) null default null 					comment 'yyyymmddhhmmss.entidad.usuario quien altero',
	sessionficha		varchar(80) null default null 					comment 'yyyymmddhhmmss.entidad.usuario quien creo'
);
-- engine = innodb;

alter table apirec_usuarios drop index if exists idx_username;
create unique index idx_username on apirec_usuarios(username);

-- -----------------------------------------------------
-- Table apirec_usuarios_permisos
-- -----------------------------------------------------

create table if not exists apirec_usuarios_permisos (
	id_permisos				int not null auto_increment primary key 	comment 'id de este permiso',
	id_usuario 				int not null 								comment 'id que hace referencia a un usuario',
	cod_modulo 				varchar(80) not null default 'all' 			comment 'en que ambitos de recibo puede operar este usuario',
	sessionflag 			varchar(80) null default null 				comment 'yyyymmddhhmmss.entidad.usuario quien altero',
	sessionficha 			varchar(80) null default null 				comment 'yyyymmddhhmmss.entidad.usuario quien creo',
	foreign key (id_usuario) references apirec_usuarios(id_usuario)
)
comment = 'permiso granular en que recibos opera el usuario';

alter table apirec_usuarios_permisos drop index if exists idx_cod_modulo;
create index idx_cod_modulo on apirec_usuarios_permisos(cod_modulo);

-- -----------------------------------------------------
-- Table apirec_modulos
-- -----------------------------------------------------
drop table if exists apirec_modulos cascade;

create table if not exists apirec_modulos (
	cod_modulo 			varchar(80) not null 							comment 'ambito de aplicabilidad de permiso',
	cod_recibo 			varchar(80) not null 							comment 'a que recibo aplica este modulo',
	primary key (cod_modulo, cod_recibo)
);
-- engine = innodb;

alter table apirec_modulos drop index if exists idx_cod_modulo; 
alter table apirec_modulos drop index if exists idx_cod_recibo;

create index idx_cod_modulo on apirec_modulos(cod_modulo);
create index idx_cod_recibo on apirec_modulos(cod_recibo);
