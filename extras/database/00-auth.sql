/**!
 * @package   ReceiptAPI
 * @filename  00-auth.sql
 * @version   1.0
 * @autor     Díaz Urbaneja Víctor Eduardo Diex <diazvictor@tutamail.com>
 * @date      21.11.2023 17:05:17 -04
 */

drop table if exists users_profiles;
drop table if exists permissions;
drop table if exists profiles;
create table profiles (
	id_profile 	int not null auto_increment primary	key,
	profile 	varchar(32) not null,
	beging		time default current_time,
	ending		time default (current_time + interval 8 hour)
);

insert into profiles
	(profile, beging, ending)
values
	('admin', '00:00:00', '23:59:59'),
	('comprador', '00:00:00', '23:59:59'),
	('vendedor', '08:00:00', '16:59:59'),
	('contabilidad', '08:00:00', '16:59:59'),
	('gastos', '08:00:00', '16:59:59');

drop table if exists modules;
create table modules (
	id_module	int not null auto_increment primary	key,
	module 		varchar(32) not null,
	description	varchar(32) not null,
	unique 		(module)
);

insert into modules
	(module, description)
values
	('login', 'iniciar sesion'),
	('upload', 'cargar recibo'),
	('lists', 'listar recibos'),
	('search', 'buscar recibos'),
	('details', 'detalle recibo'),
	('change_pass', 'cambiar clave');
	-- ('marcar recibo'),
	-- ('cerrar recibo'),
	-- ('anular recibo'),

create table permissions (
	id_permission	int not null auto_increment primary key,
	id_profile		int not null,
	id_module		int not null,
	`read`			boolean not null default true,
	`write`			boolean not null default true,
	`update`		boolean not null default true,
	foreign key (id_profile) references profiles(id_profile),
	foreign key (id_module) references modules(id_module)
);

insert into permissions
	(id_profile, id_module, `read`, `write`, `update`)
values
	-- Admin
	(1, 1, true, false, false),
	(1, 2, true, true, true),
	(1, 3, true, true, true),
	(1, 4, true, true, true),
	(1, 5, true, true, true),
	(1, 6, true, true, true),
	-- Comprador
	(2, 1, true, false, false),
	(2, 2, false, false, false),
	(2, 3, true, false, false),
	(2, 4, true, false, false),
	(2, 5, true, false, false),
	(2, 6, true, true, true),
	-- Vendedor
	(3, 1, true, false, false),
	(3, 2, true, true, true),
	(3, 3, true, true, true),
	(3, 4, true, true, true),
	(3, 5, true, true, true),
	(3, 6, true, true, true),
	-- Contabilidad
	(4, 1, true, false, false),
	(4, 2, true, false, false),
	(4, 3, true, true, true),
	(4, 4, true, true, true),
	(4, 5, true, true, true),
	(4, 6, true, true, true),
	-- Gastos
	(5, 1, true, false, false),
	(5, 2, true, false, false),
	(5, 3, true, true, true),
	(5, 4, true, true, true),
	(5, 5, true, true, true),
	(5, 6, true, true, true);

-- ('login', 'iniciar sesion'),
-- ('upload', 'cargar recibo'),
-- ('list', 'listar recibos'),
-- ('search', 'buscar recibos'),
-- ('details', 'detalle recibo'),
-- ('change_pass', 'cambiar clave');

drop table if exists users;
create table users (
    id_user 	int not null auto_increment primary	key,
    username	varchar(16) not null,
    password	char(128) not null,
    type_user	enum('admin', 'comprador', 'vendedor', 'contabilidad', 'gastos'),
    status		boolean not null default true
);

insert into users
	(username, password, type_user)
values
	('vitronic', '$2y$10$JgaTqfeMUhkii.Sj9NBc8e0NjLeMwmLKAgqGwhiUiw1nK/e7E6VdC', 1),
	('diazvictor', '$2y$10$JgaTqfeMUhkii.Sj9NBc8e0NjLeMwmLKAgqGwhiUiw1nK/e7E6VdC', 1),
	('jhondoe', '$2y$10$JgaTqfeMUhkii.Sj9NBc8e0NjLeMwmLKAgqGwhiUiw1nK/e7E6VdC', 2),
	('janedoe', '$2y$10$JgaTqfeMUhkii.Sj9NBc8e0NjLeMwmLKAgqGwhiUiw1nK/e7E6VdC', 3),
	('ezequiel', '$2y$10$JgaTqfeMUhkii.Sj9NBc8e0NjLeMwmLKAgqGwhiUiw1nK/e7E6VdC', 4),
	('tony', '$2y$10$JgaTqfeMUhkii.Sj9NBc8e0NjLeMwmLKAgqGwhiUiw1nK/e7E6VdC', 5);

create table users_profiles (
	id_user		int not null,
	id_profile	int not null,
	foreign key (id_user) references users(id_user),
	foreign key (id_profile) references profiles(id_profile)
);

insert into users_profiles
	(id_user, id_profile)
values
	(1, 1),
	(2, 1),
	(3, 2),
	(4, 3),
	(5, 4),
	(6, 5);
