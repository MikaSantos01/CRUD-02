CREATE DATABASE IF NOT EXISTS clinica;
USE clinica;

-- Tabela Médico
CREATE TABLE medico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    especialidade VARCHAR(100) NOT NULL
);

-- Tabela Paciente
CREATE TABLE paciente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    tipo_sanguineo VARCHAR(3) NOT NULL
);

-- Tabela Consulta (relacionamento N:N)
CREATE TABLE consulta (
    id_medico INT NOT NULL,
    id_paciente INT NOT NULL,
    data_hora DATETIME NOT NULL,
    observacoes TEXT,
    PRIMARY KEY (id_medico, id_paciente, data_hora),
    FOREIGN KEY (id_medico) REFERENCES medico(id) ON DELETE CASCADE,
    FOREIGN KEY (id_paciente) REFERENCES paciente(id) ON DELETE CASCADE
);
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    path VARCHAR(255) NOT NULL
);
-- Adicionar a chave estrangeira na tabela alunos
ALTER TABLE paciente
ADD COLUMN imagem_id INT,
ADD FOREIGN KEY (imagem_id) REFERENCES imagens(id) ON DELETE SET NULL;