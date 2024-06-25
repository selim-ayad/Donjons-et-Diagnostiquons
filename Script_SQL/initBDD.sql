CREATE TABLE Categorie (
  Id INT PRIMARY KEY AUTO_INCREMENT,
  Nom VARCHAR(255) NOT NULL
);

CREATE TABLE Sous_categorie (
  Id INT PRIMARY KEY AUTO_INCREMENT,
  Nom VARCHAR(255) NOT NULL,
  Description TEXT,
  Categorield INT,
  FOREIGN KEY (Categorield) REFERENCES Categorie(Id),
  INDEX idx_Sous_categorie_Categorield (Categorield)
);

CREATE TABLE Question (
  Id INT PRIMARY KEY AUTO_INCREMENT,
  Intitule VARCHAR(255) NOT NULL,
  Reponse0 VARCHAR(255),
  Reponse1 VARCHAR(255),
  Reponse2 VARCHAR(255),
  SousCategorield INT,
  FOREIGN KEY (SousCategorield) REFERENCES Sous_categorie(Id),
  INDEX idx_Question_SousCategorield (SousCategorield)
);

CREATE TABLE Entreprise (
  Id INT PRIMARY KEY AUTO_INCREMENT,
  Nom VARCHAR(255) NOT NULL,
  Email VARCHAR(255) NOT NULL,
  MDP VARCHAR(255) NOT NULL
);

CREATE TABLE ReponseEntreprise (
  Id INT PRIMARY KEY AUTO_INCREMENT,
  Valeur TINYINT NOT NULL,  -- valeur peut être 0, 1 ou 2
  IdEntrprise INT,
  IdQuestion INT,
  Justification TEXT,
  FOREIGN KEY (IdEntrprise) REFERENCES Entreprise(Id),
  FOREIGN KEY (IdQuestion) REFERENCES Question(Id),
  INDEX idx_ReponseEntreprise_IdEntrprise (IdEntrprise),
  INDEX idx_ReponseEntreprise_IdQuestion (IdQuestion)
);