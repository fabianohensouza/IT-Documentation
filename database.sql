CREATE SCHEMA saic;

CREATE TABLE saic.antivirus ( 
	coop                 varchar(1)  NOT NULL    ,
	servidor             varchar(10)      ,
	url                  varchar(25)      ,
	versao               varchar(15)      ,
	abrangencia          varchar(5)      ,
	versao_console       varchar(5)      ,
	qtd_licencas         int      ,
	expiracao            date      ,
	usb_liberado         text      
 );

CREATE TABLE saic.aplicacoes ( 
	coop                 varchar(1)  NOT NULL    ,
	servidor             varchar(10)      ,
	nome                 varchar(25)      ,
	versao               varchar(15)      ,
	desenvolvedor        varchar(25)      ,
	tipo                 varchar(25)      ,
	endereco             varchar(50)      ,
	abrangencia          varchar(5)      ,
	qtd_licencas         int      ,
	expiracao            date      ,
	descricao            text      
 );

CREATE TABLE saic.backup ( 
	id_rotina            int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	coop                 varchar(1)      ,
	servidor             varchar(10)      ,
	sistema              varchar(25)      ,
	expiracao            date      ,
	midia                varchar(15)      ,
	guarda_interna       varchar(25)      ,
	guarda_externa       varchar(25)      ,
	rotina_diaria        text      ,
	rotina_semanal       text      ,
	rotina_mensal        text      
 ) engine=InnoDB;

CREATE TABLE saic.cfs ( 
	coop                 varchar(1)      ,
	qtd_licencas         int      ,
	expiracao            date      
 ) engine=InnoDB;

CREATE TABLE saic.cooperativa ( 
	codigo_coop          varchar(1)  NOT NULL    PRIMARY KEY,
	nome                 varchar(15)  NOT NULL    ,
	resp_ti              int      ,
	resp_ic              int      ,
	qtd_usuarios         int      ,
	qtd_equip            int      ,
	adesao               date      ,
	site_unificado       boolean      
 ) engine=InnoDB;

CREATE TABLE saic.dhcp ( 
	coop                 varchar(1)  NOT NULL    ,
	pa                   varchar(0)  NOT NULL    ,
	provedor             varchar(10)      ,
	`range`              varchar(10)      ,
	mascara              varchar(3)      ,
	gateway              varchar(3)      ,
	dns                  varchar(15)      ,
	reserva              boolean      ,
	filtro               boolean      
 ) engine=InnoDB;

CREATE TABLE saic.dominio ( 
	dominio_central      boolean  NOT NULL    ,
	nome                 varchar(10)  NOT NULL    ,
	coop                 varchar(1)  NOT NULL    ,
	dc_primario          varchar(10)      ,
	dc_secundario        varchar(10)      ,
	abrangencia          varchar(5)      ,
	observ               mediumtext      
 ) engine=InnoDB;

CREATE TABLE saic.modelos_hwsw ( 
	codigo_swhw          int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	tipo                 varchar(5)  NOT NULL    ,
	marca_modelo         varchar(15)  NOT NULL    
 ) engine=InnoDB;

CREATE TABLE saic.pa ( 
	codigo_pa            varchar(0)  NOT NULL    PRIMARY KEY,
	coop                 varchar(1)  NOT NULL    ,
	firewall             varchar(15)      ,
	link_x0              varchar(20)      ,
	link_x1              varchar(20)      ,
	link_x2              varchar(20)      ,
	link_x3              varchar(20)      ,
	link_x4              varchar(20)      ,
	link_x5              varchar(20)      
 ) engine=InnoDB;

CREATE TABLE saic.relatorios ( 
	id_relatorio         int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	cooperativa          varchar(1)  NOT NULL    ,
	status               varchar(5)      ,
	responsavel          varchar(15)      ,
	arquivo              varchar(64)      ,
	tipo                 varchar(5)      ,
	data_amissao         date      ,
	visita               boolean      ,
	id_visita            int      ,
	detalhes             mediumtext      
 );

CREATE TABLE saic.servidores ( 
	codigo_servidor      int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	coop_servidor        varchar(1)  NOT NULL    ,
	pa_servidor          varchar(0)  NOT NULL    ,
	nome                 varchar(10)  NOT NULL    ,
	tipo_servidor        varchar(2)      ,
	host_pai             varchar(10)      ,
	modelo_servidor      varchar(15)  NOT NULL    ,
	st_ns                varchar(10)      ,
	tipo_garantia        varchar(10)      ,
	data_garantia        date      ,
	status_servidor      varchar(5)  NOT NULL    ,
	so_servidor          varchar(15)  NOT NULL    ,
	cadastro_sgpi        boolean      ,
	discos               varchar(25)      ,
	volumes              varchar(25)      ,
	memoria              int  NOT NULL    ,
	processador          varchar(10)  NOT NULL    ,
	idrad                boolean  NOT NULL    ,
	ip_idrac             varchar(3)      ,
	ip_lan               varchar(3)      ,
	monitorado_zbx       boolean      ,
	ip_mult              varchar(3)      ,
	fnc_dc               boolean      ,
	fnc_dns              boolean      ,
	fnc_dhcp             boolean      ,
	fnc_wsus             boolean      ,
	fnc_web              boolean      ,
	fnc_av               boolean      ,
	fnc_fs               boolean      ,
	fnc_bkp              boolean      ,
	dominio_servidor     varchar(10)      ,
	observ               mediumtext      
 ) engine=InnoDB;

CREATE TABLE saic.usuarios ( 
	id_usuario           int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	permissao            varchar(3)  NOT NULL    ,
	nome                 varchar(25)  NOT NULL    ,
	login                varchar(7)  NOT NULL    ,
	email                varchar(15)      ,
	cooperativa          varchar(1)  NOT NULL    ,
	senha                varchar(8)  NOT NULL    ,
	equipe               varchar(5)      
 ) engine=InnoDB;

CREATE TABLE saic.visita ( 
	id_visita            int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	coop                 varchar(1)      ,
	status               varchar(5)      ,
	tipo                 varchar(5)      ,
	responsavel          varchar(15)      ,
	`data-ida`           date      ,
	`data-retorno`       date      ,
	detalhes             mediumtext      
 ) engine=InnoDB;

