CREATE SCHEMA documentacao;

CREATE TABLE documentacao.modelos_hwsw ( 
	codigo_swhw          int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	tipo                 varchar(20)  NOT NULL    ,
	marca_modelo         varchar(60)  NOT NULL    
 ) engine=InnoDB;

CREATE TABLE documentacao.antivirus ( 
	coop                 int  NOT NULL    ,
	servidor             varchar(40)      ,
	url                  varchar(100)      ,
	versao               varchar(60)      ,
	abrangencia          varchar(20)      ,
	versao_console       varchar(20)      ,
	qtd_licencas         int      ,
	expiracao            date      ,
	usb_liberado         text      
 );

CREATE TABLE documentacao.aplicacoes ( 
	coop                 int  NOT NULL    ,
	servidor             varchar(40)      ,
	nome                 varchar(100)      ,
	versao               varchar(60)      ,
	desenvolvedor        varchar(100)      ,
	tipo                 varchar(100)      ,
	endereco             varchar(200)      ,
	abrangencia          varchar(20)      ,
	qtd_licencas         int      ,
	expiracao            date      ,
	descricao            text      
 );

CREATE TABLE documentacao.backup ( 
	id_rotina            int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	coop                 int      ,
	servidor             varchar(40)      ,
	sistema              varchar(100)      ,
	expiracao            date      ,
	midia                varchar(60)      ,
	guarda_interna       varchar(100)      ,
	guarda_externa       varchar(100)      ,
	rotina_diaria        text      ,
	rotina_semanal       text      ,
	rotina_mensal        text      
 ) engine=InnoDB;

CREATE TABLE documentacao.cfs ( 
	coop                 int      ,
	qtd_licencas         int      ,
	expiracao            date      
 ) engine=InnoDB;

CREATE TABLE documentacao.cooperativa ( 
	codigo_coop          int  NOT NULL    PRIMARY KEY,
	nome                 varchar(60)  NOT NULL    ,
	resp_ti              int      ,
	resp_ic              int      ,
	qtd_usuarios         int      ,
	qtd_equip            int      
 ) engine=InnoDB;

CREATE TABLE documentacao.dhcp ( 
	coop                 int  NOT NULL    ,
	pa                   int  NOT NULL    ,
	provedor             varchar(40)      ,
	`range`              varchar(40)      ,
	mascara              varchar(15)      ,
	gateway              varchar(15)      ,
	dns                  varchar(60)      ,
	reserva              bool      ,
	filtro               bool      
 ) engine=InnoDB;

CREATE TABLE documentacao.dominio ( 
	dominio_central      bool  NOT NULL    ,
	nome                 varchar(40)  NOT NULL    ,
	coop                 int  NOT NULL    ,
	dc_primario          varchar(40)      ,
	dc_secundario        varchar(40)      ,
	abrangencia          varchar(20)      ,
	observ               mediumtext      
 ) engine=InnoDB;

CREATE TABLE documentacao.pa ( 
	codigo_pa            int  NOT NULL    PRIMARY KEY,
	coop                 int  NOT NULL    ,
	firewall             varchar(60)      ,
	link_x0              varchar(80)      ,
	link_x1              varchar(80)      ,
	link_x2              varchar(80)      ,
	link_x3              varchar(80)      ,
	link_x4              varchar(80)      ,
	link_x5              varchar(80)      
 ) engine=InnoDB;

CREATE TABLE documentacao.servidores ( 
	codigo_servidor      int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	coop_servidor        int  NOT NULL    ,
	pa_servidor          int  NOT NULL    ,
	nome                 varchar(40)  NOT NULL    ,
	tipo_servidor        varchar(10)      ,
	host_pai             varchar(40)      ,
	modelo_servidor      varchar(60)  NOT NULL    ,
	st_ns                varchar(40)      ,
	tipo_garantia        varchar(40)      ,
	data_garantia        date      ,
	status_servidor      varchar(20)  NOT NULL    ,
	so_servidor          varchar(60)  NOT NULL    ,
	cadastro_sgpi        bool      ,
	discos               varchar(100)      ,
	volumes              varchar(100)      ,
	memoria              int  NOT NULL    ,
	processador          varchar(40)  NOT NULL    ,
	idrad                bool  NOT NULL    ,
	ip_idrac             varchar(15)      ,
	ip_lan               varchar(15)      ,
	monitorado_zbx       bool      ,
	ip_mult              varchar(15)      ,
	fnc_dc               bool      ,
	fnc_dns              bool      ,
	fnc_dhcp             bool      ,
	fnc_wsus             bool      ,
	fnc_web              bool      ,
	fnc_av               bool      ,
	fnc_fs               bool      ,
	fnc_bkp              bool      ,
	dominio_servidor     varchar(40)      ,
	observ               mediumtext      
 ) engine=InnoDB;

CREATE TABLE documentacao.usuarios ( 
	id_usuario           int  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	permissao            varchar(15)  NOT NULL    ,
	nome                 varchar(100)  NOT NULL    ,
	email                varchar(60)      ,
	cooperativa          int  NOT NULL    ,
	senha                varchar(64)  NOT NULL    
 ) engine=InnoDB;

ALTER TABLE documentacao.antivirus ADD CONSTRAINT fk_antivirus_cooperativa FOREIGN KEY ( coop ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.aplicacoes ADD CONSTRAINT fk_antivirus_cooperativa_0 FOREIGN KEY ( coop ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.backup ADD CONSTRAINT fk_backup_cooperativa FOREIGN KEY ( coop ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.cfs ADD CONSTRAINT fk_cfs_cooperativa FOREIGN KEY ( coop ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.cooperativa ADD CONSTRAINT fk_cooperativa_respti FOREIGN KEY ( resp_ti ) REFERENCES documentacao.usuarios( id_usuario ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.cooperativa ADD CONSTRAINT fk_cooperativa_respic FOREIGN KEY ( resp_ic ) REFERENCES documentacao.usuarios( id_usuario ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.dhcp ADD CONSTRAINT fk_dhcp_cooperativa FOREIGN KEY ( coop ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.dhcp ADD CONSTRAINT fk_dhcp_pa FOREIGN KEY ( pa ) REFERENCES documentacao.pa( codigo_pa ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.dominio ADD CONSTRAINT fk_ad_cooperativa FOREIGN KEY ( coop ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.pa ADD CONSTRAINT fk_cooperativa FOREIGN KEY ( coop ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.servidores ADD CONSTRAINT fk_servidores_cooperativa FOREIGN KEY ( coop_servidor ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.servidores ADD CONSTRAINT fk_servidores_pa FOREIGN KEY ( pa_servidor ) REFERENCES documentacao.pa( codigo_pa ) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE documentacao.usuarios ADD CONSTRAINT fk_usuarios_cooperativa FOREIGN KEY ( cooperativa ) REFERENCES documentacao.cooperativa( codigo_coop ) ON DELETE NO ACTION ON UPDATE NO ACTION;

