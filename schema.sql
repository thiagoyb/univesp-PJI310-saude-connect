CREATE TABLE sci_enderecos(
  codEnd int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  cep char(8) DEFAULT NULL,
  rua varchar(150) DEFAULT NULL,
  num char(5) DEFAULT NULL,
  bairro varchar(150) DEFAULT NULL,
  complemento varchar(150) DEFAULT NULL,
  zona ENUM('urbana','rural') NOT NULL DEFAULT 'urbana',
  moradia ENUM('casa','apartamento','condominio','sitio','barraco','quartel','abrigo') DEFAULT NULL,
  condicao ENUM('proprio','financiado','alugado','arrendado','cedido','ocupacao','desabrigado') DEFAULT NULL,
  data_criada datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE sci_posto_saude(
	codPosto INTEGER PRIMARY KEY AUTO_INCREMENT,
	nome varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	fkEndereco INTEGER NOT NULL,
	CONSTRAINT fk_ubs_end FOREIGN KEY (fkEndereco) REFERENCES sci_enderecos (codEnd)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE sci_agente_saude(
  codUser int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  nome varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  cpf BIGINT(11) UNSIGNED ZEROFILL COLLATE utf8_unicode_ci NOT NULL UNIQUE,
  senha varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  email varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  perfil ENUM('ACS','ACE','BOSS') NOT NULL DEFAULT 'ACS',
  fkPosto INTEGER NOT NULL,
  ativado tinyint(1) NOT NULL DEFAULT 1,
  data_cadastro DATETIME NOT NULL DEFAULT current_timestamp(),
  CONSTRAINT fk_acs_ubs FOREIGN KEY (fkPosto) REFERENCES sci_posto_saude (codPosto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE sci_pacientes(
	codPac INTEGER PRIMARY KEY AUTO_INCREMENT,
	nome varchar(100) COLLATE utf8_unicode_ci NOT NULL,
    cpf BIGINT(11) UNSIGNED ZEROFILL COLLATE utf8_unicode_ci NOT NULL UNIQUE,
    senha varchar(32) COLLATE utf8_unicode_ci NOT NULL,
	email varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	celular varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
	data_nasc DATE DEFAULT NULL,
	sexo ENUM('M','F') DEFAULT NULL,
	cns varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
	pcd BOOLEAN NOT NULL DEFAULT 0,
	fkEndereco INTEGER DEFAULT NULL,
	estado_civil ENUM('SOLTEIRO','CASADO','DIVORCIADO','SEPARADO','VIUVO','DESQUITADO','AMASIADO') DEFAULT NULL,
	escolaridade ENUM('analfabeto','ensino_fundamental_incompleto','ensino_fundamental_completo','ensino_medio_incompleto','ensino_medio_completo','ensino_superior_incompleto','ensino_superior_completo','especializacao','mestrado','doutorado','pos_doutorado') DEFAULT NULL,
	profissao varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    data_cadastro DATETIME NOT NULL DEFAULT current_timestamp(),
    data_update timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	CONSTRAINT fk_pac_end FOREIGN KEY (fkEndereco) REFERENCES sci_enderecos (codEnd)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE sci_comorbidades(
	codCom INTEGER PRIMARY KEY AUTO_INCREMENT,
	comorbidade varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	visivel BOOLEAN NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO sci_comorbidades (comorbidade) VALUES ('Doença cardiovascular, incluindo hipertensão'),('Diabetes'),('Doença hepática'),('Doença neurológica crônica ou neuromuscular'),('Imunodeficiência'),('Infecção pelo HIV'),('Doença renal'),('Doença pulmonar crônica'),('Neoplasia (tumor sólido ou hematológico)'),('Obesidade'),('Doença cardíaca'),('Doença imunossupressora ou autoimune'),('Doença respiratória'),('Doença cromossômica');

CREATE TABLE sci_sintomas(
	codSin INTEGER PRIMARY KEY AUTO_INCREMENT,
	sintoma varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	visivel BOOLEAN NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO sci_sintomas (sintoma) VALUES ('Febre'),('Tosse'),('Dor de garganta'),('Dificuldade de respirar'),('Mialgia/artralgia'),('Diarreia'),('Náusea/vômitos'),('Cefaleia (dor de cabeça)'),('Coriza'),('Irritabilidade/confusão'),('Adinamia (fraqueza)'),('Produção de escarro (Catarro)'),('Calafrios'),('Congestão nasal'),('Congestão conjuntival'),('Dificuldade para deglutir'),('Manchas vermelhas pelo corpo'),('Gânglios linfáticos aumentados'),('Batimento das asas nasais'),('Saturação de O2 < 95%'),('Sinais de cianose'),('Tiragem intercostal'),('Dispneia (dificuldade de respirar)'),('Outros'),('Perda de Paladar'),('Perda de Olfato'),('Dores no Corpo');

CREATE TABLE sci_parentesco(
	codPar INTEGER PRIMARY KEY AUTO_INCREMENT,
	relacao varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO sci_parentesco (relacao) VALUES ('Amigo(a)'),('Cônjuge/ Namorado(a)'),('Sogro(a)'),('Genro/Nora'),('Cunhado(a)'),('Pai'),('Mãe'),('Irmão(a)'),('Filho(a)'),('Padrasto'),('Madrasta'),('Enteado(a)'),('Tio(a)'),('Primo(a)'),('Sobrinho(a)'),('Avô'),('Avó'),('Neto(a)');

CREATE TABLE sci_visitas(
	codVis INTEGER PRIMARY KEY AUTO_INCREMENT,
	status ENUM('CRIADA','VISITADA','AUSENTE','CANCELADA') NOT NULL DEFAULT 'CRIADA',
	data_visita DATETIME NOT NULL,
	fkPaciente INTEGER NOT NULL,
	fkAgente INTEGER NOT NULL COMMENT 'cadastrou',
    data_cadastro DATETIME NOT NULL DEFAULT current_timestamp(),
	CONSTRAINT fk_vis_pac FOREIGN KEY (fkPaciente) REFERENCES sci_pacientes (codPac),
	CONSTRAINT fk_vis_usr FOREIGN KEY (fkAgente) REFERENCES sci_agente_saude (codUser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE sci_entrevistas(
	codEnt INTEGER PRIMARY KEY AUTO_INCREMENT,
	fkVisita INTEGER NOT NULL,
	fkAgente INTEGER NOT NULL COMMENT 'entrevistou',
	sintomas varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'json',
	comorbidades varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'json',

    data_cadastro DATETIME NOT NULL DEFAULT current_timestamp(),
	CONSTRAINT fk_ent_vis FOREIGN KEY (fkVisita) REFERENCES sci_visitas (codVis),
	CONSTRAINT fk_ent_usr FOREIGN KEY (fkAgente) REFERENCES sci_agente_saude (codUser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE sci_familia(
	codFam INTEGER PRIMARY KEY AUTO_INCREMENT,
	fkEntrevista INTEGER DEFAULT NULL,
	fkEndereco INTEGER NOT NULL,
	fkPaciente INTEGER NOT NULL,
	fkParentesco INTEGER NOT NULL,
	nome varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	data_nasc DATE DEFAULT NULL,
	sexo ENUM('M','F') DEFAULT NULL,
	cns varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
	pcd BOOLEAN NOT NULL DEFAULT 0,
	profissao varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
    data_cadastro DATETIME NOT NULL DEFAULT current_timestamp(),
	CONSTRAINT fk_fam_ent FOREIGN KEY (fkEntrevista) REFERENCES sci_entrevistas (codEnt),
	CONSTRAINT fk_fam_end FOREIGN KEY (fkEndereco) REFERENCES sci_enderecos (codEnd),
	CONSTRAINT fk_fam_pac FOREIGN KEY (fkPaciente) REFERENCES sci_pacientes (codPac),
	CONSTRAINT fk_fam_par FOREIGN KEY (fkParentesco) REFERENCES sci_parentesco (codPar)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE VIEW sci_users AS 
(SELECT codUser as id, nome, cpf, senha, email, perfil, ativado, data_cadastro FROM sci_agente_saude)
UNION
(SELECT codPac as id, nome, cpf, senha, email, 'PAC' as perfil, 1 as ativado, data_cadastro FROM sci_pacientes);