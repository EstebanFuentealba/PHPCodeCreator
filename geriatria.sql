## MYSQL Demo SQL Script
drop database geriatria;
create database geriatria;
use geriatria;



drop table if exists TBL_CESFAM;

drop table if exists TBL_ENCUESTA;

drop table if exists TBL_INGRESO;

drop table if exists TBL_MEDICAMENTOS;

drop table if exists TBL_PACIENTE;

drop table if exists TBL_PACIENTE_ENCUESTA;

drop table if exists TBL_PACIENTE_PATOLOGIA;

drop table if exists TBL_PATOLOGIA;

drop table if exists TBL_PERSONA;

drop table if exists TBL_SALA;

drop table if exists TBL_STEP;

drop table if exists TBL_TIPO_ENCUESTA;

/*==============================================================*/
/* Table: TBL_CESFAM                                            */
/*==============================================================*/
create table TBL_CESFAM
(
   ID_CESFAM            int not null auto_increment,
   NOMBRE               varchar(20),
   primary key (ID_CESFAM)
);

/*==============================================================*/
/* Table: TBL_ENCUESTA                                          */
/*==============================================================*/
create table TBL_ENCUESTA
(
   ID_ENCUESTA          int not null auto_increment,
   ID_STEP              int,
   ID_TIPO_ENCUESTA     int,
   NOMBRE               varchar(30),
   primary key (ID_ENCUESTA)
);

/*==============================================================*/
/* Table: TBL_INGRESO                                           */
/*==============================================================*/
create table TBL_INGRESO
(
   ID_INGRESO           int not null auto_increment,
   RUT_CUIDADOR         varchar(12),
   ID_SALA              int,
   ID_CESFAM            int,
   RUT_PACIENTE         varchar(12),
   FECHA_INGRESO        datetime,
   CAMA                 int,
   DISGNOSTICO_INGRESO  text,
   OBSERVACIONES        text,
   PLAN_DE_CUIDADOS     text,
   primary key (ID_INGRESO)
);

/*==============================================================*/
/* Table: TBL_MEDICAMENTOS                                      */
/*==============================================================*/
create table TBL_MEDICAMENTOS
(
   ID_MEDICAMENTO       int not null auto_increment,
   NOMBRE               varchar(20),
   DESCRIPCION          varchar(100),
   primary key (ID_MEDICAMENTO)
);

/*==============================================================*/
/* Table: TBL_PACIENTE                                          */
/*==============================================================*/
create table TBL_PACIENTE
(
   RUT                  varchar(12) not null,
   HISTORIA_CLINICA     varchar(20),
   RIESGO_SOCIAL        boolean,
   primary key (RUT)
);

/*==============================================================*/
/* Table: TBL_PACIENTE_ENCUESTA                                 */
/*==============================================================*/
create table TBL_PACIENTE_ENCUESTA
(
   ID_PACIENTE_ENCUESTA int not null auto_increment,
   ID_ENCUESTA          int,
   ID_INGRESO           int,
   ESTADO               boolean,
   primary key (ID_PACIENTE_ENCUESTA)
);

/*==============================================================*/
/* Table: TBL_PACIENTE_PATOLOGIA                                */
/*==============================================================*/
create table TBL_PACIENTE_PATOLOGIA
(
   ID_PATOLOGIA         int,
   RUT                  varchar(12),
   ID_MEDICAMENTO       int,
   DOSIS_MEDICAMENTO    varchar(100)
);

/*==============================================================*/
/* Table: TBL_PATOLOGIA                                         */
/*==============================================================*/
create table TBL_PATOLOGIA
(
   ID_PATOLOGIA         int not null auto_increment,
   NOMBRE               varchar(30),
   DESCRIPCION          varchar(250),
   primary key (ID_PATOLOGIA)
);

/*==============================================================*/
/* Table: TBL_PERSONA                                           */
/*==============================================================*/
create table TBL_PERSONA
(
   RUT                  varchar(12) not null,
   NOMBRES              varchar(30),
   APELLIDO_PATERNO     varchar(20),
   APELLIDO_MATERNO     varchar(20),
   FECHA_NACIMIENTO     datetime,
   SEXO                 char(1),
   primary key (RUT)
);

/*==============================================================*/
/* Table: TBL_SALA                                              */
/*==============================================================*/
create table TBL_SALA
(
   ID_SALA              int not null auto_increment,
   NOMBRE               varchar(20),
   primary key (ID_SALA)
);

/*==============================================================*/
/* Table: TBL_STEP                                              */
/*==============================================================*/
create table TBL_STEP
(
   ID_STEP              int not null auto_increment,
   NOMBRE               varchar(30),
   primary key (ID_STEP)
);

/*==============================================================*/
/* Table: TBL_TIPO_ENCUESTA                                     */
/*==============================================================*/
create table TBL_TIPO_ENCUESTA
(
   ID_TIPO_ENCUESTA     int not null auto_increment,
   NOMBRE               varchar(20),
   primary key (ID_TIPO_ENCUESTA)
);

alter table TBL_ENCUESTA add constraint FK_REFERENCE_13 foreign key (ID_STEP)
      references TBL_STEP (ID_STEP) on delete restrict on update restrict;

alter table TBL_ENCUESTA add constraint FK_REFERENCE_15 foreign key (ID_TIPO_ENCUESTA)
      references TBL_TIPO_ENCUESTA (ID_TIPO_ENCUESTA) on delete restrict on update restrict;

alter table TBL_INGRESO add constraint FK_REFERENCE_2 foreign key (RUT_CUIDADOR)
      references TBL_PERSONA (RUT) on delete restrict on update restrict;

alter table TBL_INGRESO add constraint FK_REFERENCE_4 foreign key (ID_SALA)
      references TBL_SALA (ID_SALA) on delete restrict on update restrict;

alter table TBL_INGRESO add constraint FK_REFERENCE_5 foreign key (ID_CESFAM)
      references TBL_CESFAM (ID_CESFAM) on delete restrict on update restrict;

alter table TBL_INGRESO add constraint FK_REFERENCE_6 foreign key (RUT_PACIENTE)
      references TBL_PACIENTE (RUT) on delete restrict on update restrict;

alter table TBL_PACIENTE add constraint FK_REFERENCE_1 foreign key (RUT)
      references TBL_PERSONA (RUT) on delete restrict on update restrict;

alter table TBL_PACIENTE_ENCUESTA add constraint FK_REFERENCE_12 foreign key (ID_INGRESO)
      references TBL_INGRESO (ID_INGRESO) on delete restrict on update restrict;

alter table TBL_PACIENTE_ENCUESTA add constraint FK_REFERENCE_14 foreign key (ID_ENCUESTA)
      references TBL_ENCUESTA (ID_ENCUESTA) on delete restrict on update restrict;

alter table TBL_PACIENTE_PATOLOGIA add constraint FK_REFERENCE_7 foreign key (ID_PATOLOGIA)
      references TBL_PATOLOGIA (ID_PATOLOGIA) on delete restrict on update restrict;

alter table TBL_PACIENTE_PATOLOGIA add constraint FK_REFERENCE_8 foreign key (RUT)
      references TBL_PACIENTE (RUT) on delete restrict on update restrict;

alter table TBL_PACIENTE_PATOLOGIA add constraint FK_REFERENCE_9 foreign key (ID_MEDICAMENTO)
      references TBL_MEDICAMENTOS (ID_MEDICAMENTO) on delete restrict on update restrict;


	  
/* STEP */ 
INSERT INTO tbl_persona (RUT, NOMBRES, APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO)
VALUES ('11111111-1','Esteban', 'Fuentealba','','0000-00-00','1');
INSERT INTO tbl_persona (RUT, NOMBRES, APELLIDO_PATERNO, APELLIDO_MATERNO, FECHA_NACIMIENTO, SEXO)
VALUES ('22222222-2','Juan', 'Perez','','1987-02-05','1');

INSERT INTO TBL_PACIENTE (RUT,HISTORIA_CLINICA,RIESGO_SOCIAL) 
VALUES ('11111111-1','00002',TRUE);
INSERT INTO TBL_PACIENTE (RUT,HISTORIA_CLINICA,RIESGO_SOCIAL) 
VALUES ('22222222-2','00003',FALSE);

INSERT INTO tbl_step VALUES(null,'PROBLEMAS GERIATRICOS ACTUALES');
INSERT INTO tbl_step VALUES(null,'EVALUACI�N FUNCIONAL ACTIVIDADES DE LA VIDA DIARIA');
INSERT INTO tbl_step VALUES(null,'EVALUACI�N SOCIAL');
/* tbl_tipo_encuesta */
INSERT INTO tbl_tipo_encuesta VALUES(null,'General');
INSERT INTO tbl_tipo_encuesta VALUES(null,'Evaluaci�n nutricional');
INSERT INTO tbl_tipo_encuesta VALUES(null,'Evaluaci�n bucal');
INSERT INTO tbl_tipo_encuesta VALUES(null,'Situaci�n Familiar');
INSERT INTO tbl_tipo_encuesta VALUES(null,'Unidad de Convivencia');
INSERT INTO tbl_tipo_encuesta VALUES(null,'Situaci�n Econ�mica');
INSERT INTO tbl_tipo_encuesta VALUES(null,'Entorno Social');
INSERT INTO tbl_tipo_encuesta VALUES(null,'Redes apoyo');
/* */
INSERT INTO tbl_encuesta  VALUES(null,1,1,'Intelecto menoscabado');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'Incapacidad sensorial auditiva');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'Incapacidad sensorial visual');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'Iatrogenia');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'Inestabilidad ');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'Inmovilismo');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'incontinencia urinaria');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'incontinencia fecal');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'Irritabilidad colon');
INSERT INTO tbl_encuesta  VALUES(null,1,1,'�lceras por presi�n');

INSERT INTO tbl_encuesta  VALUES(null,1,2,'Desnutrici�n (IMC<24)');
INSERT INTO tbl_encuesta  VALUES(null,1,2,'Eutr�fico (IMC 24 -26,9)');
INSERT INTO tbl_encuesta  VALUES(null,1,2,'Sobrepeso (IMC 27 -29,9)');
INSERT INTO tbl_encuesta  VALUES(null,1,2,'Obesidad	(IMC 30 y m�s)');

INSERT INTO tbl_encuesta  VALUES(null,1,3,'Dentici�n completa');
INSERT INTO tbl_encuesta  VALUES(null,1,3,'Dentici�n parcial');
INSERT INTO tbl_encuesta  VALUES(null,1,3,'Portador de pr�tesis');
INSERT INTO tbl_encuesta  VALUES(null,1,3,'Desdentado');

INSERT INTO tbl_encuesta  VALUES(null,3,4,'Soltero');
INSERT INTO tbl_encuesta  VALUES(null,3,4,'Viudo');
INSERT INTO tbl_encuesta  VALUES(null,3,4,'Divorciado/separado');
INSERT INTO tbl_encuesta  VALUES(null,3,4,'Casado');
INSERT INTO tbl_encuesta  VALUES(null,3,5,'Solo');
INSERT INTO tbl_encuesta  VALUES(null,3,5,'Solo c/hijos');
INSERT INTO tbl_encuesta  VALUES(null,3,5,'Pareja > 60 a�os y/o con incapacidad');
INSERT INTO tbl_encuesta  VALUES(null,3,5,'Otros');
INSERT INTO tbl_encuesta  VALUES(null,3,6,'Con ingreso');
INSERT INTO tbl_encuesta  VALUES(null,3,6,'Sin ingreso');
INSERT INTO tbl_encuesta  VALUES(null,3,7,'Institucionalizado');
INSERT INTO tbl_encuesta  VALUES(null,3,7,'Vivienda propia');
INSERT INTO tbl_encuesta  VALUES(null,3,7,'Vivienda arrendada');
INSERT INTO tbl_encuesta  VALUES(null,3,7,'Pieza');
INSERT INTO tbl_encuesta  VALUES(null,3,7,'Mediagua');
INSERT INTO tbl_encuesta  VALUES(null,3,8,'Familiar permanente');
INSERT INTO tbl_encuesta  VALUES(null,3,8,'Familiar ocasional');
INSERT INTO tbl_encuesta  VALUES(null,3,8,'No Familiar permanente');
INSERT INTO tbl_encuesta  VALUES(null,3,8,'No Familiar ocasional');
INSERT INTO tbl_encuesta  VALUES(null,3,8,'No tiene apoyo');


